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

	class DashboardwidgetsettingsController extends AutotaskAppController {

		public $name = 'Dashboardwidgetsettings';

		public function edit( $iDashboardwidgetId = null ) {

			$aDashboardWidgetsetting = $this->Dashboardwidgetsetting->find( 'first', array(
					'conditions' => array(
							'Dashboardwidgetsetting.dashboardwidget_id' => $iDashboardwidgetId
						,	'Dashboardwidgetsetting.name' => $this->request->data['name']
					)
			) );

			if( !empty( $aDashboardWidgetsetting ) ) {

				$this->Dashboardwidgetsetting->id = $aDashboardWidgetsetting['Dashboardwidgetsetting']['id'];
				if( $this->Dashboardwidgetsetting->saveField( 'value', $this->request->data['value'] ) ) {

					echo 'success';
					clearCache(); // Remove the view cache
					Cache::clear( null ,'1_hour' ); // Clear the model cache

				} else {
					echo 'failed';
				}

			} else {

				if( $this->Dashboardwidgetsetting->save( array(
						'dashboardwidget_id' => $iDashboardwidgetId
					,	'name' => $this->request->data['name']
					,	'value' => $this->request->data['value']
				) ) ) {

					echo 'success';
					clearCache(); // Remove the view cache
					Cache::clear( null ,'1_hour' ); // Clear the model cache

				} else {
					echo 'failed';
				}

			}

			exit();

		}

	}