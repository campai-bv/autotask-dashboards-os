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
		, 	'Autotask.SyncTickets'
		,	'Autotask.GetAutotaskObject'
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
		$this->oAutotask = $this->GetAutotaskObject->execute();
		//$this->connectAutotask();
		//$this->checkConnectAutotask();

		$this->log( 'Starting with the import.' );

		// Apparently we can login, so let's get into action!
		// may as well do these first so there are none missing
		// sync picklists
		$this->SyncPicklists->execute();
		$this->SyncTickets->execute();

	}
}