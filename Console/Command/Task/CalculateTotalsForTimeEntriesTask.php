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

		public function execute() {

			if( !$this->__importTimeEntries() ) {
				return false;
			}

			return true;

		}


		private function __importTimeEntries() {

			$bErrorsEncountered = false;

			$this->Timeentry->query('TRUNCATE TABLE timeentries;');

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

					$this->Timeentry->create();
					if( !$this->Timeentry->save( array(
							'created' => $oResult->DateWorked
						,	'resource_id' => $oResult->ResourceID
						,	'ticket_id' => $iTicketId
						,	'hours_to_bill' => $oResult->HoursToBill
						,	'hours_worked' => $oResult->HoursWorked
						,	'non_billable' => $oResult->NonBillable
						,	'offset_hours' => $oResult->OffsetHours
					) ) ) {
						$bErrorsEncountered = true;
					}

				} else {

					foreach ( $oResult as $oTimeentry ) {

						$iTicketId = 0;
						if( !empty( $oTimeentry->TicketID ) ) {
							$iTicketId = $oTimeentry->TicketID;
						}

						$this->Timeentry->create();
						if( !$this->Timeentry->save( array(
								'created' => $oTimeentry->DateWorked
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

				}

			}

			if( $bErrorsEncountered ) {
				return false;
			}

			return true;

		}

	}