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

	class Resource extends AutotaskAppModel {

		public $name = 'Resource';

		public $actsAs = array(
				'Autotask.Autotask'
		);

		public $hasMany = array(
				'Autotask.Ticket'
		);


		/**
		 * @param  string $sType  'all'
		 * @param  array  $aQuery [description]
		 * 
		 * @return object
		 */
		public function findInAutotask( $sType = 'all', $aQuery = array() ) {

			switch ( $sType ) {

				case 'all':
				default:
					return $this->_findAllInAutotask( $aQuery );
				break;

			}

		}


		public function getTotals( $aQueueIds = array(), $aResourceIds = array() ) {

			$aResourceTotals = array();

			// First we take care of the resources,
			// in the end we check the unassigned tickets.
			if( !empty( $aResourceIds ) ) {

				$aResources = $this->find( 'all', array(
						'conditions' => array(
								'Resource.id' => $aResourceIds
						)
				) );

			} else {
				$aResources = $this->find( 'all' );
			}

			foreach ( $aResources as $aResource ) {

				$iTotalDaysOpen = 0;
				$iTicketsToDivideBy = 0;
				$iTicketsClosedToday = 0;

				foreach ( $aResource['Ticket'] as $aTicket ) {

					if( 5 != $aTicket['ticketstatus_id'] ) { // Completed

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

				if( 0 == $iTicketsToDivideBy ) {

					$aResourceTotals[ $aResource['Resource']['id'] ] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => 0
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => 0
					);

				} else {

					$aResourceTotals[ $aResource['Resource']['id'] ] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => $iTicketsToDivideBy
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => number_format( $iTotalDaysOpen/$iTicketsToDivideBy, 0, ',', '.' )
					);

				}

			}
			// End

			$aResourceTotals = Hash::sort( $aResourceTotals, '{n}.closed', 'desc', 'numeric' );
			return $aResourceTotals;

		}


		private function _findAllInAutotask( Array $aQuery ) {

			$aConditions = array();

			if( !empty( $aQuery['conditions'] ) ) {
				$aQuery['conditions'] = array_merge_recursive( $aQuery['conditions'], $aConditions );
			} else {
				$aQuery['conditions'] = $aConditions;
			}

			return $this->queryAutotask( 'Resource', $aQuery );

		}

	}