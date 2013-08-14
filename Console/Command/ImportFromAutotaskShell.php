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
				'Autotask.Ticket'
			,	'Autotask.Resource'
			,	'Autotask.Ticketstatus'
			,	'Autotask.Queue'
			,	'Autotask.Account'
			,	'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
		);

		public $tasks = array(
				'Autotask.GetTicketsCompletedToday'
			,	'Autotask.GetTicketsOpenToday'
			,	'Autotask.CalculateTotalsByTicketStatus'
			,	'Autotask.CalculateTotalsForKillRate'
			,	'Autotask.CalculateTotalsForQueueHealth'
			,	'Autotask.CalculateTotalsForTimeEntries'
		);

		public function main() {

			$bErrorsEncountered = false;

			if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
				$this->iLogLevel = 0;
			}

			if( 0 < $this->iLogLevel ) {
				$this->log( 'Starting with the import.', 'cronjob' );
			}

			// First we must make sure we can login. We do this by performing a dummy call and see what it returns.
			$oResult = $this->Ticket->findInAutotask( 'open', array(
					'conditions' => array(
							'IsThisDay' => array(
								'CreateDate' => date( 'Y-m-d' )
							)
					)
			) );

			if( false === $oResult ) {
				$bErrorsEncountered = true;

			// Appearantly we can login, so let's get into action!
			} else {

				// Delete any existing records so we have a clean start.
				if( 1 < $this->iLogLevel ) {
					$this->log( '> Truncating tickets table..', 'cronjob' );
				}

				$this->Ticket->query('TRUNCATE TABLE tickets;');

				if( 1 < $this->iLogLevel ) {
					$this->log( ' ..done.', 'cronjob' );
				}
				// End

				// Import completed tickets
				if( 1 < $this->iLogLevel ) {
					$this->log( '> Importing completed tickets (today) into the database..', 'cronjob' );
				}

				$oTickets = $this->GetTicketsCompletedToday->execute();

				if( empty( $oTickets ) ) {

					if( 1 < $this->iLogLevel ) {
						$this->log( ' ..nothing saved - query returned no tickets.', 'cronjob' );
					}

				} else {

					if( !$this->__saveTicketsToDatabase( $oTickets ) ) {

						$bErrorsEncountered = true;

					} else {

						if( 1 < $this->iLogLevel ) {
							$this->log( ' ..imported ' . count( $oTickets ) . ' ticket(s).', 'cronjob' );
						}

					}

				}
				// End

				if( !$bErrorsEncountered ) {

					// Import the tickets that have any other status then 'completed'.
					if( 1 < $this->iLogLevel ) {
						$this->log( '> Importing open tickets (today) into the database..', 'cronjob' );
					}

					$oTickets = $this->GetTicketsOpenToday->execute();

					if( empty( $oTickets ) ) {

						if( 1 < $this->iLogLevel ) {
							$this->log( ' ..nothing saved - query returned no tickets.', 'cronjob' );
						}

					} else {

						if( !$this->__saveTicketsToDatabase( $oTickets ) ) {

							$bErrorsEncountered = true;

						} else {

							if( 1 < $this->iLogLevel ) {
								$this->log( ' ..imported ' . count( $oTickets ) . ' ticket(s).', 'cronjob' );
							}

						}

					}

					if( !$bErrorsEncountered ) {

						// Processing of the tickets data into totals for kill rates, queue healths etc.
						if( 1 < $this->iLogLevel ) {
							$this->log( '> Calculating ticket status totals for all dashboards..', 'cronjob' );
						}

						$this->CalculateTotalsByTicketStatus->execute();

						if( 1 < $this->iLogLevel ) {
							$this->log( ' ..done.', 'cronjob' );
						}

						if( 1 < $this->iLogLevel ) {
							$this->log( '> Calculating kill rate totals for all dashboards..', 'cronjob' );
						}

						$this->CalculateTotalsForKillRate->execute();

						if( 1 < $this->iLogLevel ) {
							$this->log( ' ..done.', 'cronjob' );
						}

						if( 1 < $this->iLogLevel ) {
							$this->log( '> Calculating queue health totals for all dashboards..', 'cronjob' );
						}

						$this->CalculateTotalsForQueueHealth->execute();

						if( 1 < $this->iLogLevel ) {
							$this->log( ' ..done.', 'cronjob' );
						}

						if( 1 < $this->iLogLevel ) {
							$this->log( '> Importing time entries..', 'cronjob' );
						}

						if( !$this->CalculateTotalsForTimeEntries->execute() ) {
							$bErrorsEncountered = true;
						}

						if( 1 < $this->iLogLevel ) {
							$this->log( ' ..done.', 'cronjob' );
						}

						if( 1 < $this->iLogLevel ) {
							$this->log( '> Clearing cache for all dashboards..', 'cronjob' );
						}

						if(
							clearCache() // Clear the view cache
							&&
							Cache::clear( null ,'1_hour' ) // Clear the model cache
						) {

							if( 1 < $this->iLogLevel ) {
								$this->log( ' ..done.', 'cronjob' );
							}

						} else {

							$bErrorsEncountered = true;
							$this->log( ' ..could not delete view cache!', 'cronjob' );

						}

					}

				}

			}
			// End

			if( $bErrorsEncountered ) {
				$this->log( 'Failed: we\'ve encountered some errors while running the import script.', 'cronjob' );
			} else {

				if( 0 < $this->iLogLevel ) {
					$this->log( 'Success: everything imported correctly.', 'cronjob' );
				}

			}

		}


		private function __saveTicketsToDatabase( $oTickets ) {

			if( !empty( $oTickets ) ) {

				// Gets filled up with queries that should get executed.
				$aQueries = array();
				// Gets filled up with the id's of all new records, to prevent duplicate ones.
				$aIds = array(
						'Ticket' => array()
					,	'Account' => array()
					,	'Resource' => array()
					,	'Queue' => array()
					,	'Ticketstatus' => array()
					,	'Issuetype' => array()
					,	'Subissuetype' => array()
				);

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

						$this->log( '- Could not save the new ' . Inflector::pluralize( $sModel ) . '. MySQL says: "' . $e->errorInfo[2] . '"', 'cronjob' );
						$this->log( '- ' . $sQuery, 'cronjob' );
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
		 * @param  object $oTicket - The ticket object we got from Autotask.
		 * @param  array $aQueries - The (referenced) array with queries we'll be executing later on.
		 * @param  array $aIds - The (referenced) array with the id's of any new records. Helps you prevent duplicate id's.
		 * @return -
		 */
		private function __rebuildAPIResponseToSaveData( $oTicket, &$aQueries, &$aIds ) {

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
			$sCreateDate = $this->__convertToOwnTimezone( $oTicket->CreateDate );

			if( !empty( $oTicket->CompletedDate ) ) {
				$sCompletedDate = $this->__convertToOwnTimezone( $oTicket->CompletedDate );
			}

			if( !empty( $oTicket->DueDateTime ) ) {
				$sDueDateTime = $this->__convertToOwnTimezone( $oTicket->DueDateTime );
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

					if( 3 < $this->iLogLevel ) {
						$this->log( '  - Found new Resource => Inserted into the database ("' . $sResourceName . '").', 'cronjob' );
					}

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

					if( 3 < $this->iLogLevel ) {
						$this->log( '  - Found new Queue => Inserted into the database (id ' . $iQueueId . ').', 'cronjob' );
					}

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

				if( 3 < $this->iLogLevel ) {
					$this->log( '  - Found new Ticket Status => Inserted into the database (id ' . $oTicket->Status . ').', 'cronjob' );
				}

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

					if( 3 < $this->iLogLevel ) {
						$this->log( '  - Found new Account => Inserted into the database ("' . $oAccount->AccountName . '").', 'cronjob' );
					}

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

					if( 3 < $this->iLogLevel ) {
						$this->log( '  - Found new Issue Type => Inserted into the database (id ' . $oTicket->IssueType . ').', 'cronjob' );
					}

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

					if( 3 < $this->iLogLevel ) {
						$this->log( '  - Found new Sub Issue Type => Inserted into the database (id ' . $oTicket->SubIssueType . ').', 'cronjob' );
					}

				}

			}
			// End

		}


		/**
		 * Converts a date from the Autotask API to your own timezone.
		 * @param  string $sDate - Date from the Autotask API
		 * @return string - Reformatted date
		 */
		private function __convertToOwnTimezone( $sDate ) {

			$oDate = new DateTime( $sDate, new DateTimeZone( 'EST' ) );
			$oDate->setTimezone( new DateTimeZone( date_default_timezone_get() ) );
			return $oDate->format( 'Y-m-d H:i:s' );

		}

	}