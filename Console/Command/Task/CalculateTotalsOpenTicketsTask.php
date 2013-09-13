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
	class CalculateTotalsOpenTicketsTask extends Shell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketstatuscount'
		);

		public function execute() {

			$iNumberOfTicketsInQueue = $this->Ticket->find( 'count', array(
					'conditions' => array(
							'Ticket.ticketstatus_id !=' => 5
					)
			) );

			$aExistingCount = $this->Ticketstatuscount->find( 'first', array(
					'conditions' => array(
							'Ticketstatuscount.created' => date( 'Y-m-d' )
						,	'Ticketstatuscount.ticketstatus_id' => 2
					)
			) );

			if( !empty( $aExistingCount ) ) {

				$this->Ticketstatuscount->save( array(
						'id' => $aExistingCount['Ticketstatuscount']['id']
					,	'ticketstatus_id' => 2 //empty status using for Open tickets
					,	'count' => $iNumberOfTicketsInQueue
				) );

			} else {

				$this->Ticketstatuscount->create();
				$this->Ticketstatuscount->save( array(
						'ticketstatus_id' => 2 //empty status using for Open tickets
					,	'count' => $iNumberOfTicketsInQueue
				) );

			}
		}
	}