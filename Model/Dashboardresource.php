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

	class Dashboardresource extends AutotaskAppModel {

		public $name = 'Dashboardresource';

		public $belongsTo = array(
				'Autotask.Dashboard'
			,	'Autotask.Resource'
		);

		public $findMethods = array(
			'forDashboard' =>  true
		);

		public function _findForDashboard( $state, $query, $results = array() ) {

			/**
			 * Typically the first thing to check in our custom find function is the state of the query.
			 * The before state is the moment to modify the query, bind new associations, apply more behaviors,
			 * and interpret any special key that is passed in the second argument of find.
			 * This state requires you to return the $query argument (modified or not).
			 */
			if ( $state === 'before' ) {
				return $query;
			}

			/**
			 * The after state is the perfect place to inspect the results, inject new data, process it to return
			 * it in another format, or do whatever you like to the recently fetched data.
			 * This state requires you to return the $results array (modified or not).
			 */
			return Set::extract( $results, '{n}.Dashboardresource.resource_id' );

		}

	}