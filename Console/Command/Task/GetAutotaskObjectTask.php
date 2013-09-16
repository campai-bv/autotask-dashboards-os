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

		public $uses = array(
				'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
			,	'Autotask.Queue'
			,	'Autotask.Ticketstatus'			
		);

		/**
		 * Updates or inserts new picklist entries
		 * 
		 * @return
		 */
		public function execute() {

			$this->connectAutotask();
			return $this->oAutotask();

		}



	private function getAutotaskLogin() {
		
		$aLogin = array(
				'login' => Configure::read( 'Autotask.username' )
			,	'password' => Configure::read( 'Autotask.password' )
			,	'location' => Configure::read( 'Autotask.wsdl' )
		);

		if(
			empty( $aLogin['login'] )
			||
			empty( $aLogin['password'] )
			||
			empty( $aLogin['location'] )
		) {

			$this->log( 'I\'m not able to use the Autotask API if you don\'t provide your credentials (/var/www/app/Plugin/Autotask/Config/bootstrap.php).', 'cronjob' );
			$this->log( 'I\'m not able to use the Autotask API if you don\'t provide your credentials (/var/www/app/Plugin/Autotask/Config/bootstrap.php).', 'error' );
			return false;

		}
		return $aLogin;
		
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
		
		$aLogin = $this->getAutotaskLogin();
		if ($aLogin == false) { 
			return false;
		}
		if ( !extension_loaded( 'soap' ) ) {
			$this->log( 'SOAP is not available, unable to perform requests to the Autotask API.', 'cronjob' );
			$this->log( 'SOAP is not available, unable to perform requests to the Autotask API.', 'error' );
			exit();
		}
		// setup the atws object
		$this->oAutotask = new atws\atws();
		if ($this->oAutotask->connect($aLogin['location'],$aLogin['login'],$aLogin['password'])) {
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
