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
	class GetResourcesTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Resource'
		);

		public function execute() {

			if ($this->dataIsNeededFor('resources')) {

				$this->log('> Importing active resources into the database..', 1);

				$oResources = $this->Resource->findInAutotask('all', array(
						'conditions' => array(
								'Equals' => array(
										'Active' => true
								)
						)
				));

				if (empty($oResources)) {

					$this->log('..done - nothing saved, query returned no resources.', 1);

					if ($this->outputIsNeededFor('resources')) {
						$this->out('No active resources found.', 1, Shell::QUIET);
					}

				} else {

					$iNewResources = 0;

					foreach ($oResources as $oResource) {

						$aResource = $this->Resource->read(null, $oResource->id);

						if (empty($aResource)) {

							$iNewResources++;
							$sResourceName = '';

							if (!empty($oResource->FirstName)) {
								$sResourceName .= $oResource->FirstName . ' ';
							}

							if (!empty($oResource->MiddleName)) {
								$sResourceName .= $oResource->MiddleName . ' ';
							}

							if (!empty($oResource->LastName)) {
								$sResourceName .= $oResource->LastName;
							}

							if (!$this->Resource->save(array(
									'id' => $oResource->id
								,	'name' => $sResourceName
							))) {
								throw new Exception('Could save active resource to the database.');
							}

						}

					}

					if ($this->outputIsNeededFor('resources')) {
						$this->out(count($oResources) . ' active resources found, ' . $iNewResources . ' new ones saved.', 1, Shell::QUIET);
					}

					$this->log('..done - found ' . count($oResources) . ' active resource(s), saved ' . $iNewResources . ' new ones.' , 1);

				}

			}

		}

	}