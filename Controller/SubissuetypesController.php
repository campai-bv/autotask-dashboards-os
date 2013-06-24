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

	class SubissuetypesController extends AutotaskAppController {

		public function edit( $iSubissuetypeId ) {

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				if( $this->Subissuetype->save( $this->request->data ) ) {

					$this->Session->setFlash( 'Subissuetype changes have been saved.' );

					$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'subissuetypes'
						,	'action' => 'edit'
						,	$this->Subissuetype->id
					) );

					exit();

				}

			}

			$this->request->data = $this->Subissuetype->read( null, $iSubissuetypeId );

		}


		public function index() {

			$aSubissuetypes = $this->Subissuetype->find( 'all' );
			$this->set( 'aSubissuetypes', $aSubissuetypes );

		}

	}