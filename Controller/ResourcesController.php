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

	class ResourcesController extends AutotaskAppController {

		public function edit( $iResourceId ) {

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				if( $this->Resource->save( $this->request->data ) ) {

					$this->Session->setFlash( 'Resource changes have been saved.' );

					$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'resources'
						,	'action' => 'edit'
						,	$this->Resource->id
					) );

					exit();

				}

			}

			$this->request->data = $this->Resource->read( null, $iResourceId );

		}


		public function index() {

			$aResources = $this->Resource->find( 'all' );
			$this->set( 'aResources', $aResources );

		}

	}