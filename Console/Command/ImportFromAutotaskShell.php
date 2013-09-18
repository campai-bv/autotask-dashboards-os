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

	public $tasks = array(
		//	'Autotask.TimeConverter'
		//,	'Autotask.GetTicketsCompletedToday'
		//,	'Autotask.GetTicketsOpenToday'
		//,	'Autotask.CalculateTotalsByTicketStatus'
		//,	'Autotask.CalculateTotalsForKillRate'
		//,	'Autotask.CalculateTotalsForQueueHealth'
		//,	'Autotask.CalculateTotalsForTimeEntries'
			'Autotask.SyncPicklists'
		, 	'Autotask.SyncTickets'
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
		
		$this->log( 'Starting with the import.' );

		$this->SyncPicklists->execute();
		$this->SyncTickets->execute();


	}
}