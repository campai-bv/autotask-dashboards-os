<?php
	/**
	 * Copyright 2013, Campai Business Solutions B.V. (http://www.campai.nl)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright 2013, Campai Business Solutions B.V. (http://www.campai.nl)
	 * @link          http://autotask.campai.nl
	 * @license       MIT License (http://opensource.org/licenses/mit-license.php)
	 * @author        Coen Coppens <coen@campai.nl>
	 */

//App::import('Vendor', 'Autotask.atws', true, array(), 'atws'.DA.'php-atws.php');
require_once(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'Vendor'.DIRECTORY_SEPARATOR.'atws'.DIRECTORY_SEPARATOR.'php-atws.php');

	class ImportFromAutotaskShell extends AppShell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Resource'
			,	'Autotask.Ticketstatus'
			,	'Autotask.Queue'
			,	'Autotask.Account'
			,	'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
		);

		public $tasks = array(
				'Autotask.TimeConverter'
			,	'Autotask.GetTicketsCompletedToday'
			,	'Autotask.GetTicketsOpenToday'
			,	'Autotask.CalculateTotalsByTicketStatus'
			,	'Autotask.CalculateTotalsForKillRate'
			,	'Autotask.CalculateTotalsForQueueHealth'
			,	'Autotask.CalculateTotalsForTimeEntries'
		);


		public function log($sMessage,$iLevel = 0) {
			if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
				$this->iLogLevel = 0;
				parent::log('log level set to:'.$this->iLogLevel,'cronjob');
			}			
			if( $iLevel <= $this->iLogLevel ) {
				parent::log($sMessage, 'cronjob');	
			}
		}
		public function main() {
			
			$bErrorsEncountered = false;
			$this->connectAutotask();
			$this->checkConnectAutotask();

			$this->log( 'Starting with the import.' );


			// First we must make sure we can login. 
			//We do this by performing an inexpensive call and see what it returns.
			if( false === $this->connectAutotask() ) {
				$this->log('Failed to connect to autotask');
				return;
			} 

			// Apparently we can login, so let's get into action!
			// may as well do these first so there are none missing
			// sync picklists
			$this->__syncPicklistsWithDatabase();
			// Delete any existing records so we have a clean start.
			$this->log( '> Truncating tickets table..',1 );

			$this->Ticket->query('TRUNCATE TABLE tickets;');

			$this->log(  ' ..done.',1);
			// End

			// Import completed tickets
			$this->log(  '> Importing completed tickets (today) into the database..',1 );

			$oTickets = $this->GetTicketsCompletedToday->execute();

			if( empty( $oTickets ) ) {

				$this->log( ' ..nothing todo - api query returned no tickets completed today.',1 );

			} else {

				if( !$this->__saveTicketsToDatabase( $oTickets ) ) {

					$bErrorsEncountered = true;

				} else {

					$this->log(  ' ..imported ' . count( $oTickets ) . ' ticket(s).' ,1);

				}

			}
			// End

			if( !$bErrorsEncountered ) {

				// Import the tickets that have any other status then 'completed'.
				$this->log(  '> Importing open tickets (today) into the database..',1);

				$oTickets = $this->GetTicketsOpenToday->execute();

				if( empty( $oTickets ) ) {

					$this->log( ' ..nothing saved - query returned no tickets.',1 );

				} else {

					if( !$this->__saveTicketsToDatabase( $oTickets ) ) {

						$bErrorsEncountered = true;

					} else {

							$this->log(  ' ..imported ' . count( $oTickets ) . ' ticket(s).' ,1);

					}

				}

				if( !$bErrorsEncountered ) {

					// Processing of the tickets data into totals for kill rates, queue healths etc.
					$this->log('> Calculating ticket status totals for all dashboards..',1 );

					$this->CalculateTotalsByTicketStatus->execute();

					$this->log( ' ..done.',1 );
					$this->log( '> Calculating kill rate totals for all dashboards..' ,1);

					$this->CalculateTotalsForKillRate->execute();

					$this->log( ' ..done.',1 );
					$this->log( '> Calculating queue health totals for all dashboards..',1 );

					$this->CalculateTotalsForQueueHealth->execute();

					$this->log( ' ..done.' ,1);

					$this->log( '> Importing time entries..',1 );

					if( !$this->CalculateTotalsForTimeEntries->execute() ) {
						$bErrorsEncountered = true;
					}

					$this->log(  ' ..done.',1 );

					$this->log( '> Clearing cache for all dashboards..',1 );


					if(
						clearCache() // Clear the view cache
						&&
						Cache::clear( null ,'1_hour' ) // Clear the model cache
					) {

						$this->log(  ' ..done.',1 );

					} else {

						$bErrorsEncountered = true;
						$this->log( ' ..could not delete view cache!' );

					}

				}

			}


			// End

			if( $bErrorsEncountered ) {
				$this->log( 'Failed: we\'ve encountered some errors while running the import script.' );
			} else {

				$this->log( 'Success: everything imported correctly.' );

			}

		}

		private function __syncPicklistsWithDatabase( ) {
			$aIssueTypes = $this->getAutotaskPicklist( 'Ticket', 'IssueType' );
			$aSubissueTypes = $this->getAutotaskPicklist('Ticket','SubIssueType');
			$aQueues = $this->getAutotaskPicklist('Ticket','QueueID');
			$aTicketstatus = $this->getAutotaskPicklist('Ticket','Status');
			
			$this->__savePicklistToModel('Issuetype',$aIssueTypes);
			$this->__savePicklistToModel('Subissuetype',$aSubissueTypes);
			$this->__savePicklistToModel('Queue',$aQueues);
			$this->__savePicklistToModel('Ticketstatus',$aTicketstatus);

		}
		private function __savePicklistToModel($sModel,$aPicklist) {
			if(!is_array($aPicklist)) {
				return false;
			}
			$aNewModelRecords = array();
			foreach ($aPicklist as $iId=>$sName) {
				$this->log('checking model:'.$sModel.' for name:'.$sName,4);
				$aModelRecord = $this->$sModel->findByid($iId);
				if (empty($aModelRecord)) {
					$this->log('non existing:'.$sModel.' model so inserting:'.$sName.' with id:'.$iId,3);
					$aNewModelRecords[] = array($sModel=>array('id'=>$iId,'name'=>$sName));
				}
				else {
					if (empty($aModelRecord[$sModel]['name'])) {
						$this->log('updating '.$sModel.' with id:'.$iId.' which does not have a name. New name:'.$sName,3);
						$aNewModelRecords[]=array($sModel=>array('id'=>$iId,'name'=>$sName));
					}
					else {
						// allow dashboard settings to change name of picklist item.
						// set back to empty to resync on next cronjob run
						$this->log($sModel.':'.$sName.' exists and has name:'.$aModelRecord[$sModel]['name'],4);
					}
				}
			}
			
			if (!empty($aNewModelRecords)) {
				// batch write our model changes
				$this->$sModel->saveAll($aNewModelRecords);
			}

		}



		private function __saveTicketsToDatabase( $oTickets ) {

			if( !empty( $oTickets ) ) {

				// Gets filled up with ticket model data that should get executed.
				$aQueries = array();

				if( 1 == count( $oTickets ) ) {

					$this->__rebuildAPIResponseToSaveData( $oTickets, $aQueries, $aIds );

				} else {

					foreach ( $oTickets as $oTicket ) {
						$this->__rebuildAPIResponseToSaveData( $oTicket, $aQueries, $aIds );
					}

				}

				foreach ( $aQueries as $sModel => $sQuery ) {

					try {
						$this->{$sModel}->query( $sQuery );
					} catch ( Exception $e ) {

						$this->log( '- Could not save the new ' . Inflector::pluralize( $sModel ) . '. MySQL says: "' . $e->errorInfo[2] . '"' );
						$this->log( '- ' . $sQuery );
						return false;

					}

				}

				return true;

			}

		}

		/**
		 * 
		 * @param  string $sType  'open', 'waitingCustomer' 
		 * @param  array  $aQuery [description]
		 * 
		 * @return object
		 */
		public function findInAutotask( $sType = 'open', $aQuery = array() ) {

			switch ( $sType ) {

				case 'open':
					return $this->_findOpenInAutotask( $aQuery );
				break;

				case 'closed':
					return $this->_findClosedInAutotask( $aQuery );
				break;

				case 'waitingCustomer':
					return $this->_findWaitingCustomerInAutotask( $aQuery );
				break;

				default:
					return false;
				break;
			}

		}

		private function _findOpenInAutotask( $query ) {

			if (!is_a($query,'atws\atwsquery')) {
				$query = $this->oAutotask->getNewQuery();
				$query->FROM('Ticket')->WHERE('Status',$query->NotEqual,5);				
			}
			return $this->oAutotask->getQueryResults( $query );
		}
		
		private function _findStatusInAutotask( $status ) {
			
			$query = $this->oAutotask->getNewQuery();
			$query->FROM('Ticket')->WHERE('Status',$query->Equals,$status);				
			return $this->oAutotask->getQueryResults( $query );

		}

		private function _findWaitingCustomerInAutotask( Array $aQuery ) {

			if (!is_a($query,'atws\atwsquery')) {
				$query = $this->oAutotask->getNewQuery();
				$query->FROM('Ticket')->WHERE('Status',$query->Equals,7);				
			}
			return $this->oAutotask->getQueryResults( $query );

		}
		
		private function _findClosedInAutotask( Array $aQuery ) {

			$aConditions = array(
					'Equals' => array(
							'Status' => 5
					)
			);

			if( !empty( $aQuery['conditions'] ) ) {
				$aQuery['conditions'] = array_merge_recursive( $aQuery['conditions'], $aConditions );
			} else {
				$aQuery['conditions'] = $aConditions;
			}

			return $this->queryAutotask( 'Ticket', $aQuery );

		}

		/**
		 * Rebuilds the response from the Autotask API into a set of MySQL insert queries.
		 * 
		 * Makes things a bit faster since Cake is kinda slow when running thousands of
		 * Model->save() requests at once.
		 * 
		 * @param  object $oTicket - The ticket object we got from Autotask.
		 * @param  array $aQueries - The (referenced) array with queries we'll be executing later on.
		 * @param  array $aIds - The (referenced) array with the id's of any new records. Helps you prevent duplicate id's.
		 * @return -
		 */
		private function __rebuildTicketToModelDataArray( $oTicket, &$aQueries, &$aIds ) {

			// Defaults
			$sCompletedDate = '';
			$sDueDateTime = '';
			$iResourceId = 0;
			$iAccountId = 9999999999; // This ID gets used for your own company. Autotask uses 0 as ID, I use an actual ID ;-)
			$iIssueTypeId = 0;
			$iSubIssueTypeId = 0;
			$iQueueId = 0;
			$iHasMetSLA = 0;
			// End

			// Reformat the dates to your own timezone.
			// @todo: rewrite autotask objects to have dates.
			// 		  then getQueryResults function can convert date based on time zone so objects
			// 		  returned automatically have the right date
			$sCreateDate = $this->TimeConverter->convertToOwnTimezone( $oTicket->CreateDate );

			if( !empty( $oTicket->CompletedDate ) ) {
				$sCompletedDate = $this->TimeConverter->convertToOwnTimezone( $oTicket->CompletedDate );
			}

			if( !empty( $oTicket->DueDateTime ) ) {
				$sDueDateTime = $this->TimeConverter->convertToOwnTimezone( $oTicket->DueDateTime );
			}
			// End

			if( !empty( $oTicket->AssignedResourceID ) ) {
				$iResourceId = $oTicket->AssignedResourceID;
			}

			if( !empty( $oTicket->AccountID ) ) {
				$iAccountId = $oTicket->AccountID;
			}

			if( !empty( $oTicket->IssueType ) ) {
				$iIssueTypeId = $oTicket->IssueType;
			}

			if( !empty( $oTicket->SubIssueType ) ) {
				$iSubIssueTypeId = $oTicket->SubIssueType;
			}

			if( !empty( $oTicket->QueueID ) ) {
				$iQueueId = $oTicket->QueueID;
			}

			if( isset( $oTicket->ServiceLevelAgreementHasBeenMet ) ) {
				$iHasMetSLA = $oTicket->ServiceLevelAgreementHasBeenMet;
			}

			// All data is present, let's add it to the query
			if( empty( $aQueries['Ticket'] ) ) {
				$aQueries['Ticket'] = "INSERT INTO tickets (id, created, completed, number, title, ticketstatus_id, queue_id, resource_id, account_id, issuetype_id, subissuetype_id, due, priority, has_met_sla ) VALUES ";
			} else {
				$aQueries['Ticket'] .= ', ';
			}

			$aQueries['Ticket'] .= '(';
				$aQueries['Ticket'] .= $oTicket->id;
				$aQueries['Ticket'] .= ",'" . $sCreateDate . "'";
				$aQueries['Ticket'] .= ",'" . $sCompletedDate . "'";
				$aQueries['Ticket'] .= ",'" . $oTicket->TicketNumber . "'";
				$aQueries['Ticket'] .= ",'" . htmlspecialchars( $oTicket->Title, ENT_QUOTES ) . "'";
				$aQueries['Ticket'] .= ',' . $oTicket->Status;
				$aQueries['Ticket'] .= ',' . $iQueueId;
				$aQueries['Ticket'] .= ',' . $iResourceId;
				$aQueries['Ticket'] .= ',' . $iAccountId;
				$aQueries['Ticket'] .= ',' . $iIssueTypeId;
				$aQueries['Ticket'] .= ',' . $iSubIssueTypeId;
				$aQueries['Ticket'] .= ",'" . $sDueDateTime . "'";
				$aQueries['Ticket'] .= ',' . $oTicket->Priority;
				$aQueries['Ticket'] .= ',' . $iHasMetSLA;
			$aQueries['Ticket'] .= ')';
			// End

			// Save the resource (if new)
			if( !empty( $oTicket->AssignedResourceID ) ) {

				$aResource = $this->Resource->read( null, $iResourceId );

				if(
					empty( $aResource )
					&&
					!in_array( $oTicket->AssignedResourceID, $aIds['Resource'] )
				) {

					$oResource = $this->Resource->findInAutotask( 'all', array(
							'conditions' => array(
									'Equals' => array(
											'id' => $oTicket->AssignedResourceID
									)
							)
					) );

					$sResourceName = '';

					if( !empty( $oResource->FirstName ) ) {
						$sResourceName .= $oResource->FirstName . ' ';
					}

					if( !empty( $oResource->FirstName ) ) {
						$sResourceName .= $oResource->MiddleName . ' ';
					}

					if( !empty( $oResource->FirstName ) ) {
						$sResourceName .= $oResource->LastName;
					}
					

					if( empty( $aQueries['Resource'] ) ) {
						$aQueries['Resource'] = "INSERT INTO resources (id, name ) VALUES ";
					} else {
						$aQueries['Resource'] .= ', ';
					}

					$aQueries['Resource'] .= '(';
						$aQueries['Resource'] .= $iResourceId;
						$aQueries['Resource'] .= ",'" . $sResourceName . "'";
					$aQueries['Resource'] .= ')';

					$aIds['Resource'][] = $iResourceId;

					$this->log( '  - Found new Resource => Inserted into the database ("' . $sResourceName . '").' ,3);

				}

			}
			// End


			// Save the queue (if new)
			if( 0 != $iQueueId ) {

				$aQueue = $this->Queue->read( null, $iQueueId );

				if(
					empty( $aQueue )
					&&
					!in_array( $iQueueId, $aIds['Queue'] )
				) {

					if( empty( $aQueries['Queue'] ) ) {
						$aQueries['Queue'] = "INSERT INTO queues (id, name ) VALUES ";
					} else {
						$aQueries['Queue'] .= ', ';
					}

					$aQueries['Queue'] .= '(';
						$aQueries['Queue'] .= $iQueueId;
						$aQueries['Queue'] .= ",'Not yet specified'";
					$aQueries['Queue'] .= ')';

					$aIds['Queue'][] = $iQueueId;

					$this->log( '  - Found new Queue => Inserted into the database (id ' . $iQueueId . ').' ,3);

				}

			}
			// End

			// Save the ticketstatus (if new)
			$aTicketstatus = $this->Ticketstatus->read( null, $oTicket->Status );

			if(
				empty( $aTicketstatus )
				&&
				!in_array( $oTicket->Status, $aIds['Ticketstatus'] )
			) {

				if( empty( $aQueries['Ticketstatus'] ) ) {
					$aQueries['Ticketstatus'] = "INSERT INTO ticketstatuses (id, name ) VALUES ";
				} else {
					$aQueries['Ticketstatus'] .= ', ';
				}

				$aQueries['Ticketstatus'] .= '(';
					$aQueries['Ticketstatus'] .= $oTicket->Status;
					$aQueries['Ticketstatus'] .= ",'Not yet specified'";
				$aQueries['Ticketstatus'] .= ')';

				$aIds['Ticketstatus'][] = $oTicket->Status;

				$this->log('  - Found new Ticket Status => Inserted into the database (id ' . $oTicket->Status . ').' ,3);

			}
			// End
			
			// Save the account (if new)
			if( !empty( $oTicket->AccountID ) ) {

				$aAccount = $this->Account->read( null, $oTicket->AccountID );

				if(
					empty( $aAccount )
					&&
					!in_array( $oTicket->AccountID, $aIds['Account'] )
				) {

					$oAccount = $this->Account->findInAutotask( 'all', array(
							'conditions' => array(
									'Equals' => array(
											'id' => $oTicket->AccountID
									)
							)
					) );

					if( empty( $aQueries['Account'] ) ) {
						$aQueries['Account'] = "INSERT INTO accounts (id, name ) VALUES ";
					} else {
						$aQueries['Account'] .= ', ';
					}

					$aQueries['Account'] .= '(';
						$aQueries['Account'] .= $oTicket->AccountID;
						$aQueries['Account'] .= ",'" . $oAccount->AccountName . "'";
					$aQueries['Account'] .= ')';

					$aIds['Account'][] = $oTicket->AccountID;

					$this->log( '  - Found new Account => Inserted into the database ("' . $oAccount->AccountName . '").' ,3);

				}

			}
			// End

			// Save the issuetype (if new)
			if( !empty( $oTicket->IssueType ) ) {

				$aIssueType = $this->Issuetype->read( null, $oTicket->IssueType );

				if(
					empty( $aIssueType )
					&&
					!in_array( $oTicket->IssueType, $aIds['Issuetype'] )
				) {

					if( empty( $aQueries['Issuetype'] ) ) {
						$aQueries['Issuetype'] = "INSERT INTO issuetypes (id ) VALUES ";
					} else {
						$aQueries['Issuetype'] .= ', ';
					}

					$aQueries['Issuetype'] .= '(';
						$aQueries['Issuetype'] .= $oTicket->IssueType;
					$aQueries['Issuetype'] .= ')';

					$aIds['Issuetype'][] = $oTicket->IssueType;

						$this->log(  '  - Found new Issue Type => Inserted into the database (id ' . $oTicket->IssueType . ').',3 );

				}

			}
			// End

			// Save the subissuetype (if new)
			if( !empty( $oTicket->SubIssueType ) ) {

				$aSubIssueType = $this->Subissuetype->read( null, $oTicket->SubIssueType );

				if(
					empty( $aSubIssueType )
					&&
					!in_array( $oTicket->SubIssueType, $aIds['Subissuetype'] )
				) {

					if( empty( $aQueries['Subissuetype'] ) ) {
						$aQueries['Subissuetype'] = "INSERT INTO subissuetypes (id ) VALUES ";
					} else {
						$aQueries['Subissuetype'] .= ', ';
					}

					$aQueries['Subissuetype'] .= '(';
						$aQueries['Subissuetype'] .= $oTicket->SubIssueType;
					$aQueries['Subissuetype'] .= ')';

					$aIds['Subissuetype'][] = $oTicket->SubIssueType;

					$this->log( '  - Found new Sub Issue Type => Inserted into the database (id ' . $oTicket->SubIssueType . ').' ,3);

				}

			}
			// End

		}


// finished below


		public function getAutotaskPicklist( $sEntity, $sPicklist ) {
			if($this->connectAutotask() !== true ) {
				return false;
			}
			$aPicklistResult = $this->oAutotask->getPicklist($sEntity, $sPicklist);
			
			if(is_array($aPicklistResult)) {
				return $aPicklistResult;
			}
			else {
				$this->log('failed to get picklist:'.$sPicklist.' for entity:'.$sEntity);
				if(isset($this->oAutotask->last_picklist_fault)) {
					$this->log('soapfault:'.$this->oAutotask->last_picklist_fault);
				}
				return false;
			}
		}

		private function getAutotaskLogin() {
			
			$aLogin = array(
					'login' => Configure::read( 'Autotask.username' )
				,	'password' => Configure::read( 'Autotask.password' )
				,	'location' => Configure::read( 'Autotask.wsdl' )
			);

			if(
				empty( $aLogin['login'] )
				||
				empty( $aLogin['password'] )
				||
				empty( $aLogin['location'] )
			) {

				$this->log( 'I\'m not able to use the Autotask API if you don\'t provide your credentials (/var/www/app/Plugin/Autotask/Config/bootstrap.php).', 'cronjob' );
				$this->log( 'I\'m not able to use the Autotask API if you don\'t provide your credentials (/var/www/app/Plugin/Autotask/Config/bootstrap.php).', 'error' );
				return false;

			}
			return $aLogin;
			
		}
		public function checkConnectAutotask() {
			
			$oResponse = $this->oAutotask->client->getThresholdAndUsageInfo();
			if(empty($oResponse->getThresholdAndUsageInfoResult->EntityReturnInfoResults->EntityReturnInfo->Message)) {
				return false;
			}
			if(strpos($oResponse->getThresholdAndUsageInfoResult->EntityReturnInfoResults->EntityReturnInfo->Message, 'TimeframeOfLimitation')) {
				return true;
			}
			return false;
		}
		public function connectAutotask() {
			if(isset($this->oAutotask)) {
				if(is_object($this->oAutotask)) {
					return true;
				}
			}
			
			$aLogin = $this->getAutotaskLogin();
			if ($aLogin == false) { 
				return false;
			}
			if ( !extension_loaded( 'soap' ) ) {
				$this->log( 'SOAP is not available, unable to perform requests to the Autotask API.', 'cronjob' );
				$this->log( 'SOAP is not available, unable to perform requests to the Autotask API.', 'error' );
				exit();
			}
			// setup the atws object
			$this->oAutotask = new atws\atws();
			if ($this->oAutotask->connect($aLogin['location'],$aLogin['login'],$aLogin['password'])) {
				if ($this->checkConnectAutotask() === true) {
					return true;
				}
				else {
					unset($this->oAutotask);
					$this->log('autotask connected but simple check failed');
					return false;
				}				
			}
			else {
				$this->log('could not connect to autotask');
				if(isset($this->oAutotask->last_connect_fault)) {
					$this->log($this->oAutotask->last_connect_fault);
				}
			}

		}


	}
