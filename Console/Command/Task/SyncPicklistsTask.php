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
	class SyncPicklistsTask extends Shell {

		public $uses = array(
				'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
			,	'Autotask.Queue'
			,	'Autotask.Ticketstatus'			
		);
		public $tasks = array('Autotask.GetAutotaskObject');
		/**
		 * Updates or inserts new picklist entries
		 * 
		 * @return
		 */
		public function execute() {
			$this->oAutotask = $this->GetAutotaskObject->execute();
			$this->__syncPicklistWithDatabase();

		}

		public function __syncPicklistWithDatabase() {
			$aIssueTypes = $this->__getAutotaskPicklist( 'Ticket', 'IssueType' );
			$aSubissueTypes = $this->__getAutotaskPicklist('Ticket','SubIssueType');
			$aQueues = $this->__getAutotaskPicklist('Ticket','QueueID');
			$aTicketstatus = $this->__getAutotaskPicklist('Ticket','Status');
			
			$this->__savePicklistToModel('Issuetype',$aIssueTypes);
			$this->__savePicklistToModel('Subissuetype',$aSubissueTypes);
			$this->__savePicklistToModel('Queue',$aQueues);
			$this->__savePicklistToModel('Ticketstatus',$aTicketstatus);

		}
		public function __savePicklistToModel($sModel,$aPicklist) {
			if(!is_array($aPicklist)) {
				return false;
			}
			$aNewModelRecords = array();
			foreach ($aPicklist as $iId=>$sName) {
				$this->log('checking model:'.$sModel.' for name:'.$sName,4);
				$aModelRecord = $this->$sModel->findByid($iId);
				if (empty($aModelRecord)) {
					$this->log('non existing:'.$sModel.' model so inserting:'.$sName.' with id:'.$iId,3);
					$aNewModelRecords[] = array($sModel=>array('id'=>$iId,'name'=>$sName));
				}
				else {
					if (empty($aModelRecord[$sModel]['name'])) {
						$this->log('updating '.$sModel.' with id:'.$iId.' which does not have a name. New name:'.$sName,3);
						$aNewModelRecords[]=array($sModel=>array('id'=>$iId,'name'=>$sName));
					}
					else {
						// allow dashboard settings to change name of picklist item.
						// set back to empty to resync on next cronjob run
						$this->log($sModel.':'.$sName.' exists and has name:'.$aModelRecord[$sModel]['name'],4);
					}
				}
			}
			
			if (!empty($aNewModelRecords)) {
				// batch write our model changes
				$this->$sModel->saveAll($aNewModelRecords);
			}
		}

		public function __getAutotaskPicklist( $sEntity, $sPicklist ) {
			if($this->oAutotask->connected() !== true ) {
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
		public function log($sMessage,$iLevel = 0) {
			if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
				$this->iLogLevel = 4;
				parent::log('log level set to:'.$this->iLogLevel,'cronjob');
			}			
			if( $iLevel <= $this->iLogLevel ) {
				parent::log($sMessage, 'cronjob');	
			}
		}
	}
