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
	App::uses('AutotaskAppController', 'Autotask.Controller');

	class TicketstatusesController extends AutotaskAppController {

		public function edit( $iTicketstatusId ) {

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				if( $this->Ticketstatus->save( $this->request->data ) ) {

					$this->Session->setFlash( 'Ticket Status changes have been saved.' );

					$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'ticketstatuses'
						,	'action' => 'edit'
						,	$this->Ticketstatus->id
					) );

					exit();

				}

			}

			$this->request->data = $this->Ticketstatus->read( null, $iTicketstatusId );

		}


		public function index() {

			$aTicketstatuses = $this->Ticketstatus->find( 'all' );
			$this->set( 'aTicketstatuses', $aTicketstatuses );

		}

	}