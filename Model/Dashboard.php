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
	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Dashboard extends AutotaskAppModel {

		public $name = 'Dashboard';

		public $hasMany = array(
				'Dashboardqueue' => array(
						'className' => 'Autotask.Dashboardqueue'
					,	'dependent' => true
				)
			,	'Dashboardresource' => array(
						'className' => 'Autotask.Dashboardresource'
					,	'dependent' => true
				)
			,	'Dashboardticketstatus' => array(
						'className' => 'Autotask.Dashboardticketstatus'
					,	'dependent' => true
				)
			,	'Dashboardwidget' => array(
						'className' => 'Autotask.Dashboardwidget'
					,	'dependent' => true
				)
		);


		public function getWidgetData( $iDashboardId ) {

			$aDashboardWidgets = array();

			// First we figure out if the widgets should be listening to any
			// specific queues or statuses.
			$aQueueIds = array();
			$aResourceIds = array();
			$aTicketstatusIds = array();

			if( !empty( $iDashboardId ) ) {

				App::uses( 'Dashboardqueue', 'Autotask.Model' );
				$this->Dashboardqueue = new Dashboardqueue();

				$aQueueIds = $this->Dashboardqueue->find( 'forDashboard', array(
						'conditions' => array(
								'Dashboardqueue.dashboard_id' => $iDashboardId
						)
				) );

				App::uses( 'Dashboardresource', 'Autotask.Model' );
				$this->Dashboardresource = new Dashboardresource();

				$aResourceIds = $this->Dashboardresource->find( 'forDashboard', array(
						'conditions' => array(
								'Dashboardresource.dashboard_id' => $iDashboardId
						)
				) );

				App::uses( 'Dashboardticketstatus', 'Autotask.Model' );
				$this->Dashboardticketstatus = new Dashboardticketstatus();

				$aTicketstatusIds = $this->Dashboardticketstatus->find( 'forDashboard', array(
						'conditions' => array(
								'Dashboardticketstatus.dashboard_id' => $iDashboardId
						)
				) );

			}
			// End

			// Now we fetch the widgets for the dashboard, and for each widget its actual data.
			$aDashboard = $this->find( 'first', array(
					'conditions' => array(
							'Dashboard.id' => $iDashboardId
					)
				,	'contain' => array(
							'Dashboardwidget' => array(
									'Widget'
								,	'order' => 'Dashboardwidget.row ASC'
							)
					)
			) );

			if( !empty( $aDashboard['Dashboardwidget'] ) ) {

				foreach ( $aDashboard['Dashboardwidget'] as $iKey => $aWidget ) {

					switch ( $aWidget['Widget']['id'] ) {

						// Kill Rate
						case 1:

							App::uses( 'Ticket', 'Autotask.Model' );
							$this->Ticket = new Ticket();

							$aWidget = array_merge( $aWidget, array(
									'Widgetdata' => $this->Ticket->getKillRate( $aQueueIds )
							) );

						break;

						// Kill Rate History - Graph
						// Kill Rate History - Bars
						case 2:
						case 8:

							App::uses( 'Killratecount', 'Autotask.Model' );
							$this->Killratecount = new Killratecount();

							$aWidget = array_merge( $aWidget, array(
									'Widgetdata' => $this->Killratecount->getRollingWeek( $iDashboardId )
							) );

						break;

						// Queue Health Graph
						case 3:

							App::uses( 'Queuehealthcount', 'Autotask.Model' );
							$this->Queuehealthcount = new Queuehealthcount();

							$aWidget = array_merge( $aWidget, array(
									'Widgetdata' => $this->Queuehealthcount->getHistory( $iDashboardId )
							) );

						break;

						// Accounts Top X
						case 4:

							App::uses( 'Account', 'Autotask.Model' );
							$this->Account = new Account();

							$aWidget = array_merge( $aWidget, array(
									'Widgetdata' => $this->Account->getTotals( $aQueueIds )
							) );

						break;

						// Queues Tables
						case 5:

							App::uses( 'Queue', 'Autotask.Model' );
							$this->Queue = new Queue();

							$aWidget = array_merge( $aWidget, array(
									'Widgetdata' => $this->Queue->getTotals( $aQueueIds )
							) );

						break;

						// Resources Tables
						case 6:

							App::uses( 'Resource', 'Autotask.Model' );
							$this->Resource = new Resource();

							$aWidget = array_merge( $aWidget, array(
									'Widgetdata' => $this->Resource->getTotals( $aQueueIds, $aResourceIds )
							) );

						break;

						// Ticket Status (count)
						case 7:

							if( !empty( $aWidget['type'] ) ) {

								App::uses( 'Ticket', 'Autotask.Model' );
								$this->Ticket = new Ticket();

								switch ( $aWidget['type'] ) {

									case 'missing_issue_type':

										$aWidget = array_merge( $aWidget, array(
												'Widgetdata' => array(
														'count' => $this->Ticket->getAtes( $aQueueIds )
												)
										) );

									break;

									case 'unassigned':

										$aWidget = array_merge( $aWidget, array(
												'Widgetdata' => $this->Ticket->getUnassignedTotals( $aQueueIds )
										) );

									break;

									case 'sla_violations':

										$aWidget = array_merge( $aWidget, array(
												'Widgetdata' => $this->Ticket->getSLAViolations( $aQueueIds )
										) );

									break;

									default:
									break;

								}

							} elseif( !empty( $aWidget['ticketstatus_id'] ) ) {

								App::uses( 'Ticketstatus', 'Autotask.Model' );
								$this->Ticketstatus = new Ticketstatus();

								$aWidgetData = $this->Ticketstatus->getTotals( $aQueueIds, array( $aWidget['ticketstatus_id'] ) );

								$aWidget = array_merge( $aWidget, array(
										'Widgetdata' => $aWidgetData[$aWidget['ticketstatus_id']]
								) );

							}

						break;

						default:
						break;

					}

					//  Make sure every widgets looks pretty by filling up empty names.
					if( empty( $aWidget['display_name'] ) ) {

						switch ( $aWidget['type'] ) {

							case 'missing_issue_type':
								$aWidget['display_name'] = 'Missing Issue Type';
							break;

							case 'unassigned':
								$aWidget['display_name'] = 'Unassigned';
							break;

							default:
								$aWidget['display_name'] = $aWidget['Widget']['default_name'];
							break;

						}

					}
					// End

					// Put the widget in the output array.
					$aDashboardWidgets[$iKey] = array(
							'Dashboardwidget' => $aWidget
					);

				}

			}
			// End

			return $aDashboardWidgets;

		}


		/**
		 * Since 1.2.0 widgets are seperate database entries.
		 * Whenever you first fire up a dashboard, all widgets are saved
		 * in the database.
		 * 
		 * @param  [type] $iDashboardId [description]
		 * @return [type]               [description]
		 */
		public function createDashboardWidgets( $iDashboardId ) {

			$this->recursive = 2;
			$aDashboard = $this->find( 'first', array(
					'conditions' => array(
							'Dashboard.id' => $iDashboardId
					)
			) );

			App::uses( 'Dashboardwidget', 'Autotask.Model' );
			$this->Dashboardwidget = new Dashboardwidget();

			// Take care of the ticket statuses
			if( !empty( $aDashboard['Dashboardticketstatus'] ) ) {

				foreach ( $aDashboard['Dashboardticketstatus'] as $aTicketstatus ) {

					$this->Dashboardwidget->create();
					$this->Dashboardwidget->save( array(
							'dashboard_id' => $iDashboardId
						,	'widget_id' => 7
						,	'ticketstatus_id' => $aTicketstatus['Ticketstatus']['id']
						,	'display_name' => $aTicketstatus['Ticketstatus']['name']
					) );

				}

			}

				// Unassigned
				if( 1 == $aDashboard['Dashboard']['show_unassigned'] ) {

					$this->Dashboardwidget->create();
					$this->Dashboardwidget->save( array(
							'dashboard_id' => $iDashboardId
						,	'widget_id' => 7
						,	'type' => 'unassigned'
						,	'display_name' => 'Unassigned'
					) );

				}
				// End
				
				// Missing Issue Type
				if( 1 == $aDashboard['Dashboard']['show_missing_issue_type'] ) {

					$this->Dashboardwidget->create();
					$this->Dashboardwidget->save( array(
							'dashboard_id' => $iDashboardId
						,	'widget_id' => 7
						,	'type' => 'missing_issue_type'
						,	'display_name' => 'Missing Issue Type'
					) );

				}
				// End
				
				// SLA Violations
				if( 1 == $aDashboard['Dashboard']['show_sla_violations'] ) {

					$this->Dashboardwidget->create();
					$this->Dashboardwidget->save( array(
							'dashboard_id' => $iDashboardId
						,	'widget_id' => 7
						,	'type' => 'sla_violations'
						,	'display_name' => 'SLA Violations'
					) );

				}
				// End


			// End

			if( 1 == $aDashboard['Dashboard']['show_kill_rate'] ) {

				$this->Dashboardwidget->create();
				$this->Dashboardwidget->save( array(
						'dashboard_id' => $iDashboardId
					,	'widget_id' => 1
				) );

			}

			if( 1 == $aDashboard['Dashboard']['show_rolling_week'] ) {

				$this->Dashboardwidget->create();
				$this->Dashboardwidget->save( array(
						'dashboard_id' => $iDashboardId
					,	'widget_id' => 2
				) );

			}

			if( 1 == $aDashboard['Dashboard']['show_rolling_week_bars'] ) {

				$this->Dashboardwidget->create();
				$this->Dashboardwidget->save( array(
						'dashboard_id' => $iDashboardId
					,	'widget_id' => 8
				) );

			}

			if( 1 == $aDashboard['Dashboard']['show_queue_health'] ) {

				$this->Dashboardwidget->create();
				$this->Dashboardwidget->save( array(
						'dashboard_id' => $iDashboardId
					,	'widget_id' => 3
				) );

			}

			if( 1 == $aDashboard['Dashboard']['show_accounts'] ) {

				$this->Dashboardwidget->create();
				$this->Dashboardwidget->save( array(
						'dashboard_id' => $iDashboardId
					,	'widget_id' => 4
				) );

			}

			if( 1 == $aDashboard['Dashboard']['show_queues'] ) {

				$this->Dashboardwidget->create();
				$this->Dashboardwidget->save( array(
						'dashboard_id' => $iDashboardId
					,	'widget_id' => 5
				) );

			}

			if( 1 == $aDashboard['Dashboard']['show_resources'] ) {

				$this->Dashboardwidget->create();
				$this->Dashboardwidget->save( array(
						'dashboard_id' => $iDashboardId
					,	'widget_id' => 6
				) );

			}

			return;

		}


		public function getLastImportDate() {

			$sFilename = APP . 'tmp/logs/cronjob.log';

			if ( file_exists( $sFilename ) ) {

				date_default_timezone_set( 'Europe/Amsterdam' );
				return date( "Y-m-d H:i:s", filemtime( $sFilename ) );

			}

			return false;

		}


	}