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

	class Timeentry extends AutotaskAppModel {

		public $name = 'Timeentry';

		public $actsAs = array(
				'Autotask.Autotask'
		);

		public $belongsTo = array(
				'Autotask.Ticket'
			,	'Autotask.Resource'
		);

		/**
		 * @param  string $sType  'all'
		 * @param  array  $aQuery [description]
		 * 
		 * @return object
		 */
		public function findInAutotask($sType = 'all', $aConditions = array()) {

			$aQuery = array(
				'queryxml' => array(
						'entity' => 'Timeentry',
						'query' => array(
								'condition' => array()
						)
				)
			);

			$aQuery['queryxml']['query']['condition'] = array_merge($aQuery['queryxml']['query']['condition'], $aConditions);

			switch ($sType) {

				case 'all':
				default:
				break;

			}

			return $this->queryAutotask($aQuery);

		}

	}