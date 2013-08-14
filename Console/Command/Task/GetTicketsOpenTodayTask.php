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
	class GetTicketsOpenTodayTask extends Shell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Dashboardqueue'
		);

		public function execute() {

			// We only fetch open tickets that go back 1 year (default).
			// This prevents recurring tickets from being included, which often leads
			// to insane amount of tickets.
			$aDates = array(
					date( 'Y-m-d' )
			);

			if( !$iAmountOfDays = Configure::read( 'Import.OpenTickets.history' ) ) {
				$iAmountOfDays = 365;
			}

			for ( $i=1; $i <= $iAmountOfDays; $i++ ) { 
				$aDates[] = date( 'Y-m-d', strtotime( '-' . $i . ' days' ) );
			}
			// End

			$oResult = $this->Ticket->findInAutotask( 'open', array(
					'conditions' => array(
							'Equals' => array(
								'QueueID' => Hash::extract( $this->Dashboardqueue->find( 'all' ), '{n}.Dashboardqueue.queue_id' )
							)
						,	'IsThisDay' => array(
								'CreateDate' => $aDates
							)
					)
			) );

			return $oResult;

		}

	}