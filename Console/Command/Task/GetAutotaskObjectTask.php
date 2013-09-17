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
class GetAutotaskObjectTask extends Shell {

	public function &execute() {

		$this->connectAutotask();
		return $this->oAutotask;

	}

	public function checkConnectAutotask() {
		
		$oResponse = $this->oAutotask->client->getThresholdAndUsageInfo();
		if(empty($oResponse->getThresholdAndUsageInfoResult->EntityReturnInfoResults->EntityReturnInfo->Message)) {
			return false;
		}
		if(strpos($oResponse->getThresholdAndUsageInfoResult->EntityReturnInfoResults->EntityReturnInfo->Message, 'TimeframeOfLimitation')) {
			return true;
		}
		return false;
	}
	public function connectAutotask() {
		if(isset($this->oAutotask)) {
			if(is_object($this->oAutotask)) {
				return true;
			}
		}
		
		$sLogin = Configure::read( 'Autotask.username' );
		$sPassword = Configure::read( 'Autotask.password' );
		$sLocation = Configure::read( 'Autotask.wsdl' ) ;

		if ( !extension_loaded( 'soap' ) ) {
			$this->log( 'SOAP is not available, unable to perform requests to the Autotask API.', 'cronjob' );
			$this->log( 'SOAP is not available, unable to perform requests to the Autotask API.', 'error' );
			exit();
		}
		// setup the atws object
		$this->oAutotask = new atws\atws();
		if ($this->oAutotask->connect($sLocation,$sLogin,$sPassword)) {
			if ($this->checkConnectAutotask() === true) {
				return true;
			}
			else {
				unset($this->oAutotask);
				$this->log('autotask connected but simple check failed');
				return false;
			}				
		}
		else {
			$this->log('could not connect to autotask');
			if(isset($this->oAutotask->last_connect_fault)) {
				$this->log($this->oAutotask->last_connect_fault);
			}
		}

	}


	}
