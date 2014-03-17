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
	class CalculateTotalsForTimeEntriesTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Timeentry'
			,	'Autotask.Dashboard'
			,	'Autotask.Dashboardresource'
		);

		public $tasks = array(
				'Autotask.TimeConverter'
		);

		public function execute() {

			$this->TimeConverter = $this->Tasks->load('Autotask.TimeConverter');

			if( !$this->__importTimeEntries() ) {
				return false;
			}

			return true;

		}


		private function __importTimeEntries() {

			if ($this->dataIsNeededFor('time_entries')) {

				$this->log('> Importing time entries..', 2);
				$bErrorsEncountered = false;

				$this->log('> Truncating timeentries table..', 4);
				$this->Timeentry->query('TRUNCATE TABLE timeentries;');
				$this->log('..done.', 4);

				$this->log('> Importing time entries into the database for today (' . date( 'Y-m-d' ) . ')..', 4);

				$oResult = $this->Timeentry->findInAutotask('all', array(
						'conditions' => array(
								'IsThisDay' => array(
										'DateWorked' => date('Y-m-d')
								)
						)
				));

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

						$iNonBillable = 0;
						if (!empty($oResult->NonBillable)) {
							$iNonBillable = $oResult->NonBillable;
						}

						$this->Timeentry->create();
						if( !$this->Timeentry->save( array(
								'created' => $this->TimeConverter->convertToOwnTimezone( $sStartDateTime )
							,	'resource_id' => $oResult->ResourceID
							,	'ticket_id' => $iTicketId
							,	'hours_to_bill' => $dHoursToBill
							,	'hours_worked' => $dHoursWorked
							,	'non_billable' => $iNonBillable
							,	'offset_hours' => $oResult->OffsetHours
						) ) ) {

							$bErrorsEncountered = true;

						} else {
							$this->log('..done - imported 1 time entry.', 2);
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

							$iNonBillable = 0;
							if (!empty($oTimeentry->NonBillable)) {
								$iNonBillable = $oTimeentry->NonBillable;
							}

							$this->Timeentry->create();
							if( !$this->Timeentry->save( array(
									'created' => $this->TimeConverter->convertToOwnTimezone( $sStartDateTime )
								,	'resource_id' => $oTimeentry->ResourceID
								,	'ticket_id' => $iTicketId
								,	'hours_to_bill' => $dHoursToBill
								,	'hours_worked' => $dHoursWorked
								,	'non_billable' => $iNonBillable
								,	'offset_hours' => $oTimeentry->OffsetHours
							) ) ) {
								$bErrorsEncountered = true;
							}

						}

						if( !$bErrorsEncountered ) {

							if ($this->outputIsNeededFor('time_entries')) {
								$this->out(count($oResult) . ' time entries found with Worked Date equal to ' . date('Y-m-d') . '.', 1, Shell::QUIET);
							}

							$this->log('..done - imported ' . count( $oResult ) . ' time entries.', 2);
						}

					}

				} else {

					if ($this->outputIsNeededFor('time_entries')) {
						$this->out('No time entries found with Worked Date = ' . date('Y-m-d') . '.', 1, Shell::QUIET);
					}

					$this->log('..done - nothing saved because query returned no time entries.', 2);
				}

				if( $bErrorsEncountered ) {
					return false;
				}

			}

			return true;

		}

	}