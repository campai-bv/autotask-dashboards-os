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
	class SyncTimeEntriesTask extends Shell {
		public $uses = array(
			'Autotask.Timeenry'
		);
		public $tasks = array('Autotask.GetAutotaskObject');
		public $aTimeEntryModelMap = array(
			'id'=>array('sync'=>TRUE,'field'=>'id')
			,'TaskID'=>array('sync'=>FALSE)
			,'TicketID'=>array('sync'=>TRUE,'field'=>'ticket_id')
			,'InternalAllocationCodeID'=>array('sync'=>FALSE)
			,'Type'=>array('sync'=>FALSE)
			,'DateWorked'=>array('sync'=>FALSE)
			,'StartDateTime'=>array('sync'=>TRUE,'field'=>'created','dbhook'=>'dateToDb')
			,'EndDateTime'=>array('sync'=>FALSE)
			,'HoursWorked'=>array('sync'=>TRUE,'field'=>'hours_worked')
			,'HoursToBill'=>array('sync'=>TRUE,'field'=>'hours_to_bill')
			,'OffsetHours'=>array('sync'=>TRUE,'field'=>'offset_hours')
			,'SummaryNotes'=>array('sync'=>FALSE)
			,'InternalNotes'=>array('sync'=>FALSE)
			,'RoleID'=>array('sync'=>FALSE)
			,'CreateDateTime'=>array('sync'=>FALSE)
			,'ResourceID'=>array('sync'=>TRUE,'field'=>'resource_id')
			,'CreatorUserID'=>array('sync'=>FALSE)
			,'LastModifiedUserID'=>array('sync'=>FALSE)
			,'LastModifiedDateTime'=>array('sync'=>TRUE,'field'=>'modified','dbhook'=>'dateToDb')
			,'AllocationCodeID'=>array('sync'=>FALSE)
			,'ContractID'=>array('sync'=>FALSE)
			,'ShowOnInvoice'=>array('sync'=>FALSE)
			,'NonBillable'=>array('sync'=>TRUE,'field'=>'non_billable')
			,'BillingApprovalDateTime'=>array('sync'=>FALSE)
			,'BillingApprovalLevelMostRecent'=>array('sync'=>FALSE)
			,'BillingApprovalResourceID'=>array('sync'=>FALSE)
		);
		private $oSyncFromModifiedDate = null;
		private $bInitialSync = false;
		private $syncResults = array();
		/**
		 * 

		 * (set the last modified date to x days ago from config)
		 * 
		 * get all Timeentries with last activity after our last Timeentries last activity
		 * this should maintain complete integrity, assuming last activity is like a modified db field
		 * if the database is empty, start the initial sync from the start of today
		 * 
		 * @return
		 */
		public function execute() {
			$this->log('Starting time entry import',0);
			$this->oAutotask = $this->GetAutotaskObject->execute();
			$this->__UpdateTimeEntriesFromAutotask();
			$this->log('Finished time entry import',0);
		}
		private function __UpdateTimeEntriesFromAutotask($more=false) {
			// get the entities from autotask
			$results = $this->__GetTimeEntriesToSyncFromAutotask($more);
			if ($results === FALSE) {
				$this->log('no time entries to update',0);
				return FALSE;
			}
			// results are insert or replace added
			foreach($results as $oTimeEntryEntity) {
				$aModelData = $this->__ConvertEntityResultsToModelArray($oTimeEntryEntity);
				$this->log($aModelData);
				$aNewModelRecords[] = array('Timeentry' => $aModelData);
				$this->log('Queued time entry data for sync with id:'.$aModelData['id']);
			}
			if (!empty($aNewModelRecords)) {
				// batch write our model changes
				$this->Timeentry->saveAll($aNewModelRecords);
			}
			if (count($results) == 500) {
				$this->log('Running another import run as 500 results returned',0);
				$iLastId = end($results)->id;
				return $this->__UpdateTimeEntriesFromAutotask($iLastId);
			}
			return true;
		}

		// @todo:can probably overload the autotask classmap for time entry 
		// to have a function which returns the model...
		// but that should come later
		private function __ConvertEntityResultsToModelArray($oTimeEntry) {
			$aModelData = false;

			$this->log('Converting time entry id:'.$oTimeEntry->id,3);
			foreach($oTimeEntry as $sField => $uValue) {
				$this->log('checking field:'.$sField);
				// ignore udfs (for now)
				if (is_array($uValue)) {
					$this->log($sField . ' is a UDF field');
					continue;
				}
				if (!isset($this->aTimeEntryModelMap[$sField])) {
					$this->log($sField . ' is not in time entry map');
					// not in TimeEntry map
					continue;
				}
				$map = $this->aTimeEntryModelMap[$sField];
				if (!is_array($map)) {
					$this->log($sField . ' has time entry map entry but not configured correctly');
					// TimeEntry map not configured correctly
					continue;
				}
				if (!isset($map['sync'])) {
					$this->log($sField . ' is not to be synced with database');
					// not syncing
					continue;
				}
				if ($map['sync'] !== TRUE) {
					$this->log($sField . ' is not to be synced with database');
					// not syncing
					continue;
				}
				if (isset($map['dbhook'])){
					$this->log($sField . ' is to be converted with:'.$map['dbhook']);
					$value = $this->$map['dbhook']($uValue);
				}
				else {
					$value = $uValue;
				}
				if (!isset($value)) {
					$value = "";
				}
				if (isset($map['field'])) {
					$aModelData[$map['field']] = $value;	
				}
			}
			return $aModelData;
		}
		private function dateToDb($api_date) {
			if (!isset($this->oApiTimeZone)) {
				$this->oApiTimeZone = new DateTimeZone('US/Eastern');
			}
			if (!isset($this->oLocalTimeZone)) {
				$this->oLocalTimeZone = new DateTimeZone('Europe/London');
			}
			$oDate = date_create($api_date,$this->oApiTimeZone);
			return $oDate->setTimezone($this->oLocalTimeZone)->format("Y-m-d H:i:s");
		}
		private function SetLastModifiedDate() {
			// gets the last update date
			// if not found, gets everything modified for 7 days.
			// and everything not "complete"
			$this->oSyncFromModifiedDate = $this->Timeentry->field('modified',
				array('modified is not null'),'modified DESC');
			
			if(!isset($this->oSyncFromModifiedDate)) {
				$this->oSyncFromModifiedDate = '0000-00-00 00:00:00';
			}
			if($this->oSyncFromModifiedDate == null) {
				$this->oSyncFromModifiedDate = '0000-00-00 00:00:00';
			}
			if ($this->oSyncFromModifiedDate == '0000-00-00 00:00:00') {
				// lets just start 7 days ago for now
				
				$this->oSyncFromModifiedDate = date_create(date_create()->format('Y-m-d 00:00:00'));// start of today
				$this->oSyncFromModifiedDate->sub(new DateInterval('P7D'));
				$this->bInitialSync = TRUE; // get all open Timeentries + Timeentries modified today
				$this->log('Initial Sync');
			}
			if (! $this->oSyncFromModifiedDate instanceof DateTime) {
				// it's from the DB'
				$this->oSyncFromModifiedDate = date_create($this->oSyncFromModifiedDate);
			}
			$this->log('Sync from modified:'.$this->oSyncFromModifiedDate->format('Y-m-d H:i:s'));
		}
		private function __GetTimeEntriesToSyncFromAutotask($more=false) {
			if ($more===false) {
				if (!isset($this->oSyncFromModifiedDate)) {
					$this->SetLastModifiedDate();
				}
				if ($this->bInitialSync === TRUE) {
					$this->query = $this->__GetInitialSyncQuery();
				}
				else {
					$this->query = $this->__GetSyncQuery();
				}
			}
			else {
				$this->query->qField('id',$this->query->GreaterThan,$more);
			}
			$this->log('at query:'.$this->query->getQueryXml(),3);
			$results = $this->oAutotask->getQueryResults($this->query);			
			if ($results === FALSE) {
				$this->log($this->oAutotask->getLastQueryError());
				$this->log($this->oAutotask->getLastQueryFault());
				$this->Log('No time entries to update',0);
				return FALSE;
			}
			$this->Log('Retrieved '.count($results).' time entries',3);
			return $results;
		}
		
		private function __GetSyncQuery() {
			$query = $this->oAutotask->getNewQuery();
			$query->qFROM('TimeEntry');
			$query->qField('LastModifiedDateTime',$query->GreaterThanorEquals,$this->oSyncFromModifiedDate);
			return $query;
		}
		
		private function __GetInitialSyncQuery() {
			$query = $this->__GetSyncQuery();
			return $query;
		}
		public function log($sMessage,$iLevel = 5) {
			if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
				$this->iLogLevel = 4;
				parent::log('log level set to:'.$this->iLogLevel,'cronjob');
			}			
			if( $iLevel <= $this->iLogLevel ) {
				parent::log($sMessage, 'cronjob');	
			}
		}
	}
