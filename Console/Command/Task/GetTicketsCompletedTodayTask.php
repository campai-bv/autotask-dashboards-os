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
	class GetTicketsCompletedTodayTask extends Shell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Queue'
		);

		public function execute() {

			$oResult = $this->Ticket->findInAutotask( 'closed', array(
					'conditions' => array(
							'IsThisDay' => array(
								'CompletedDate' => date( 'Y-m-d' )
							)
					)
			) );

			return $oResult;

		}

	}