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
	class CalculateTotalsByTicketStatusTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketstatus'
			,	'Autotask.Ticketstatuscount'
		);

		public function execute() {

			$this->log('> Calculating ticket status totals for all dashboards..', 2);

			// Now that all the data is in place, calculate totals.
			$aTicketCounts = array();

			$this->Ticketstatus->recursive = -1;
			$aStatuses = $this->Ticketstatus->find( 'list' );

			// Then import the new ones.
			if(empty($aStatuses)) {
				$this->log('..done - no ticket statuses available.', 2);
			} else {
			
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
	
						if( $this->Ticketstatuscount->save( array(
								'id' => $aExistingCount['Ticketstatuscount']['id']
							,	'ticketstatus_id' => $iStatusId
							,	'count' => $iNumberOfTicketsInQueue
						) ) ) {
							$this->log('- Updated ticket status count for status "' . $iStatusId .'" (counted ' . $iNumberOfTicketsInQueue . ')', 4);
						} else {
							$this->log('- Could not update ticket status count for source "' . $iStatusId .'" (counted ' . $iNumberOfTicketsInQueue . ')', 4);
						}
	
					} else {
	
						$this->Ticketstatuscount->create();
						if( $this->Ticketstatuscount->save( array(
								'ticketstatus_id' => $iStatusId
							,	'count' => $iNumberOfTicketsInQueue
						) ) ) {
							$this->log('- Created ticket status count for status "' . $iStatusId .'" (counted ' . $iNumberOfTicketsInQueue . ')', 4);
						} else {
							$this->log('- Could not create ticket status count for source "' . $iStatusId .'" (counted ' . $iNumberOfTicketsInQueue . ')', 4);
						}
	
					}
	
				}
				
				$this->log('..done.', 2);
				
			}
			// End

		}

	}