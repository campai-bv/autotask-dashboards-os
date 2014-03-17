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
		 * @param  array  $aConditions [description]
		 * 
		 * @return object
		 */
		public function findInAutotask($sType = 'all', $aConditions = array()) {

			$aQuery = array(
				'queryxml' => array(
						'entity' => 'Account',
						'query' => array(
								'condition' => array()
						)
				)
			);

			$aQuery['queryxml']['query']['condition'] = array_merge($aQuery['queryxml']['query']['condition'], $aConditions);

			switch ($sType) {

				case 'all':
				default:

					$aQuery['queryxml']['query']['condition'][] = array(
							'@operator' => 'AND',
							'field' => array(
									'expression' => array(
											'@op' => 'equals',
											'@' => 1
									),
									'@' => 'Active'
							)
					);

				break;

			}

			return $this->queryAutotask($aQuery);

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

	}