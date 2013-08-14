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

	class DashboardwidgetsController extends AutotaskAppController {

		public $name = 'Dashboardwidgets';

		public function edit( $iDashboardwidgetId = null ) {

			$this->Dashboardwidget->id = $iDashboardwidgetId;
			$this->Dashboardwidget->saveField( $this->request->data['id'], $this->request->data['value'] );
			echo $this->request->data['value'];

			clearCache(); // Remove the view cache
			Cache::clear( null ,'1_hour' ); // Clear the model cache

			exit();

		}

	}