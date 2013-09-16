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
	class SyncTicketsTask extends Shell {

		public $uses = array(
				'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
			,	'Autotask.Queue'
			,	'Autotask.Ticketstatus'		
			,	'Autotask.Tickets'	
		);

		/**
		 * 

		 * (set the last activity date to x days ago from config)
		 * 
		 * get all tickets with last activity after our last tickets last activity
		 * this should maintain complete integrity, assuming last activity is like a modified db field
		 * if the database is empty, start the initial sync from the start of today
		 * 
		 * @return
		 */
		public function execute() {
			App::uses('CakeTime', 'Utility');
			//echo CakeTime::convert(time(), new DateTimeZone('Asia/Jakarta'));

			$this->__UpdateTicketsFromAutotask();


		}
		private function __UpdateTicketsFromAutotask() {
			// get the entities from autotask
			if ($this->__GetTicketsToSyncFromAutotask() === FALSE) {
				return FALSE;
			}
			// results are insert or replace added
			foreach($this->syncResults as $oTicketEntity) {
				$aModelData = $this->__ConvertEntityResultsToModelArray($oTicketEntity);
				$aNewModelRecords[] = array('Ticket' => $aModelData);
				$this->log('Queued ticket data for sync with tnumber:'.$aModelData['TicketNumber'],4);
			}
		}

		private function __ConvertEntityResultToModelArray($oTicketEntity) {
			foreach($oTicketEntity as $sField => $uValue) {
				// ignore udfs (for now)
				if (!is_array($oTicketEntity->$sField)) {
					$aModelData[$sField] = $uValue;
				}
			}
			return $aModelData;
		}

		private function __SetLastActivityDate() {
			// gets the last update date
			// if not found, works out the date to use
			// from the bootstrap settings
			$this->sSyncFromActivityDate = $this->Ticket->field(
				'LastActivityDateTime', array(), 'LastActivityDateTime DESC');
			
			if (!isset($this->sSyncFromActivityDate)) {
				// lets just start with today for now
				$this->sSyncFromActivityDate = CakeTime::dayAsSql(date('y-m-d'));
				$this->bInitialSync = TRUE; // get all open tickets + tickets modified today
				$this->log('Initial Sync');
			}
			
			$this->log('Sync from LastActivityDate:'.$this->sSyncFromActivityDate);
			
		}
		private function __GetTicketsToSyncFromAutotask( ) {
			if (!isset($this->sSyncFromActivityDate)) {
				$this->__SetLastActivityDate();
			}
			if ($this->bInitialSync === TRUE) {
				$this->query = $this->__GetInitialSyncQuery();
			}
			else {
				$this->query = $this->__GetSyncQuery();
			}
			$this->log('at query:'.$this->query->getQueryXml(),4);
			
			$this->syncResults = $this->oAutotask->getQueryResults($this->query);
			
			if ($this->syncResults === FALSE) {
				return FALSE;
				$this->Log('No tickets to update');
			}
			$this->Log('Updating or adding '.count($this->syncResults.' Tickets'));
			return TRUE;
			
		}
		
		private function __GetSyncQuery() {
			$query = $this->oAutotask->getNewQuery();
			$query->qFROM('Ticket');
			$query->qWHERE('LastActivityDateTime',$query->GreaterThanorEquals,$this->sSyncFromActivityDate);
			return $query;
		}
		
		private function __GetInitialSyncQuery() {
			$query = $this->__GetSyncQuery();
			$query->qWHERE('LastActivityDateTime',$query->GreaterThanorEquals,$this->sSyncFromActivityDate);
			$query->openBracket();
			$query->qOR('LastActivityDateTime',$query->GreaterThanorEquals,$this->sSyncFromActivityDate);
			//@todo: we should have a config table with names of certain identifiers - like completed for closed 
			// tickets
			$query->qAND('Status',$query->NotEquals,$this->oAutotask->getPicklistValueFromName('Completed'));
			return $query;
		}
	}