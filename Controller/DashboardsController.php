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

	class DashboardsController extends AutotaskAppController {

		public $uses = array(
				'Autotask.Dashboard'
			,	'Autotask.Dashboardqueue'
			,	'Autotask.Dashboardresource'
			,	'Autotask.Dashboardticketstatus'
			,	'Autotask.Dashboardwidget'
			,	'Autotask.Ticket'
			,	'Autotask.Resource'
			,	'Autotask.Ticketstatus'
			,	'Autotask.Queue'
			,	'Autotask.Ticketstatuscount'
			,	'Autotask.Account'
			,	'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
			,	'Autotask.Killratecount'
			,	'Autotask.Queuehealthcount'
		);

		public $helpers = array(
				'Cache'
		);

		public $cacheAction = array(
				'display'  => array(
						'duration' => 3600
					,	'callbacks' => true
				)
		);

		public function beforeFilter() {

			parent::beforeFilter();

			if(
				$this->bIsMobile
				&&
				'display' == $this->action
			) {

				$this->redirect( array(
						'plugin' => 'autotask'
					,	'controller' => 'dashboards'
					,	'action' => 'mobile'
					,	$this->request->params['pass'][0]
				) );
				exit();
			}

		}

		public function index() {

			$aDashboards = $this->Dashboard->find( 'all', array(
					'contain' => array(
							'Dashboardqueue' => array(
									'Queue'
							)
						,	'Dashboardresource' => array(
									'Resource'
							)
					)
			) );

			$this->set( 'aDashboards', $aDashboards );

		}


		public function display( $iDashboardId = 0 ) {

			// Set it to request->data so the app controller can use it.
			$this->request->data = $this->Dashboard->read( null, $iDashboardId );

			$sCacheFile = 'widgets_for_dashboard_' . $iDashboardId;
			$aWidgets = Cache::read( $sCacheFile, '1_hour' );
			if ( !$aWidgets ) {
				$aWidgets = $this->Dashboard->getWidgetData( $iDashboardId );
				Cache::write( $sCacheFile, $aWidgets, '1_hour' );
			}

			// Backwards compatibility.
			if( empty( $aWidgets ) ) {

				// This dashboard didnt have widgets attached 'new style'. Rebuild it :-)
				$this->Dashboard->createDashboardWidgets( $this->request->data );
				$aWidgets = $this->Dashboard->getWidgetData( $iDashboardId );

			}
			// End

			$this->set( 'aWidgets', $aWidgets );

			$sLastImportDate = $this->Dashboard->getLastImportDate();
			$this->set( 'sLastImportDate', $sLastImportDate );

			if( $this->bIsMobile ) {
				$this->render( '/Dashboards/Mobile/display' );
			}

		}


		public function mobile( $iDashboardId = 0 ) {
			$this->display( $iDashboardId );
		}


		public function reorganize( $iDashboardId = 0 ) {
			$this->display( $iDashboardId );
		}


		/**
		 * Save function that gets called when you drag & drop widgets.
		 * 
		 * @param  integer $iDashboardId - The ID of the dashboard you're modifying.
		 * @return -
		 */
		public function ajaxSave( $iDashboardId ) {

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				if( !empty( $this->request->data  ) ) {

					$bErrorOccured = false;

					App::uses( 'Dashboardwidget', 'Autotask.Model' );
					$this->Dashboardwidget = new Dashboardwidget();

					foreach ( $this->request->data as $aWidget ) {

						$aSaveData = array(
								'id' => $aWidget['id']*1
							,	'col' => $aWidget['col']*1
							,	'row' => $aWidget['row']*1
						);

						if( !$this->Dashboardwidget->save( $aSaveData ) ) {
							$bErrorOccured = true;
						}

					}

				}

				clearCache(); // Remove the view cache

				if( $bErrorOccured ) {
					echo 'Saved but with errors.';
				} else {
					echo 'Saved without any errors.';
				}
				exit();

			}

		}


		public function add() {

			$aResources['options'] = $this->Resource->find( 'list', array(
					'order' => 'name asc'
			) );

			$aResources['selected'] = array();

			$aQueues['options'] = $this->Queue->find( 'list', array(
					'order' => 'name asc'
			) );

			$aQueues['selected'] = array();

			$aTicketstatuses['options'] = $this->Ticketstatus->find( 'list', array(
					'order' => 'name asc'
			) );

			$aTicketstatuses['selected'] = array();

			$this->set( 'aResources', $aResources );
			$this->set( 'aQueues', $aQueues );
			$this->set( 'aTicketstatuses', $aTicketstatuses );

			if( $this->request->is( 'post' ) ) {

				if( $this->Dashboard->save( $this->request->data['Dashboard'] ) ) {

					// Queues
					if( !empty( $this->request->data['Dashboardqueue']['id'] ) ) {

						foreach ( $this->request->data['Dashboardqueue']['id'] as $iKey => $iQueueId ) {

							$this->Dashboardqueue->create();
							$this->Dashboardqueue->save( array(
									'queue_id' => $iQueueId
								,	'dashboard_id' => $this->Dashboard->id
							) );
						}

					}
					// End - queues


					// Resources
					if( !empty( $this->request->data['Dashboardresource']['id'] ) ) {

						foreach ( $this->request->data['Dashboardresource']['id'] as $iKey => $iResourceId ) {

							$this->Dashboardresource->create();
							$this->Dashboardresource->save( array(
									'resource_id' => $iResourceId
								,	'dashboard_id' => $this->Dashboard->id
							) );
						}

					}
					// End - resources
					

					// Ticket statuses
					if( !empty( $this->request->data['Dashboardticketstatus']['id'] ) ) {

						foreach ( $this->request->data['Dashboardticketstatus']['id'] as $iKey => $iTicketstatusId ) {

							$this->Dashboardticketstatus->create();
							$this->Dashboardticketstatus->save( array(
									'ticketstatus_id' => $iTicketstatusId
								,	'dashboard_id' => $this->Dashboard->id
							) );
						}

					}
					// End - Ticket statuses

					$sFlashMessage = '<strong>Success!</strong> Dashboard has been added.';
					$this->Session->setFlash( $sFlashMessage );
					$this->redirect( '/' . $this->request->data['Dashboard']['slug'] );
					exit();

				}

			}

		}


		public function edit( $iDashboardId = null ) {

			$aDashboard = $this->Dashboard->read( null, $iDashboardId );

			// Set all the options for the checkboxes, together with the already selected options.
			$aResources['options'] = $this->Resource->find( 'list', array(
					'order' => 'name asc'
			) );

			$aQueues['options'] = $this->Queue->find( 'list', array(
					'order' => 'name asc'
			) );

			$aTicketstatuses['options'] = $this->Ticketstatus->find( 'list', array(
					'order' => 'name asc'
			) );

			$aResources['selected'] = array();
			if( !empty( $aDashboard['Dashboardresource'] ) ) {
				$aResources['selected'] = Hash::extract( $aDashboard['Dashboardresource'], '{n}.resource_id' );
			}

			$aQueues['selected'] = array();
			if( !empty( $aDashboard['Dashboardqueue'] ) ) {
				$aQueues['selected'] = Hash::extract( $aDashboard['Dashboardqueue'], '{n}.queue_id' );
			}

			$aTicketstatuses['selected'] = array();
			if( !empty( $aDashboard['Dashboardticketstatus'] ) ) {
				$aTicketstatuses['selected'] = Hash::extract( $aDashboard['Dashboardticketstatus'], '{n}.ticketstatus_id' );
			}

			$this->set( 'aResources', $aResources );
			$this->set( 'aQueues', $aQueues );
			$this->set( 'aTicketstatuses', $aTicketstatuses );
			// End

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				// Adjust all widgets
				$this->Dashboard->createDashboardWidgets( $this->request->data );

				$this->Dashboard->save( $this->request->data['Dashboard'] );

				// Queues
				$this->Dashboardqueue->deleteAll( array(
						'dashboard_id' => $this->request->data['Dashboard']['id']
				) );

				if( !empty( $this->request->data['Dashboardqueue']['id'] ) ) {

					foreach ( $this->request->data['Dashboardqueue']['id'] as $iKey => $iQueueId ) {

						$this->Dashboardqueue->create();
						$this->Dashboardqueue->save( array(
								'queue_id' => $iQueueId
							,	'dashboard_id' => $this->request->data['Dashboard']['id']
						) );
					}

				}
				// End - queues


				// Resources
				$this->Dashboardresource->deleteAll( array(
						'dashboard_id' => $this->request->data['Dashboard']['id']
				) );

				if( !empty( $this->request->data['Dashboardresource']['id'] ) ) {

					foreach ( $this->request->data['Dashboardresource']['id'] as $iKey => $iResourceId ) {

						$this->Dashboardresource->create();
						$this->Dashboardresource->save( array(
								'resource_id' => $iResourceId
							,	'dashboard_id' => $this->request->data['Dashboard']['id']
						) );
					}

				}
				// End - resources


				// Ticket statuses
				$this->Dashboardticketstatus->deleteAll( array(
						'dashboard_id' => $this->request->data['Dashboard']['id']
				) );

				if( !empty( $this->request->data['Dashboardticketstatus']['id'] ) ) {

					foreach ( $this->request->data['Dashboardticketstatus']['id'] as $iKey => $iTicketstatusId ) {

						$this->Dashboardticketstatus->create();
						$this->Dashboardticketstatus->save( array(
								'ticketstatus_id' => $iTicketstatusId
							,	'dashboard_id' => $this->request->data['Dashboard']['id']
						) );
					}

				}
				// End - Ticket statuses

				$sFlashMessage = '<strong>Success!</strong> Dashboard has been updated.';

				App::uses('HtmlHelper', 'View/Helper');
				$this->Html = new HtmlHelper( new View($this) );

				$sFlashMessage .= '<br/>' . $this->Html->link(
								'View your updated dashboard'
							,	array(
										'plugin' => 'autotask'
									,	'controller' => 'dashboards'
									,	'action' => 'display'
									,	$this->request->data['Dashboard']['id']
								)
				);

				clearCache(); // Clear the view cache
				Cache::clear( null ,'1_hour' ); // Clear the model cache

				$this->Session->setFlash( $sFlashMessage );
				$this->redirect( array(
						'plugin' => 'autotask'
					,	'controller' => 'dashboards'
					,	'action' => 'edit'
					,	$this->request->data['Dashboard']['id']
				) );

				exit();

			} else {
				$this->request->data['Dashboard'] = $aDashboard['Dashboard'];
			}

		}


		public function delete( $iDashboardId = null ) {

			if( $this->Dashboard->delete( $iDashboardId ) ) {

				$this->Session->setFlash( '<strong>Success!</strong> Dashboard has been deleted.' );
				$this->redirect( array(
						'plugin' => 'autotask'
					,	'controller' => 'dashboards'
					,	'action' => 'index'
				) );
				exit();

			}

		}


		/**
		 * Enables you to toggle a dashboard fullscreen, basically meaning you hide the navbar :-)
		 * 
		 * @param  integer $iDashboardId - The id of the dashboard.
		 * @return -
		 */
		public function toggleFullscreen( $iDashboardId = null ) {

			$iIsFullscreen = $this->Session->read( 'Dashboard.' . $iDashboardId . '.fullscreen' );

			if( 0 == $iIsFullscreen ) {

				$this->Session->write( 'Dashboard.' . $iDashboardId . '.fullscreen', 1 );
				echo 'enabled';

			} else {

				$this->Session->write( 'Dashboard.' . $iDashboardId . '.fullscreen', 0 );
				echo 'disabled';

			}

			clearCache(); // Clear the view cache
			exit();

		}

	}