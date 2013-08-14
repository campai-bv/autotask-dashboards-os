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

	class Ticketstatus extends AutotaskAppModel {

		public $name = 'Ticketstatus';

		public $hasMany = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketstatuscount'
		);


		public function getTotals( $aQueueIds = array(), $aTicketstatusIds = array() ) {

			if( empty( $aTicketstatusIds ) ) {
				return array();
			}

			$aConditions = array();

			$aContains['old'] = array(
					'Ticketstatuscount' => array(
							'conditions' => array(
									'Ticketstatuscount.created' => date( 'Y-m-d', strtotime( '-1 day' ) )
							)
					)
				,	'Ticket' => array()
			);

			$aContains['new'] = array(
					'Ticketstatuscount' => array(
							'conditions' => array(
									'Ticketstatuscount.created' => date( 'Y-m-d' )
							)
					)
				,	'Ticket' => array()
			);

			if( !empty( $aQueueIds ) ) {

				$aContains['old']['Ticket'] = array(
						'conditions' => array(
								'Ticket.queue_id' => $aQueueIds
						)
				);

				$aContains['new']['Ticket'] = array(
						'conditions' => array(
								'Ticket.queue_id' => $aQueueIds
						)
				);

			}


			if( !empty( $aTicketstatusIds ) ) {

				$aConditions['old'] = array(
						'Ticketstatus.id' => $aTicketstatusIds
				);

				$aConditions['new'] = array(
						'Ticketstatus.id' => $aTicketstatusIds
				);

			}

			$aOldStatuses = $this->find( 'all', array(
					'contain' => $aContains['old']
				,	'conditions' => $aConditions['old']
			) );

			$aNewStatuses = $this->find( 'all', array(
					'contain' => $aContains['new']
				,	'conditions' => $aConditions['new']
			) );

			$aTotals = array();

			foreach ( $aNewStatuses as $aStatusTotals ) {

				$iTicketStatusId = $aStatusTotals['Ticketstatus']['id'];

				$aTotals[ $iTicketStatusId ] = array(
						'name' => $aStatusTotals['Ticketstatus']['name']
					,	'counts' => array(
								'new' => count( $aStatusTotals['Ticket'] )
							,	'old' => 0
							,	'difference' => 0
						)
				);

			}

			foreach ( $aOldStatuses as $aStatusTotals ) {

				$iTicketStatusId = $aStatusTotals['Ticketstatus']['id'];

				if( !empty( $aStatusTotals['Ticketstatuscount'][0]['count'] ) ) {
					$iOld = $aStatusTotals['Ticketstatuscount'][0]['count'];
				} else {
					$iOld = 0;
				}

				$aTotals[ $iTicketStatusId ]['counts']['old'] = $iOld;
				$iNew = $aTotals[ $iTicketStatusId ]['counts']['new'];

				// You cant divide by zero
				if( 0 == $iOld ) {
					$aTotals[ $iTicketStatusId ]['counts']['difference'] = 0;
				} else {
					$aTotals[ $iTicketStatusId ]['counts']['difference'] = number_format( ( ( 100 * $iNew ) / $iOld ) - 100, 0, ',', '.' );
				}

			}

			return $aTotals;

		}

	}