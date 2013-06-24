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
	// Connect the dashboards, dynamically.
	App::uses( 'Dashboard', 'Autotask.Model' );
	$mDashboard = new Dashboard();

	$sPossibleDashboardSlug = substr( $_SERVER['REQUEST_URI'], 1 );

	$aDashboard = $mDashboard->find( 'first' , array(
			'conditions' => array(
					'slug' => $sPossibleDashboardSlug
			)
	) );

	if( !empty( $aDashboard ) ) {
		Router::connect( $_SERVER['REQUEST_URI'], array('plugin' => 'Autotask', 'controller' => 'dashboards', 'action' => 'display', $aDashboard['Dashboard']['id'] ) );
	}
	// End

	Router::connect('/autotask/settings', array('plugin' => 'autotask', 'controller' => 'settings', 'action' => 'edit'));