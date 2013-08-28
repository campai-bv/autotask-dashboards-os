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
	class AutotaskBehavior extends ModelBehavior {

		private $_iLastId = 0;
		private $_aResults = array();
		private $_aPicklistResult = array();
		private $_aPicklist = array();
		
		public function __construct() {
			if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
				$this->iLogLevel = 0;
			}

		}

		public function getAutotaskPicklist( Model $oModel, $sEntity, $sPicklist ) {
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

		public function queryAutotask( Model $oModel, $sEntity, Array $aQuery ) {

			if ($this->connectAutotask() !== true) {
				return false;
			}
			$sXML = '
				<queryxml>
					<entity>' . $sEntity . '</entity>
					<query>
			';

			if( !empty( $aQuery['conditions'] ) ) {

				// Every query consists of a set of conditions ($aDetails) grouped by the equality ($sEquality),
				// i.e. 'Equals' or 'NotEqual'.
				foreach ( $aQuery['conditions'] as $sEquality => $aDetails ) {

					// Every field gets transformed to a proper XML string, i.e.
					// QueueID ($sField) equals ($sEquality) 2233932 ($sExpression).
					foreach ( $aDetails as $sField => $mExpressions ) {

						// Multiple values to match.
						if( is_array( $mExpressions ) ) {

							$sXML .= '<condition>';

								foreach ( $mExpressions as $iKey => $sExpression ) {

									if( 0 == $iKey ) {
										$sXML .= '<condition>';
									} else {
										$sXML .= '<condition operator="OR">';
									}
										$sXML .= '<field>' . $sField . '<expression op="' . $sEquality . '">' . $sExpression . '</expression></field>';
									$sXML .= '</condition>';

								}

							$sXML .= '</condition>';

						// Just one value to match.
						} else {

							$sXML .= '<condition>';
								$sXML .= '<field>' . $sField . '<expression op="' . $sEquality . '">' . $mExpressions . '</expression></field>';
							$sXML .= '</condition>';

						}

					}

				}

			}

			$sXML .= '
					</query>
				</queryxml>
			';

			try {
				$oResponse = $this->oAutotask->client->query( array( 'sXML' => $sXML ) );
			} catch ( SoapFault $fault ) {
				$this->log( ' - Error occured while performing query: "' . $fault->faultcode .' - ' . $fault->faultstring . '"', 'cronjob' );
				return false;
			}

			if( !empty( $oResponse->queryResult->EntityResults->Entity ) ) {

				// Only 1 result returned.
				if( 1 == count( $oResponse->queryResult->EntityResults->Entity ) ) {

					if( !empty( $oResponse->queryResult->EntityResults->Entity->id ) ) {
						$this->_aResults = $oResponse->queryResult->EntityResults->Entity;
						$this->_iLastId = $oResponse->queryResult->EntityResults->Entity->id;
					}

				// Multiple results returned.
				} else {

					foreach ( $oResponse->queryResult->EntityResults->Entity as $oEntry ) {

						if( !empty( $oEntry->id ) ) {

							$this->_aResults[] = $oEntry;
							$this->_iLastId = $oEntry->id;

						}

					}

				}

				// The API has a limit of 500 results per request. If you get 500 there's probably some more
				// to fetch :-)
				if( 500 == count( $oResponse->queryResult->EntityResults->Entity ) ) {

					$this->log( ' - Whoa, that\'s quite the amount of entries! Going for another 500, hang on..', 'cronjob' );
					$aQuery['conditions']['GreaterThan']['id'] = $this->_iLastId;
					return $this->queryAutotask( $oModel, $sEntity, $aQuery );

				} else {

					$aCompleteResults = $this->_aResults;

					$this->_iLastId = 0;
					$this->_aResults = array();

					return $aCompleteResults;

				}

			} else {
				return array();
			}

		}

		private function getAutotaskLogin() {
			
			$aLogin = array(
					'login' => Configure::read( 'Autotask.username' )
				,	'password' => Configure::read( 'Autotask.password' )
				,	'location' => Configure::read( 'Autotask.asmx' )
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
			App::uses('atws','Vendor');
			$this->oAutotask = new atws();
			if ($this->oAutotask->connect($aLogin['location'],$aLogin['username'],$aLogin['password'])) {
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