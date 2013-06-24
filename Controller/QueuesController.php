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

	class QueuesController extends AutotaskAppController {

		public function edit( $iQueueId ) {

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				if( $this->Queue->save( $this->request->data ) ) {

					$this->Session->setFlash( 'Queue changes have been saved.' );

					$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'queues'
						,	'action' => 'edit'
						,	$this->Queue->id
					) );

					exit();

				}

			}

			$this->request->data = $this->Queue->read( null, $iQueueId );

		}


		public function index() {

			$aQueues = $this->Queue->find( 'all' );
			$this->set( 'aQueues', $aQueues );

		}

	}