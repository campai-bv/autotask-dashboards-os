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

			$this->log( '> Truncating timeentries table..', 2 );
			$this->Timeentry->query('TRUNCATE TABLE timeentries;');
			$this->log( '..done.', 2 );

			$this->log( '> Importing time entries into the database for today (' . date( 'Y-m-d' ) . ')..', 2 );

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

					$dHoursToBill = 0.00;
					if( isset( $oResult->HoursToBill ) ) {
						$dHoursToBill = $oResult->HoursToBill;
					}

					$dHoursWorked = 0.00;
					if( isset( $oResult->HoursWorked ) ) {
						$dHoursWorked = $oResult->HoursWorked;
					}

					$this->Timeentry->create();
					if( !$this->Timeentry->save( array(
							'created' => $this->TimeConverter->convertToOwnTimezone( $sStartDateTime )
						,	'resource_id' => $oResult->ResourceID
						,	'ticket_id' => $iTicketId
						,	'hours_to_bill' => $dHoursToBill
						,	'hours_worked' => $dHoursWorked
						,	'non_billable' => $oResult->NonBillable
						,	'offset_hours' => $oResult->OffsetHours
					) ) ) {

						$bErrorsEncountered = true;

					} else {
						$this->log( '..imported 1 time entry.', 2 );
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

						$dHoursToBill = 0.00;
						if( isset( $oTimeentry->HoursToBill ) ) {
							$dHoursToBill = $oTimeentry->HoursToBill;
						}

						$dHoursWorked = 0.00;
						if( isset( $oTimeentry->HoursWorked ) ) {
							$dHoursWorked = $oTimeentry->HoursWorked;
						}

						$this->Timeentry->create();
						if( !$this->Timeentry->save( array(
								'created' => $this->TimeConverter->convertToOwnTimezone( $sStartDateTime )
							,	'resource_id' => $oTimeentry->ResourceID
							,	'ticket_id' => $iTicketId
							,	'hours_to_bill' => $dHoursToBill
							,	'hours_worked' => $dHoursWorked
							,	'non_billable' => $oTimeentry->NonBillable
							,	'offset_hours' => $oTimeentry->OffsetHours
						) ) ) {
							$bErrorsEncountered = true;
						}

					}

					if( !$bErrorsEncountered ) {
						$this->log( '..imported ' . count( $oResult ) . ' time entries.', 2 );
					}

				}


			} else {
				$this->log( '..nothing saved - query returned no time entries.', 2 );
			}

			if( $bErrorsEncountered ) {
				return false;
			}

			return true;

		}

	}