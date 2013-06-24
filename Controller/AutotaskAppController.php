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
	App::uses('Controller', 'Controller');
	App::uses('AppController', 'Controller');

	class AutotaskAppController extends AppController {

		public function beforeFilter() {

			parent::beforeFilter();

			// Decide if mobile
			$bIsMobile = false;
			if( $this->request->isMobile() ) {

				$bIsiPad = (bool) strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' );

				if( !$bIsiPad ) {
					$bIsMobile = true;
				}

			}

			$this->bIsMobile = $bIsMobile;
			$this->set( 'bIsMobile', $bIsMobile );
			// End

		}

		public function beforeRender() {

			parent::beforeRender();

			if(
				'Dashboards' == $this->name
				&&
				(
					'display' == $this->action
					||
					'reorganize' == $this->action
				)
			) {

				$this->set( 'sApplicationTitle', $this->request->data['Dashboard']['name'] );

			} else {
				
				App::uses( 'Setting', 'Autotask.Model' );
				$this->Setting = new Setting();

				$aSettings = $this->Setting->read( null, 1 );
				$this->set( 'sApplicationTitle', $aSettings['Setting']['app_title'] );

			}

		}

	}