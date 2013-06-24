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
		);

		public function main() {

			$this->log( 'Starting with the import.', 'cronjob' );

			// First delete any existing records of today, so we have a clean start.
			$this->log( '> Truncating tickets table..', 'cronjob' );
			$this->Ticket->query('TRUNCATE TABLE tickets;');
			$this->log( ' ..done.', 'cronjob' );

			$this->log( '> Importing completed tickets (today) into the database..', 'cronjob' );
			$oTickets = $this->GetTicketsCompletedToday->execute();

			if( empty( $oTickets ) ) {
				$this->log( ' ..nothing saved - query returned no tickets.', 'cronjob' );
			} else {
				$this->__saveTicketsToDatabase( $oTickets );
				$this->log( ' ..imported ' . count( $oTickets ) . ' ticket(s).', 'cronjob' );
			}

			$this->log( '> Importing open tickets (today) into the database..', 'cronjob' );
			$oTickets = $this->GetTicketsOpenToday->execute();

			if( empty( $oTickets ) ) {
				$this->log( ' ..nothing saved - query returned no tickets.', 'cronjob' );
			} else {
				$this->__saveTicketsToDatabase( $oTickets );
				$this->log( ' ..imported ' . count( $oTickets ) . ' ticket(s).', 'cronjob' );
			}

			$this->log( '> Calculating ticket status totals for all dashboards..', 'cronjob' );
			$this->CalculateTotalsByTicketStatus->execute();
			$this->log( ' ..done.', 'cronjob' );

			$this->log( '> Calculating kill rate totals for all dashboards..', 'cronjob' );
			$this->CalculateTotalsForKillRate->execute();
			$this->log( ' ..done.', 'cronjob' );

			$this->log( '> Calculating queue health totals for all dashboards..', 'cronjob' );
			$this->CalculateTotalsForQueueHealth->execute();
			$this->log( ' ..done.', 'cronjob' );

			$this->log( '> Clearing cached views for all dashboards..', 'cronjob' );
			if( clearCache() ) {
				$this->log( ' ..done.', 'cronjob' );
			} else {
				$this->log( ' ..could not delete view cache!', 'cronjob' );
			}

			$this->log( 'All tickets imported correctly.', 'cronjob' );

		}


		private function __saveTicketsToDatabase( $oTickets ) {

			if( !empty( $oTickets ) ) {

				if( 1 == count( $oTickets ) ) {

					$this->__saveTicket( $oTickets );

				} else {

					foreach ( $oTickets as $oTicket ) {
						$this->__saveTicket( $oTicket );
					}

				}

			}

		}


		private function __saveTicket( $oTicket ) {

			$iResourceId = 0;
			if( !empty( $oTicket->AssignedResourceID ) ) {
				$iResourceId = $oTicket->AssignedResourceID;
			}

			$sCompletedDate = '';
			if( !empty( $oTicket->CompletedDate ) ) {
				$sCompletedDate = $oTicket->CompletedDate;
			}

			// This is gets used for your own company.
			// 
			// Autotask uses 0 as ID, I use an actual ID ;-)
			$iAccountId = 9999999999;
			if( !empty( $oTicket->AccountID ) ) {
				$iAccountId = $oTicket->AccountID;
			}

			$iIssueTypeId = 0;
			if( !empty( $oTicket->IssueType ) ) {
				$iIssueTypeId = $oTicket->IssueType;
			}

			$iSubIssueTypeId = 0;
			if( !empty( $oTicket->SubIssueType ) ) {
				$iSubIssueTypeId = $oTicket->SubIssueType;
			}

			$iQueueId = 0;
			if( !empty( $oTicket->QueueID ) ) {
				$iQueueId = $oTicket->QueueID;
			}

			$iHasMetSLA = 0;
			if( isset( $oTicket->ServiceLevelAgreementHasBeenMet ) ) {
				$iHasMetSLA = $oTicket->ServiceLevelAgreementHasBeenMet;
			}

			// First, save the ticket.
			$this->Ticket->create();
			$this->Ticket->save( array(
					'created' => $oTicket->CreateDate
				,	'completed' => $sCompletedDate
				,	'number' => $oTicket->TicketNumber
				,	'title' => $oTicket->Title
				,	'ticketstatus_id' => $oTicket->Status
				,	'queue_id' => $iQueueId
				,	'resource_id' => $iResourceId
				,	'account_id' => $iAccountId
				,	'issuetype_id' => $iIssueTypeId
				,	'subissuetype_id' => $iSubIssueTypeId
				,	'due' => $oTicket->DueDateTime
				,	'priority' => $oTicket->Priority
				,	'has_met_sla' => $iHasMetSLA
			) );
			// End

			// Save the resource (if new)
			if( !empty( $oTicket->AssignedResourceID ) ) {

				$aResource = $this->Resource->read( null, $iResourceId );

				if( empty( $aResource ) ) {

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

					$this->Resource->save( array(
							'id' => $iResourceId
						,	'name' => $sResourceName
					) );

					$this->log( '  - Found new Resource => Inserted into the database ("' . $sResourceName . '").', 'cronjob' );

				}

			}
			// End


			// Save the queue (if new)
			if( 0 != $iQueueId ) {

				$aQueue = $this->Queue->read( null, $iQueueId );

				if( empty( $aQueue ) ) {

					$this->Queue->save( array(
							'id' => $iQueueId
						,	'name' => 'Not yet specified'
					) );

					$this->log( '  - Found new Queue => Inserted into the database (id ' . $iQueueId . ').', 'cronjob' );

				}

			}
			// End
			
			// Save the ticketstatus (if new)
			$aTicketstatus = $this->Ticketstatus->read( null, $oTicket->Status );

			if( empty( $aTicketstatus ) ) {

				$this->Ticketstatus->save( array(
						'id' => $oTicket->Status
					,	'name' => 'Not yet specified'
				) );

				$this->log( '  - Found new Ticket Status => Inserted into the database (id ' . $oTicket->Status . ').', 'cronjob' );

			}
			// End
			
			// Save the account (if new)
			if( !empty( $oTicket->AccountID ) ) {

				$aAccount = $this->Account->read( null, $oTicket->AccountID );

				if( empty( $aAccount ) ) {

					$oAccount = $this->Account->findInAutotask( 'all', array(
							'conditions' => array(
									'Equals' => array(
											'id' => $oTicket->AccountID
									)
							)
					) );

					$this->Account->save( array(
							'id' => $oTicket->AccountID
						,	'name' => $oAccount->AccountName
					) );

					$this->log( '  - Found new Account => Inserted into the database ("' . $oAccount->AccountName . '").', 'cronjob' );

				}

			}
			// End

			// Save the issuetype (if new)
			if( !empty( $oTicket->IssueType ) ) {

				$aIssueType = $this->Issuetype->read( null, $oTicket->IssueType );

				if( empty( $aIssueType ) ) {

					$this->Issuetype->save( array(
							'id' => $oTicket->IssueType
					) );

					$this->log( '  - Found new Issue Type => Inserted into the database (id ' . $oTicket->IssueType . ').', 'cronjob' );

				}

			}
			// End

			// Save the subissuetype (if new)
			if( !empty( $oTicket->SubIssueType ) ) {

				$aSubIssueType = $this->Subissuetype->read( null, $oTicket->SubIssueType );

				if( empty( $aSubIssueType ) ) {

					$this->Subissuetype->save( array(
							'id' => $oTicket->SubIssueType
					) );

					$this->log( '  - Found new Sub Issue Type => Inserted into the database (id ' . $oTicket->SubIssueType . ').', 'cronjob' );

				}

			}
			// End

			return true;

		}

	}