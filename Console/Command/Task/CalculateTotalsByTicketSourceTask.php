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
	class CalculateTotalsByTicketSourceTask extends Shell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketsource'
			,	'Autotask.Ticketsourcecount'
		);

		public function execute() {

			// Now that all the data is in place, calculate totals.
			$aTicketCounts = array();

			$this->Ticketsource->recursive = -1;
			$aSources = $this->Ticketsource->find( 'list' );

			// Then import the new ones.
			foreach ( $aSources as $iSourceId => $sName ) {

				$iNumberOfTicketsInQueue = $this->Ticket->find( 'count', array(
						'conditions' => array(
								'Ticket.ticketsource_id' => $iSourceId
							,	'datediff(Ticket.created, now() )' => 0
						)
				) );

				$aExistingCount = $this->Ticketsourcecount->find( 'first', array(
						'conditions' => array(
								'Ticketsourcecount.created' => date( 'Y-m-d' )
							,	'Ticketsourcecount.ticketsource_id' => $iSourceId
						)
				) );

				if( !empty( $aExistingCount ) ) {

					$this->Ticketsourcecount->save( array(
							'id' => $aExistingCount['Ticketsourcecount']['id']
						,	'ticketsource_id' => $iSourceId
						,	'count' => $iNumberOfTicketsInQueue
					) );

				} else {

					$this->Ticketsourcecount->create();
					if( $this->Ticketsourcecount->save( array(
							'ticketsource_id' => $iSourceId
						,	'count' => $iNumberOfTicketsInQueue
					) ) ) {
						$this->log( '- Saved source count for source "' . $iSourceId .'"', 2 );
					}
				}
			}
			// End
		}
	}