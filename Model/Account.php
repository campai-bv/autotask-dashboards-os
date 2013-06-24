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

	class Account extends AutotaskAppModel {

		public $name = 'Account';

		public $actsAs = array(
				'Autotask.Autotask'
		);

		public $hasMany = array(
				'Autotask.Ticket'
		);


		/**
		 * Fallback. You can overwrite this in your model.
		 * 
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


		public function getTotals( $aQueueIds = array() ) {

			$aAccountTotals = array();

			$aAccounts = $this->find( 'all' );

			foreach ( $aAccounts as $aAccount ) {

				if(
					0 != count( $aAccount['Ticket'] )
				) {

					$iTotalDaysOpen = 0;
					$iTicketsToDivideBy = 0;

					foreach ( $aAccount['Ticket'] as $aTicket ) {

						if(
							13 != $aTicket['ticketstatus_id'] // Opgelost
							&&
							5 != $aTicket['ticketstatus_id'] // Created
						) {

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

						}

					}

					if( 0 == $iTicketsToDivideBy ) {

						$aAccountTotals[ $aAccount['Account']['id'] ] = array(
								'name' => $aAccount['Account']['name']
							,	'id' => $aAccount['Account']['id']
							,	'count' => 0
							,	'average_days_open' => 0
						);

					} else {

						$aAccountTotals[ $aAccount['Account']['id'] ] = array(
								'name' => $aAccount['Account']['name']
							,	'id' => $aAccount['Account']['id']
							,	'count' => $iTicketsToDivideBy
							,	'average_days_open' => number_format( $iTotalDaysOpen/$iTicketsToDivideBy, 0, ',', '.' )
						);

					}

				}

			}
			// End

			$aAccountTotals = Hash::sort( $aAccountTotals, '{n}.count', 'desc', 'numeric' );

			if( 3 < count( $aAccountTotals ) ) {
				$aAccountTotals = array_chunk( $aAccountTotals, 3 );
				$aAccountTotals = $aAccountTotals[0];
			}

			return $aAccountTotals;

		}

		/**
		 * Fallback. You can overwrite this in your model.
		 * 
		 */
		private function _findAllInAutotask( Array $aQuery ) {

			$aConditions = array();

			if( !empty( $aQuery['conditions'] ) ) {
				$aQuery['conditions'] = array_merge_recursive( $aQuery['conditions'], $aConditions );
			} else {
				$aQuery['conditions'] = $aConditions;
			}

			return $this->queryAutotask( 'Account', $aQuery );

		}

	}