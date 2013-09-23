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
	App::uses('AutotaskAppModel', 'Autotask.Model');
	App::uses('CakeTime', 'Utility');
	class Resource extends AutotaskAppModel {

		public $name = 'Resource';

		public $hasMany = array(
				'Autotask.Ticket'
				,'Autotask.Timeentry'
		);


		public function getTotals( $aQueueIds = array(), $aResourceIds = array() ) {
			$db = $this->getDataSource();

			$aResourceTotals = array(
					'time_totals' => array(
							'hours_to_bill' => 0
						,	'hours_worked' => 0
					)
				,	'Resource' => array()
			);

			// First we take care of the resources,
			// in the end we check the unassigned tickets.
			if( !empty( $aResourceIds ) ) {

				$aResources = $this->find( 'all', array(
					'conditions' => array(
						'Resource.id' => $aResourceIds
						)
					,'contain' => array(
						'Ticket' => array(
							'conditions'=>array(
								array(CakeTime::daysAsSql('Sep 23, 2013', 'Sep 23, 2013', 'Ticket.completed'))
								,'OR'=>array('Ticket.ticketstatus_id != 5')
								)
							)
						,'Timeentry'=> array(
							'conditions'=>array(
								array(CakeTime::daysAsSql('Sep 23, 2013', 'Sep 23, 2013', 'Timeentry.created'))
								,'Timeentry.ticket_id > 0'
								)
							)
						)
					) );

			} 
			else {
				$aResources = $this->find( 'all' );
			}

			foreach ( $aResources as $aResource ) {

				$iTotalDaysOpen = 0;
				$iTicketsToDivideBy = 0;
				$iTicketsClosedToday = 0;
				$aTimeTotals = array(
						'hours_to_bill' => 0
					,	'hours_worked' => 0
				);

				foreach ( $aResource['Ticket'] as $aTicket ) {

					if( 5 != $aTicket['ticketstatus_id'] ) { // Not completed

						if( !empty( $aQueueIds ) ) {

							if( in_array( $aTicket['queue_id'], $aQueueIds ) ) {

								$start = strtotime( $aTicket['created'] );
								$end = strtotime( date( 'Y-m-d h:I:s' ) );
								$iTotalDaysOpen += round( abs( $end - $start ) / 86400,0 );
								$iTicketsToDivideBy += 1;

							}

						} else {

							$start = strtotime( $aTicket['created'] );
							$end = strtotime( date( 'Y-m-d h:I:s' ) );
							$iTotalDaysOpen += round( abs( $end - $start ) / 86400,0 );
							$iTicketsToDivideBy += 1;

						}

					// Closed/completed today
					} else {

						if( !empty( $aQueueIds ) ) {

							if(
								in_array( $aTicket['queue_id'], $aQueueIds )
								&&
								stristr( $aTicket['completed'], date( 'Y-m-d' ) )
							) {
								$iTicketsClosedToday ++;
							}

						} else {

							if( stristr( $aTicket['completed'], date( 'Y-m-d' ) ) ) {
								$iTicketsClosedToday ++;
							}

						}

					}
				}
				if( !empty( $aResource['Timeentry'] ) ) {
					foreach($aResource['Timeentry'] as $aTimeEntry) {
					// Calculate the time spent
						$aTimeTotals['hours_worked'] += $aTimeEntry['hours_worked'];
						$aResourceTotals['time_totals']['hours_worked'] += $aTimeEntry['hours_worked'];
						$aTimeTotals['hours_to_bill'] += $aTimeEntry['hours_to_bill'];
						$aResourceTotals['time_totals']['hours_to_bill'] += $aTimeEntry['hours_to_bill'];
					}
				}

				if( 0 == $iTicketsToDivideBy ) {

					$aResourceTotals['Resource'][ $aResource['Resource']['id'] ] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => 0
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => 0
						,	'time_totals' => $aTimeTotals
					);

				} else {

					$aResourceTotals['Resource'][ $aResource['Resource']['id'] ] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => $iTicketsToDivideBy
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => number_format( $iTotalDaysOpen/$iTicketsToDivideBy, 0, ',', '.' )
						,	'time_totals' => $aTimeTotals
					);

				}

			}
			// End

			$aResourceTotals['Resource'] = Hash::sort( $aResourceTotals['Resource'], '{n}.time_totals.hours_worked', 'desc', 'numeric' );
			return $aResourceTotals;

		}

	}