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

	class SettingsController extends AutotaskAppController {

		public function edit() {

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				if( $this->Setting->save( $this->request->data ) ) {

					$this->Session->setFlash( 'Setting changes have been saved.' );

					$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'settings'
						,	'action' => 'edit'
						,	$this->Setting->id
					) );

					exit();

				}

			}

			$this->request->data = $this->Setting->read( null, 1 );

		}

	}