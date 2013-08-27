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
	class CalculateTotalsForTimeEntriesTask extends Shell {

		public $uses = array(
				'Autotask.Timeentry'
			,	'Autotask.Dashboard'
			,	'Autotask.Dashboardresource'
		);

		public $tasks = array(
				'Autotask.TimeConverter'
		);

		public function execute() {

			if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
				$this->iLogLevel = 0;
			}

			$this->TimeConverter = $this->Tasks->load( 'Autotask.TimeConverter' );

			if( !$this->__importTimeEntries() ) {
				return false;
			}

			return true;

		}


		private function __importTimeEntries() {

			$bErrorsEncountered = false;

			if( 2 < $this->iLogLevel ) {
				$this->log( "\t\t" . '> Truncating timeentries table..', 'cronjob' );
			}

			$this->Timeentry->query('TRUNCATE TABLE timeentries;');

			if( 2 < $this->iLogLevel ) {
				$this->log( "\t\t" . '..done.', 'cronjob' );
			}

			if( 2 < $this->iLogLevel ) {
				$this->log( "\t\t" . '> Importing time entries into the database for today (' . date( 'Y-m-d' ) . ')..', 'cronjob' );
			}

			$oResult = $this->Timeentry->findInAutotask( 'all', array(
					'conditions' => array(
							'IsThisDay' => array(
								'DateWorked' => date( 'Y-m-d' )
							)
					)
			) );

			if( !empty( $oResult ) ) {

				$bErrorsEncountered = false;

				if( 1 == count( $oResult ) ) {

					$iTicketId = 0;
					if( !empty( $oResult->TicketID ) ) {
						$iTicketId = $oResult->TicketID;
					}

					if( isset( $oResult->StartDateTime ) ) {
						$sStartDateTime = $oResult->StartDateTime;
					} else {
						$sStartDateTime = $oResult->CreateDateTime;
					}

					$this->Timeentry->create();
					if( !$this->Timeentry->save( array(
							'created' => $this->TimeConverter->convertToOwnTimezone( $sStartDateTime )
						,	'resource_id' => $oResult->ResourceID
						,	'ticket_id' => $iTicketId
						,	'hours_to_bill' => $oResult->HoursToBill
						,	'hours_worked' => $oResult->HoursWorked
						,	'non_billable' => $oResult->NonBillable
						,	'offset_hours' => $oResult->OffsetHours
					) ) ) {

						$bErrorsEncountered = true;

					} else {

						if( 2 < $this->iLogLevel ) {
							$this->log( "\t\t" . '..imported 1 time entry.', 'cronjob' );
						}

					}

				} else {

					foreach ( $oResult as $oTimeentry ) {

						$iTicketId = 0;
						if( !empty( $oTimeentry->TicketID ) ) {
							$iTicketId = $oTimeentry->TicketID;
						}

						if( isset( $oTimeentry->StartDateTime ) ) {
							$sStartDateTime = $oTimeentry->StartDateTime;
						} else {
							$sStartDateTime = $oTimeentry->CreateDateTime;
						}

						$this->Timeentry->create();
						if( !$this->Timeentry->save( array(
								'created' => $this->TimeConverter->convertToOwnTimezone( $sStartDateTime )
							,	'resource_id' => $oTimeentry->ResourceID
							,	'ticket_id' => $iTicketId
							,	'hours_to_bill' => $oTimeentry->HoursToBill
							,	'hours_worked' => $oTimeentry->HoursWorked
							,	'non_billable' => $oTimeentry->NonBillable
							,	'offset_hours' => $oTimeentry->OffsetHours
						) ) ) {
							$bErrorsEncountered = true;
						}

					}

					if( !$bErrorsEncountered ) {

						if( 2 < $this->iLogLevel ) {
							$this->log( "\t\t" . '..imported ' . count( $oResult ) . ' time entries.', 'cronjob' );
						}

					}

				}


			} else {

				if( 2 < $this->iLogLevel ) {
					$this->log( "\t\t" . '..nothing saved - query returned no time entries.', 'cronjob' );
				}

			}

			if( $bErrorsEncountered ) {
				return false;
			}

			return true;

		}

	}