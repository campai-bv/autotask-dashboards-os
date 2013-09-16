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

//App::import('Vendor', 'Autotask.atws', true, array(), 'atws'.DA.'php-atws.php');
require_once(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'Vendor'.DIRECTORY_SEPARATOR.'atws'.DIRECTORY_SEPARATOR.'php-atws.php');

class ImportFromAutotaskShell extends AppShell {

	public $uses = array(
			'Autotask.Ticket'
		,	'Autotask.Resource'
		,	'Autotask.Ticketstatus'
		,	'Autotask.Queue'
		,	'Autotask.Account'
		,	'Autotask.Issuetype'
		,	'Autotask.Subissuetype'
	);

	public $tasks = array(
			'Autotask.TimeConverter'
		,	'Autotask.GetTicketsCompletedToday'
		,	'Autotask.GetTicketsOpenToday'
		,	'Autotask.CalculateTotalsByTicketStatus'
		,	'Autotask.CalculateTotalsForKillRate'
		,	'Autotask.CalculateTotalsForQueueHealth'
		,	'Autotask.CalculateTotalsForTimeEntries'
		,	'Autotask.SyncPicklists'
	);


	public function log($sMessage,$iLevel = 0) {
		if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
			$this->iLogLevel = 0;
			parent::log('log level set to:'.$this->iLogLevel,'cronjob');
		}			
		if( $iLevel <= $this->iLogLevel ) {
			parent::log($sMessage, 'cronjob');	
		}
	}
	public function main() {
		
		$bErrorsEncountered = false;
		$this->connectAutotask();
		$this->checkConnectAutotask();

		$this->log( 'Starting with the import.' );


		// First we must make sure we can login. 
		//We do this by performing an inexpensive call and see what it returns.
		if( false === $this->connectAutotask() ) {
			$this->log('Failed to connect to autotask');
			return;
		} 

		// Apparently we can login, so let's get into action!
		// may as well do these first so there are none missing
		// sync picklists
		$this->SyncPicklists->execute();




	}



// finished below


	public function getAutotaskPicklist( $sEntity, $sPicklist ) {
		if($this->connectAutotask() !== true ) {
			return false;
		}
		$aPicklistResult = $this->oAutotask->getPicklist($sEntity, $sPicklist);
		
		if(is_array($aPicklistResult)) {
			return $aPicklistResult;
		}
		else {
			$this->log('failed to get picklist:'.$sPicklist.' for entity:'.$sEntity);
			if(isset($this->oAutotask->last_picklist_fault)) {
				$this->log('soapfault:'.$this->oAutotask->last_picklist_fault);
			}
			return false;
		}
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
