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
	class CalculateTotalsByTicketStatusTask extends Shell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketstatus'
			,	'Autotask.Ticketstatuscount'
		);

		public function execute() {

			// Now that all the data is in place, calculate totals.
			$aTicketCounts = array();

			$this->Ticketstatus->recursive = -1;
			$aStatuses = $this->Ticketstatus->find( 'list' );

			// Then import the new ones.
			foreach ( $aStatuses as $iStatusId => $sName ) {

				$iNumberOfTicketsInQueue = $this->Ticket->find( 'count', array(
						'conditions' => array(
								'Ticket.ticketstatus_id' => $iStatusId
						)
				) );

				$aExistingCount = $this->Ticketstatuscount->find( 'first', array(
						'conditions' => array(
								'Ticketstatuscount.created' => date( 'Y-m-d' )
							,	'Ticketstatuscount.ticketstatus_id' => $iStatusId
						)
				) );

				if( !empty( $aExistingCount ) ) {

					$this->Ticketstatuscount->save( array(
							'id' => $aExistingCount['Ticketstatuscount']['id']
						,	'ticketstatus_id' => $iStatusId
						,	'count' => $iNumberOfTicketsInQueue
					) );

				} else {

					$this->Ticketstatuscount->create();
					$this->Ticketstatuscount->save( array(
							'ticketstatus_id' => $iStatusId
						,	'count' => $iNumberOfTicketsInQueue
					) );

				}

			}
			// End

		}

	}