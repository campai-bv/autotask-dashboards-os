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

	class Queue extends AutotaskAppModel {

		public $name = 'Queue';

		public $hasMany = array(
				'Autotask.Ticket'
			,	'Autotask.Dashboardqueue'
		);


		public function getTotals( $aQueueIds = array() ) {

			if( !empty( $aQueueIds ) ) {

				$aQueues = $this->find( 'all', array(
						'conditions' => array(
								'Queue.id' => $aQueueIds
						)
					,	'contain' => array(
								'Ticket' => array(
										'conditions' => array(
												'Ticket.ticketstatus_id !=' => 5 // Completed
										)
								)
						)
				) );

			} else {

				$aQueues = $this->find( 'all', array(
						'contain' => array(
								'Ticket' => array(
										'conditions' => array(
												'Ticket.ticketstatus_id !=' => 5
										)
								)
						)
				) );

			}

			$aQueueTotals = array();

			foreach ( $aQueues as $aQueue ) {

				$iTotalDaysOpen = 0;
				$iOverdueTickets = 0;

				if( !empty( $aQueue['Ticket'] ) ) {

					foreach ( $aQueue['Ticket'] as $aTicket ) {

						$start = strtotime( $aTicket['created'] );
						$end = strtotime( date( 'Y-m-d h:I:s' ) );
						$iTotalDaysOpen += round( abs( $end - $start ) / 86400,0 );

						if( $aTicket['due'] < date( 'Y-m-d' ) ) {
							$iOverdueTickets ++;
						}

					}

					$aQueueTotals[ $aQueue['Queue']['id'] ] = array(
							'id' => $aQueue['Queue']['id']
						,	'name' => $aQueue['Queue']['name']
						,	'count' => count( $aQueue['Ticket'] )
						,	'average_days_open' => number_format( $iTotalDaysOpen/ count( $aQueue['Ticket'] ), 0, ',', '.' )
						,	'overdue' => $iOverdueTickets
					);

				} else {

					$aQueueTotals[ $aQueue['Queue']['id'] ] = array(
							'id' => $aQueue['Queue']['id']
						,	'name' => $aQueue['Queue']['name']
						,	'count' => 0
						,	'average_days_open' => 0
						,	'overdue' => $iOverdueTickets
					);

				}

				$aQueueTotals = Hash::sort( $aQueueTotals, '{n}.average_days_open', 'asc', 'numeric' );

			}

			return $aQueueTotals;

		}

	}