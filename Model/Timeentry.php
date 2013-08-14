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
		public function findInAutotask( $sType = 'all', $aQuery = array() ) {

			switch ( $sType ) {

				case 'all':
				default:
					return $this->_findAllInAutotask( $aQuery );
				break;

			}

		}


		private function _findAllInAutotask( Array $aQuery ) {

			$aConditions = array();

			if( !empty( $aQuery['conditions'] ) ) {
				$aQuery['conditions'] = array_merge_recursive( $aQuery['conditions'], $aConditions );
			} else {
				$aQuery['conditions'] = $aConditions;
			}

			return $this->queryAutotask( 'Timeentry', $aQuery );

		}

	}