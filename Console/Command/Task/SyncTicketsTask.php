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
			,	'Autotask.Ticket'	
		);
		public $tasks = array('Autotask.GetAutotaskObject');
		public $aTicketModelMap = array(
			'id'=>array(TRUE,'field'=>'id')
			,'AccountID'=>array('sync'=>TRUE,'field'=>'account_id')
			,'AllocationCodeID'=>array('sync'=>FALSE)
			,'CompletedDate'=>array('sync'=>TRUE,'field'=>'completed','dbhook'=>'dateToDb')
			,'ContactID'=>array('sync'=>FALSE)
			,'ContractID'=>array('sync'=>FALSE)
			,'CreateDate'=>array('sync'=>TRUE,'field'=>'created','dbhook'=>'dateToDb')
			,'CreatorResourceID'=>array('sync'=>FALSE)
			,'Description'=>array('sync'=>FALSE)
			,'DueDateTime'=>array('sync'=>TRUE,'field'=>'due','dbhook'=>'dateToDb')
			,'EstimatedHours'=>array('sync'=>FALSE)
			,'InstalledProductID'=>array('sync'=>FALSE)
			,'IssueType'=>array('sync'=>TRUE,'field'=>'issuetype_id')
			,'LastActivityDate'=>array('sync'=>TRUE,'field'=>'last_activity','dbhook'=>'datetoDb')
			,'Priority'=>array('sync'=>TRUE,'field'=>'priority')
			,'QueueID'=>array('sync'=>TRUE,'field'=>'queue_id')
			,'AssignedResourceID'=>array('sync'=>TRUE,'field'=>'resource_id')
			,'AssignedResourceRoleID'=>array('sync'=>FALSE)
			,'Source'=>array('sync'=>TRUE,'field'=>'ticketsource_id')
			,'Status'=>array('sync'=>TRUE,'field'=>'ticketstatus_id')
			,'SubIssueType'=>array('sync'=>TRUE,'field'=>'subissuetype_id')
			,'TicketNumber'=>array('sync'=>TRUE,'field'=>'number')
			,'Title'=>array('sync'=>TRUE,'field'=>'title')
			,'FirstResponseDateTime'=>array('sync'=>FALSE)
			,'ResolutionPlanDateTime'=>array('sync'=>FALSE)
			,'ResolvedDateTime'=>array('sync'=>FALSE)
			,'FirstResponseDueDateTime'=>array('sync'=>FALSE)
			,'ResolutionPlanDueDateTime'=>array('sync'=>FALSE)
			,'ResolvedDueDateTime'=>array('sync'=>FALSE)
			,'ServiceLevelAgreementID'=>array('sync'=>FALSE)
			,'ServiceLevelAgreementHasBeenMet'=>array('sync'=>TRUE,'field'=>'has_met_sla')
			,'Resolution'=>array('sync'=>FALSE)
			,'PurchaseOrderNumber'=>array('sync'=>FALSE)
			,'TicketType'=>array('sync'=>FALSE)
		);
		private $bInitialSync = false;
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
			$this->oAutotask = $this->GetAutotaskObject->execute();
			$this->__UpdateTicketsFromAutotask();
		}
		private function __UpdateTicketsFromAutotask() {
			// get the entities from autotask
			if ($this->__GetTicketsToSyncFromAutotask() === FALSE) {
				$this->log('not tickets to update');
				return FALSE;
			}
			// results are insert or replace added
			foreach($this->syncResults as $oTicketEntity) {
				$aModelData = $this->__ConvertEntityResultsToModelArray($oTicketEntity);
				$aNewModelRecords[] = array('Ticket' => $aModelData);
				$this->log('Queued ticket data for sync with tnumber:'.$aModelData['TicketNumber'],4);
			}
		}

		// @todo:can probably overload the autotask classmap for ticket 
		// to have a function which returns the model...
		// but that should come later
		private function __ConvertEntityResultsToModelArray($oTicketEntity) {
			foreach($oTicketEntity as $sField => $uValue) {
				// ignore udfs (for now)
				if (is_array($sField)) {
					break;
				}
				if (!isset($this->aTicketModelMap[$sField])) {
					// not in ticket map
					break;	
				}
				$map = $this->aTicketModelMap[$sField];
				if (!is_array($map)) {
					// ticket map not configured correctly
					break;
				}
				if (!isset($map['sync'])) {
					// not syncing
					break;
				}
				if ($this->aTicketModelMap[$sField]['sync'] === TRUE)
				if (isset($this->aTicketModelMap[$sField]['dbhook'])){
					$value = $this->aTicketModelMap[$sField]['dbhook']($uValue);
				}
				else {
					$value = $uValue;
				}
				$aModelData[$this->aTicketModelMap[$sField]['field']] = $value;

			}
			return $aModelData;
		}
		private function __dateToDb($api_date) {
			if (!isset($this->oApiTimeZone)) {
				$this->oApiTimeZone = new DateTimeZone('US/Eastern');
			}
			$oDate = date_create($api_date,$this->oApiTimeZone);
		}
		private function __SetLastActivityDate() {
			// gets the last update date
			// if not found, works out the date to use
			// from the bootstrap settings
			$this->oSyncFromActivityDate = $this->Ticket->field('last_activity',
				array('last_activity is not null'),'last_activity DESC');
			
			
			if ($this->oSyncFromActivityDate == '0000-00-00 00:00:00') {
				// lets just start with today for now
				$this->oSyncFromActivityDate = date_create(date_create()->format('Y-m-d 00:00:00'));// start of today
				$this->bInitialSync = TRUE; // get all open tickets + tickets modified today
				$this->log('Initial Sync');
			}
			if (! $this->oSyncFromActivityDate instanceof DateTime) {
				$this->oSyncFromActivityDate = date_create($this->oSyncFromActvityDate);
			}
		
			$this->log('Sync from LastActivityDate:'.$this->oSyncFromActivityDate->format('Y-m-d H:i:s'));
			
		}
		private function __GetTicketsToSyncFromAutotask() {
			if (!isset($this->oSyncFromActivityDate)) {
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
				
				$this->log($this->oAutotask->getLastQueryError());
				$this->log($this->oAutotask->getLastQueryFault());
				$this->Log('No tickets to update');
				return FALSE;
			}
			$this->Log('Updating or adding '.count($this->syncResults.' Tickets'));
			return TRUE;
			
		}
		
		private function __GetSyncQuery() {
			$query = $this->oAutotask->getNewQuery();
			$query->qFROM('Ticket');
			$query->qField('LastActivityDate',$query->GreaterThanorEquals,$this->oSyncFromActivityDate);
			return $query;
		}
		
		private function __GetInitialSyncQuery() {
			$complete=$this->oAutotask->getPicklistValueFromName('Ticket','Status','Complete');
			$query = $this->__GetSyncQuery();
			$query->openBracket('OR');
			//@todo: we should have a config table with names of certain identifiers - like completed for closed 
			// tickets
			if ($complete !== false) {
				$query->qField('Status',$query->NotEqual,$complete);
			}
			$query->closeBracket();
			return $query;
		}
	}
