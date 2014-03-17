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
	class ImportFromAutotaskShell extends AppShell {

		public $uses = array(
				'Autotask.AutotaskApi'
			,	'Autotask.Ticket'
			,	'Autotask.Resource'
			,	'Autotask.Ticketstatus'
			,	'Autotask.Ticketsource'
			,	'Autotask.Queue'
			,	'Autotask.Account'
			,	'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
		);

		public $tasks = array(
				'Autotask.TimeConverter'
			,	'Autotask.GetTicketsCompleted'
			,	'Autotask.GetTicketsOpen'
			,	'Autotask.CalculateTotalsByTicketStatus'
			,	'Autotask.CalculateTotalsByTicketSource'
			,	'Autotask.CalculateTotalsForKillRate'
			,	'Autotask.CalculateTotalsForQueueHealth'
			,	'Autotask.CalculateTotalsForTimeEntries'
			,	'Autotask.CalculateTotalsOpenTickets'
		);


		public function getOptionParser() {

			$parser = parent::getOptionParser();

			$parser->addOption('data_to_check', array(
					'short' => 'd'
				,	'help' => 'Only run logic concerning data of a certain type, like time entries. Useful for resolving problems in a specific part of the cronjob. Will recalculate the appropriate totals only.'
				,	'choices' => array(
							'open_tickets'
						,	'completed_tickets'
						,	'time_entries'
						,	'ticket_sources'
						,	'picklist'
					)
			))->addOption('full', array(
					'short' => 'f'
				,	'help' => 'Run the cronjob, taking into account the full history of all items. Careful: might take a long time to run.'
				,	'boolean' => true
			));

			return $parser;

		}


		public function log($sMessage, $iLevel = 0) {

			if (!$this->iLogLevel = Configure::read('Import.logLevel')) {
				$this->iLogLevel = 0;
			}

			if ($iLevel <= $this->iLogLevel) {

				// Auto indent the messages based on their level.
				$sIdentation = '';

				for( $i=2; $i <= $iLevel; $i++ ) {
					$sIdentation .= "   ";
				}

				parent::log($sIdentation . $sMessage, 'cronjob');

			}

		}


		public function main() {

			$bErrorsEncountered = false;

			$this->log('Starting with the import..', 1);

			// Set the database object so we can clean quotes from user input.
			$this->db = ConnectionManager::getDataSource('default');

			// First we must make sure we can login. We do this by performing an inexpensive call and see what it returns.
			if (false === $this->Ticket->connectAutotask()) {
				$bErrorsEncountered = true;

			// Appearantly we can login, so let's get into action!
			// @comment removing this indent.
			} else {

				// may as well do these first so there are none missing
				// sync issue types
				if ($this->dataIsNeededFor('picklist') || $this->params['full']) {
					$this->syncPicklistsWithDatabase();
				}

				try {

					$this->purgeDatabase();

					// This function has been left out of the GetTicketsCompletedTask to allow
					// it to use the saveResponseToDatabase function.
					$this->importCompletedTickets();

					// This function has been left out of the GetTicketsOpenTask to allow
					// it to use the saveResponseToDatabase function.
					$this->importOpenTickets();

					$this->calculateTotals();
					$this->clearCache();

				} catch(Exception $e) {
					$this->log('Failed: we\'ve encountered some errors while running the import script. Erorr thrown: ' . $e->getMessage(), 1);
				}

			}
			// End

			$this->log( 'Success! Everything imported correctly.', 1);

		}


		private function syncPicklistsWithDatabase() {

			$aIssueTypes = $this->Ticket->getAutotaskPicklist('Ticket', 'IssueType');
			$aSubissueTypes = $this->Ticket->getAutotaskPicklist('Ticket','SubIssueType');
			$aQueues = $this->Ticket->getAutotaskPicklist('Ticket','QueueID');
			$aTicketstatus = $this->Ticket->getAutotaskPicklist('Ticket','Status');
			$aTicketsource = $this->Ticket->getAutotaskPicklist('Ticket','Source');

			$this->savePicklistToModel('Issuetype',$aIssueTypes);
			$this->savePicklistToModel('Subissuetype',$aSubissueTypes);
			$this->savePicklistToModel('Queue',$aQueues);
			$this->savePicklistToModel('Ticketstatus',$aTicketstatus);
			$this->savePicklistToModel('Ticketsource',$aTicketsource);

		}


		private function savePicklistToModel($sModel, $aPicklist) {

			if (!is_array($aPicklist)) {
				return false;
			}

			$aNewModelRecords = array();

			foreach ($aPicklist as $iId => $sName) {

				$this->log('> Checking model: ' . $sModel . ' for name: ' . $sName . '..', 4);

				$this->$sModel->recursive = -1;
				$aModelRecord = $this->$sModel->findByid($iId);

				if (empty($aModelRecord)) {

					$this->log('..Non existing: ' . $sModel . ' model so inserting: "' . $sName . '" with id '. $iId , 4);
					$aNewModelRecords[] = array($sModel=>array('id'=>$iId,'name'=>$sName));

					if ($this->outputIsNeededFor('picklist')) {
						$this->out('Inserted new ' . $sModel . ' with name ' . $sName . '.', 1, Shell::QUIET);
					}

				} else {

					if (empty($aModelRecord[$sModel]['name'])) {

						$this->log('..Updating ' . $sModel . ' with id ' . $iId . ' which does not have a name. New name: "' . $sName . '"', 4);
						$aNewModelRecords[] = array($sModel => array('id' => $iId, 'name' => $sName));

						if ($this->outputIsNeededFor('picklist')) {
							$this->out('Updating ' . $sModel . ' with id ' . $iId . ' which does not have a name. New name: "' . $sName . '"', 1, Shell::QUIET);
						}

					} else {
						// allow dashboard settings to change name of picklist item.
						// set back to empty to resync on next cronjob run
						$this->log('..' . $sModel . ' "' . $sName . '" exists and has name "' . $aModelRecord[$sModel]['name'] . '"' , 4);

						if ($this->outputIsNeededFor('picklist')) {
							$this->out($sModel . ' "' . $sName . '" exists and has name "' . $aModelRecord[$sModel]['name'] . '"', 1, Shell::QUIET);
						}

					}

				}

			}

			if (!empty($aNewModelRecords)) {
				// batch write our model changes
				$this->$sModel->saveAll($aNewModelRecords);
			}

		}


		/**
		 * @todo rename this function
		 * @param  [type] $oTickets [description]
		 * @return [type]           [description]
		 */
		private function saveResponseToDatabase($oTickets) {

			if (!empty($oTickets)) {

				// Gets filled up with queries that should get executed.
				$aQueries = array(
						'Ticket' => ''
					,	'Account' => ''
					,	'Resource' => ''
					,	'Queue' => ''
					,	'Ticketstatus' => ''
					,	'Ticketsource' => ''
					,	'Issuetype' => ''
					,	'Subissuetype' => ''
				);

				// Gets filled up with the id's of all new records, to prevent duplicate ones.
				$aIds = array(
						'Ticket' => array()
					,	'Account' => array()
					,	'Resource' => array()
					,	'Queue' => array()
					,	'Ticketstatus' => array()
					,	'Issuetype' => array()
					,	'Subissuetype' => array()
					,	'Ticketsource' => array()
				);

				// Fills up the $aQueries and $aIds arrays
				$this->rebuildAPIResponseToQueries($oTickets, $aQueries, $aIds);

				foreach ($aQueries as $sModel => $sQuery) {

					try {

						if (!empty($aIds[$sModel])) {
							// Delete only the entries that we're going to insert again.
							$this->{$sModel}->deleteAll(array($sModel . '.id' => $aIds[$sModel]));
						}

						// Then save the updated records.
						$this->{$sModel}->query($sQuery);

					} catch (Exception $e) {

						$this->log('- Could not save the new ' . Inflector::pluralize($sModel) . '. MySQL says: "' . $e->errorInfo[2] . '"');
						$this->log('- Query executed: "' . $sQuery . '"');
						return false;

					}

				}

				return true;

			}

		}


		/**
		 * Rebuilds the response from the Autotask API into a set of MySQL insert queries.
		 * 
		 * Makes things a bit faster since Cake is kinda slow when running thousands of
		 * Model->save() requests at once.
		 * 
		 * When rebuilding the API response to one big ass query, there are several parts of
		 * data that's returned. Of course there's the tickets, but there might be other
		 * stuff attached. Each of these items have their own function using the format
		 * "{item}toQuery()".
		 * 
		 * @param  object $oTicket - The ticket object we got from Autotask.
		 * @param  array $aQueries - The (referenced) array with queries we'll be executing later on.
		 * @param  array $aIds - The (referenced) array with the id's of any new records. Helps you prevent duplicate id's.
		 * @return -
		 */
		private function rebuildAPIResponseToQueries($oTickets, &$aQueries, &$aIds) {

			foreach ($oTickets as $oTicket) {

				$aQueries['Ticket'] .= $this->ticketToQuery($oTicket, $aQueries['Ticket'], &$aIds);
				$aQueries['Resource'] .= $this->resourceToQuery($oTicket, $aQueries['Resource'], &$aIds);
				$aQueries['Queue'] .= $this->queueToQuery($oTicket, $aQueries['Queue'], &$aIds);
				$aQueries['Ticketstatus'] .= $this->statusToQuery($oTicket, $aQueries['Ticketstatus'], &$aIds);
				$aQueries['Ticketsource'] .= $this->ticketsourceToQuery($oTicket, $aQueries['Ticketsource'], &$aIds);
				$aQueries['Account'] .= $this->accountToQuery($oTicket, $aQueries['Account'], &$aIds);
				$aQueries['Issuetype'] .= $this->issuetypeToQuery($oTicket, $aQueries['Issuetype'], &$aIds);
				$aQueries['Subissuetype'] .= $this->subissuetypeToQuery($oTicket, $aQueries['Subissuetype'], &$aIds);

			}

		}


		/**
		 * Decide whether or not to run logic for specific data.
		 * 
		 * You can limit execution of the cronjob to certain data. For example,
		 * all tasks related to time entries. This function allows you to check
		 * if pieces of code should be executed in the current run.
		 * 
		 * @param  mixed $mDataToCheck - String or array containing the name(s) of the type of data that needs to be checked.
		 * @return boolean
		 */
		public function dataIsNeededFor($mDataToCheck = NULL) {

			// No specific data has been requested.
			if (empty($this->params['data_to_check'])) {
				return true;
			}

			// Only code concering specific data needs to run.
			if (is_array($mDataToCheck)) {

				if (in_array($this->params['data_to_check'], $mDataToCheck)) {
					return true;
				}

			} else {

				if (!empty($this->params['data_to_check']) && $mDataToCheck == $this->params['data_to_check']) {
					return true;
				}

			}

			return false;

		}


		/**
		 * Decide whether or not to log specific data.
		 * 
		 * You can limit execution of the cronjob to certain data. For example,
		 * all tasks related to time entries. This function allows you to check
		 * if pieces of code should be logged in the current run.
		 * 
		 * @param  mixed $mDataToCheck - String or array containing the name(s) of the type of data that needs to be checked.
		 * @return boolean
		 */
		public function outputIsNeededFor($mDataToCheck = NULL) {

			// Only code concering specific data needs to be logged.
			if (!empty($this->params['data_to_check'])) {

				if (is_array($mDataToCheck)) {

					if (in_array($this->params['data_to_check'], $mDataToCheck)) {
						return true;
					}

				} else {

					if (!empty($this->params['data_to_check']) && $mDataToCheck == $this->params['data_to_check']) {
						return true;
					}

				}

			}

			return false;

		}


		/**
		 * Deletes any existing records so we have a clean start.
		 *
		 * Note that this only gets used in the situation where ALL records of
		 * a certain type have to get removed. Specific records get removed
		 * purged right before saving (see function saveResponseToDatabase)
		 * 
		 */
		public function purgeDatabase() {

			if (!empty($this->params['data_to_check'])) {

				switch ($this->params['data_to_check']) {

					case 'completed_tickets':

						if ($this->params['full']) {

							$this->log('> Deleting all completed tickets from database..', 1);

							if ($this->Ticket->query('DELETE from tickets WHERE ticketstatus_id = 5;')) {
								$this->log('..done.', 1);
							} else {
								throw new Exception('Could not delete completed tickets.');
							}

						}

					break;


					case 'open_tickets':

						if ($this->params['full']) {

							$this->log('> Deleting all open tickets from database..', 1);

							if ($result = $this->Ticket->query('DELETE from tickets WHERE ticketstatus_id != 5;')) {
								$this->log('..done.', 1);
							} else {
								throw new Exception('Could not delete open tickets (' . $result . ').');
							}

						}

					break;

					default:
					break;

				}

			} else {

				if ($this->params['full']) {

					$this->log('> Truncating tickets table..', 1);

					if (!$this->Ticket->query('TRUNCATE TABLE tickets;')) {
						throw new Exception('Could truncate tickets table.');
					}

					$this->log('..done.', 1);

				}

			}

			return true;

		}


		/**
		 * Imports the completed tickets.
		 */
		private function importCompletedTickets() {

			if ($this->dataIsNeededFor('completed_tickets')) {

				$this->log('> Importing completed tickets into the database..', 1);

				$oTickets = $this->GetTicketsCompleted->execute();

				if (empty($oTickets)) {

					$this->log('..done - nothing saved, query returned no tickets.', 1);

					if ($this->outputIsNeededFor('completed_tickets')) {

						if ($this->params['full']) {
							$this->out('No completed tickets found with Completed Date = ' . date('Y-m-d', strtotime('-1 days')) . ' or ' . date('Y-m-d') . '.', 1, Shell::QUIET);
						} else {
							$this->out('No completed tickets found with Completed Date = ' . date('Y-m-d') . '.', 1, Shell::QUIET);
						}

					}

				} else {

					if (!$this->saveResponseToDatabase($oTickets)) {
						throw new Exception('Could save completed tickets to the database using saveResponseToDatabase().');
					} else {

						if ($this->outputIsNeededFor('completed_tickets')) {

							if ($this->params['full']) {
								$this->out(count($oTickets) . ' completed tickets found with Completed Date equal to ' . date('Y-m-d', strtotime('-1 days')) . ' or ' . date('Y-m-d') . '.', 1, Shell::QUIET);
							} else {
								$this->out(count($oTickets) . ' completed tickets found with Completed Date equal to ' . date('Y-m-d') . '.', 1, Shell::QUIET);
							}

						}

						$this->log('..done - imported ' . count($oTickets) . ' ticket(s).' , 1);

					}

				}

			}

			return true;

		}


		/**
		 * Import the tickets that have any other status then 'completed'.
		 */
		public function importOpenTickets() {

			if ($this->dataIsNeededFor('open_tickets')) {

				$this->log('> Importing open tickets into the database..', 1);

				$oTickets = $this->GetTicketsOpen->execute();

				if (empty($oTickets)) {

					if ($this->outputIsNeededFor('open_tickets')) {

						if (!$iAmountOfDays = Configure::read('Import.OpenTickets.history')) {
							$iAmountOfDays = 365;
						}

						if ($this->params['full']) {
							$this->out('No open tickets found with Create Date between ' . date('Y-m-d', strtotime('-' . $iAmountOfDays . ' days')) . ' and ' . date('Y-m-d') . '.', 1, Shell::QUIET);
						} else {
							$this->out('No open tickets found with Create Date ' . date('Y-m-d') . '.', 1, Shell::QUIET);
						}
					}

					$this->log('..done - nothing saved, query returned no tickets.', 1);

				} else {

					if (!$this->saveResponseToDatabase($oTickets)) {
						throw new Exception('Could save open tickets to the database using saveResponseToDatabase().');
					} else {

						if ($this->outputIsNeededFor('open_tickets')) {

							if (!$iAmountOfDays = Configure::read('Import.OpenTickets.history')) {
								$iAmountOfDays = 365;
							}

							if ($this->params['full']) {
								$this->out(count($oTickets) . ' open tickets found with Create Date between ' . date('Y-m-d', strtotime('-' . $iAmountOfDays . ' days')) . ' and ' . date('Y-m-d') . '.', 1, Shell::QUIET);
							} else {
								$this->out(count($oTickets) . ' open tickets found with Create Date ' . date('Y-m-d') . '.', 1, Shell::QUIET);
							}

						}

						$this->log('..done - imported ' . count($oTickets) . ' ticket(s).' , 1);

					}

				}

			}

			return true;

		}


		/**
		 * After tickets have been fetched from Autotask we calculate the totals for
		 * kill rate, queue health etc.
		 */
		private function calculateTotals() {

			// Processing of the tickets data into totals for kill rates, queue healths etc.
			$this->log('> Processing the tickets data into totals for all dashboards..', 1);

			if ($this->dataIsNeededFor(array('completed_tickets', 'open_tickets'))) {

				try {

					$this->CalculateTotalsByTicketStatus->execute();
					$this->CalculateTotalsOpenTickets->execute();
					$this->CalculateTotalsForKillRate->execute();
					$this->CalculateTotalsForQueueHealth->execute();
					$this->CalculateTotalsByTicketSource->execute();

				} catch (Exception $e) {
					throw $e;
				}

			}

			if ($this->dataIsNeededFor('ticket_sources')) {

				try {
					$this->CalculateTotalsByTicketSource->execute();
				} catch (Exception $e) {
					throw $e;
				}

			}

			if ($this->dataIsNeededFor('time_entries')) {

				try {
					$this->CalculateTotalsForTimeEntries->execute();
				} catch (Exception $e) {
					throw $e;
				}

			}

			$this->log('..done.', 1);
			return true;

		}


		/**
		 * Deletes both the view and the model cache.
		 */
		private function clearCache() {

			$this->log('> Clearing cache for all dashboards..', 1);

			// Clear the view and the model cache
			if (clearCache() && Cache::clear(null, '1_hour')) {
				$this->log('..done.', 1);
			} else {
				$this->log('..could not delete cache!', 1);
				throw new Exception('Could not delete cache.');
			}

			return true;

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function ticketToQuery($oTicket, $sExistingQuery) {

			$sQueryString = '';

			if (empty($sExistingQuery)) {
				$sQueryString .= "INSERT INTO tickets (`id`, `created`, `completed`, `number`, `title`, `ticketstatus_id`, `queue_id`, `resource_id`, `account_id`, `issuetype_id`, `subissuetype_id`, `due`, `priority`, `has_met_sla`, `ticketsource_id` ) VALUES ";
			} else {
				$sQueryString .= ', ';
			}

			// Defaults
			$sCompletedDate = '';
			$sDueDateTime = '';
			$iResourceId = 0;
			$iAccountId = 9999999999; // This ID gets used for your own company. Autotask uses 0 as ID, I use an actual ID ;-)
			$iIssueTypeId = 0;
			$iSubIssueTypeId = 0;
			$iQueueId = 0;
			$iHasMetSLA = 0;
			$iPriority = 0;
			$iSourceId = 0;
			// End

			// Reformat the dates to your own timezone.
			$sCreateDate = $this->TimeConverter->convertToOwnTimezone($oTicket->CreateDate);

			if (!empty($oTicket->CompletedDate)) {
				$sCompletedDate = $this->TimeConverter->convertToOwnTimezone($oTicket->CompletedDate);
			}

			if (!empty($oTicket->DueDateTime)) {
				$sDueDateTime = $this->TimeConverter->convertToOwnTimezone($oTicket->DueDateTime);
			}
			// End

			if (!empty($oTicket->AssignedResourceID)) {
				$iResourceId = $oTicket->AssignedResourceID;
			}

			if (!empty($oTicket->AccountID)) {
				$iAccountId = $oTicket->AccountID;
			}

			if (!empty($oTicket->IssueType)) {
				$iIssueTypeId = $oTicket->IssueType;
			}

			if (!empty($oTicket->SubIssueType)) {
				$iSubIssueTypeId = $oTicket->SubIssueType;
			}

			if (!empty($oTicket->QueueID)) {
				$iQueueId = $oTicket->QueueID;
			}

			if (!empty($oTicket->Source)) {
				$iSourceId = $oTicket->Source;
			}

			if (!empty($oTicket->Priority)) {
				$iPriority = $oTicket->Priority;
			}

			if (isset($oTicket->ServiceLevelAgreementHasBeenMet)) {
				$iHasMetSLA = $oTicket->ServiceLevelAgreementHasBeenMet;
			}

			$sQueryString .= '(';
				$sQueryString .= $oTicket->id;
				$sQueryString .= ',' . $this->db->value($sCreateDate);
				$sQueryString .= ',' . $this->db->value($sCompletedDate);
				$sQueryString .= ',' . $this->db->value($oTicket->TicketNumber);
				$sQueryString .= ',' . $this->db->value($oTicket->Title);
				$sQueryString .= ',' . $this->db->value($oTicket->Status);
				$sQueryString .= ',' . $this->db->value($iQueueId);
				$sQueryString .= ',' . $this->db->value($iResourceId);
				$sQueryString .= ',' . $this->db->value($iAccountId);
				$sQueryString .= ',' . $this->db->value($iIssueTypeId);
				$sQueryString .= ',' . $this->db->value($iSubIssueTypeId);
				$sQueryString .= ',' . $this->db->value($sDueDateTime);
				$sQueryString .= ',' . $this->db->value($iPriority);
				$sQueryString .= ',' . $this->db->value($iHasMetSLA);
				$sQueryString .= ',' . $this->db->value($iSourceId);
			$sQueryString .= ')';
			// End

			$aIds['Ticket'][] = $oTicket->id;

			return $sQueryString;

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function resourceToQuery($oTicket, $sExistingQuery, Array $aIds) {

			if (!empty($oTicket->AssignedResourceID)) {

				$sQueryString = '';

				if (empty($sExistingQuery)) {
					$sQueryString .= "INSERT INTO resources (`id`, `name` ) VALUES ";
				} else {
					$sQueryString .= ', ';
				}

				$aResource = $this->Resource->read(null, $oTicket->AssignedResourceID);

				if (empty($aResource) && !in_array($oTicket->AssignedResourceID, $aIds['Resource'])) {

					$oResource = $this->Resource->queryAutotask($aQuery);

					debug($oResource);
					exit();

					$sResourceName = '';

					if (!empty($oResource[0]->FirstName)) {
						$sResourceName .= $oResource[0]->FirstName . ' ';
					}

					if (!empty($oResource[0]->MiddleName)) {
						$sResourceName .= $oResource[0]->MiddleName . ' ';
					}

					if (!empty($oResource[0]->LastName)) {
						$sResourceName .= $oResource[0]->LastName;
					}

					$sQueryString .= '(';
						$sQueryString .= $iResourceId;
						$sQueryString .= ',' . $this->db->value($sResourceName);
					$sQueryString .= ')';

					$aIds['Resource'][] = $iResourceId;

					$this->log('- Found new Resource => Inserted into the database ("' . $sResourceName . '").' , 3);

				}

				return $sQueryString;

			} else {
				return '';
			}

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function queueToQuery($oTicket, $sExistingQuery, Array $aIds) {

			if (isset($oTicket->QueueID)) {

				$sQueryString = '';

				if (empty($sExistingQuery)) {
					$sQueryString .= "INSERT INTO queues (`id`, `name` ) VALUES ";
				} else {
					$sQueryString .= ', ';
				}

				$aQueue = $this->Queue->read(null, $iQueueId);

				if (empty($aQueue) && !in_array($iQueueId, $aIds['Queue'])) {

					$sQueryString .= '(';
						$sQueryString .= $iQueueId;
						$sQueryString .= ",''";
					$sQueryString .= ')';

					$aIds['Queue'][] = $iQueueId;

					$this->log('- Found new Queue => Inserted into the database (id ' . $iQueueId . ').' , 3);

				}

				return $sQueryString;

			} else {
				return '';
			}

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function statusToQuery($oTicket, $sExistingQuery, Array $aIds) {

			$sQueryString .= '';

			$aTicketstatus = $this->Ticketstatus->read(null, $oTicket->Status);

			if (empty($aTicketstatus) && !in_array($oTicket->Status, $aIds['Ticketstatus'])) {

				if (empty($sExistingQuery)) {
					$sQueryString .= "INSERT INTO ticketstatuses (`id`, `name` ) VALUES ";
				} else {
					$sQueryString .= ', ';
				}

				$sQueryString .= '(';
					$sQueryString .= $oTicket->Status;
					$sQueryString .= ",''";
				$sQueryString .= ')';

				$aIds['Ticketstatus'][] = $oTicket->Status;

				$this->log('- Found new Ticket Status => Inserted into the database (id ' . $oTicket->Status . ').' ,3);

			}

			return $sQueryString;

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function ticketsourceToQuery($oTicket, $sExistingQuery, Array $aIds) {

			if (isset($oTicket->Source)) {

				$sQueryString = '';

				$aTicketsource = $this->Ticketsource->read(null, $oTicket->Source);

				if (empty($aTicketsource) && !in_array($oTicket->Source, $aIds['Ticketsource'])) {

					if (empty($sExistingQuery)) {
						$sQueryString .= "INSERT INTO ticketsources (id, name ) VALUES ";
					} else {
						$sQueryString .= ', ';
					}

					$sQueryString .= '(';
						$sQueryString .= $oTicket->Source;
						$sQueryString .= ",''";
					$sQueryString .= ')';

					$aIds['Ticketsource'][] = $oTicket->Source;

					if (3 < $this->iLogLevel) {
						$this->log('- Found new Ticket Source => Inserted into the database (id ' . $oTicket->Source . ').', 'cronjob');
					}

				}

				return $sQueryString;

			} else {
				return '';
			}

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function accountToQuery($oTicket, $sExistingQuery, Array $aIds) {

			if (!empty($oTicket->AccountID)) {

				$sQueryString = '';

				$aAccount = $this->Account->read(null, $oTicket->AccountID);

				if (empty($aAccount) && !in_array($oTicket->AccountID, $aIds['Account'])) {

					if (empty($sExistingQuery)) {
						$sQueryString = "INSERT INTO accounts (`id`, `name` ) VALUES ";
					} else {
						$sQueryString .= ', ';
					}

					$oAccount = $this->Account->findInAutotask('all', array(
							'conditions' => array(
									'id' => $oTicket->AccountID
							)
					));

					if (!empty($oAccount[0]->AccountName)) {
						$sAccountName = $oAccount[0]->AccountName;
					} else {
						$sAccountName = '';
					}

					$sQueryString .= '(';
						$sQueryString .= $oTicket->AccountID;
						$sQueryString .= ',' . $this->db->value($sAccountName);
					$sQueryString .= ')';

					$aIds['Account'][] = $oTicket->AccountID;

					$this->log('- Found new Account => Inserted into the database ("' . $sAccountName . '").' , 3);

				}

				return $sQueryString;

			} else {
				return '';
			}

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function issuetypeToQuery($oTicket, $sExistingQuery, Array $aIds) {

			if (!empty($oTicket->IssueType)) {

				$sQueryString = '';

				$aIssueType = $this->Issuetype->read(null, $oTicket->IssueType);

				if (empty($aIssueType) && !in_array($oTicket->IssueType, $aIds['Issuetype'])) {

					if (empty($sExistingQuery)) {
						$sQueryString .= "INSERT INTO issuetypes (`id`) VALUES ";
					} else {
						$sQueryString .= ', ';
					}

					$sQueryString .= '(';
						$sQueryString .= $oTicket->IssueType;
					$sQueryString .= ')';

					$aIds['Issuetype'][] = $oTicket->IssueType;

					$this->log('- Found new Issue Type => Inserted into the database (id ' . $oTicket->IssueType . ').', 3);

				}

				return $sQueryString;

			} else {
				return '';
			}

		}


		/**
		 * Rebuilds the data of a ticket object into a string that you can use
		 * for your mysql query.
		 */
		private function subissuetypeToQuery($oTicket, $sExistingQuery, Array $aIds) {

			if (!empty($oTicket->SubIssueType)) {

				$sQueryString = '';

				$aSubIssueType = $this->Subissuetype->read(null, $oTicket->SubIssueType);

				if (empty($aSubIssueType) && !in_array($oTicket->SubIssueType, $aIds['Subissuetype'])) {

					if (empty($sExistingQuery)) {
						$sQueryString .= "INSERT INTO subissuetypes (`id`) VALUES ";
					} else {
						$sQueryString .= ', ';
					}

					$sQueryString .= '(';
						$sQueryString .= $oTicket->SubIssueType;
					$sQueryString .= ')';

					$aIds['Subissuetype'][] = $oTicket->SubIssueType;

					$this->log('- Found new Sub Issue Type => Inserted into the database (id ' . $oTicket->SubIssueType . ').' , 3);

				}

				return $sQueryString;

			} else {
				return '';
			}

		}

	}