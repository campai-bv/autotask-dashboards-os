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
	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Ticket extends AutotaskAppModel {

		public $name = 'Ticket';

		public $actsAs = array(
				'Autotask.Autotask'
		);

		public $belongsTo = array(
				'Autotask.Resource'
			,	'Autotask.Ticketstatus'
			,	'Autotask.Queue'
			,	'Autotask.Account'
			,	'Autotask.Issuetype'
			,	'Autotask.Subissuetype'
		);

		public $hasMany = array(
				'Autotask.Timeentry'
		);

		/**
		 * 
		 * @param  string $sType  'open', 'waitingCustomer' 
		 * @param  array  $aQuery [description]
		 * 
		 * @return object
		 */
		public function findInAutotask( $sType = 'open', $aQuery = array() ) {

			switch ( $sType ) {

				case 'open':
					return $this->_findOpenInAutotask( $aQuery );
				break;

				case 'closed':
					return $this->_findClosedInAutotask( $aQuery );
				break;

				case 'waitingCustomer':
					return $this->_findWaitingCustomerInAutotask( $aQuery );
				break;

				default:
					return false;
				break;
			}

		}


		public function getUnassignedTotals( $aQueueIds = array() ) {

			if( !empty( $aQueueIds ) ) {

				$aUnassignedTickets = $this->find( 'all', array(
						'conditions' => array(
								'Ticket.queue_id' => $aQueueIds
							,	'Ticket.resource_id' => 0
							,	'Ticket.ticketstatus_id !=' => 5 // Completed
						)
				) );

			} else {

				$aUnassignedTickets = $this->find( 'all', array(
						'conditions' => array(
								'Ticket.resource_id' => 0
							,	'Ticket.ticketstatus_id !=' => 5 // Completed
						)
				) );

			}

			$iTotalDaysOpen = 0;

			foreach ( $aUnassignedTickets as $aTicket ) {

				$start = strtotime( $aTicket['Ticket']['created'] );
				$end = strtotime( date( 'Y-m-d h:I:s' ) );
				$iTotalDaysOpen += round( abs( $end - $start ) / 86400,0 );

			}

			if( 0 == count( $aUnassignedTickets ) ) {
				
				$aUnassignedTotals = array(
						'name' => 'Unassigned'
					,	'count' => 0
					,	'average_days_open' => 0
				);

			} else {

				$aUnassignedTotals = array(
						'name' => 'Unassigned'
					,	'count' => count( $aUnassignedTickets )
					,	'average_days_open' => number_format( $iTotalDaysOpen/ count( $aUnassignedTickets ), 0, ',', '.' )
				);

			}

			
			// End
			
			return $aUnassignedTotals;

		}


		public function getAtes( $aQueueIds = array() ) {

			$aATESIds = array(
					0
			);

			// Backwards compatibility, will become deprecated in the future.
			$iATESId = Configure::read( 'ATES.subIssueTypeId' );

			if( !empty( $iATESId ) ) {
				$aATESIds[] = $iATESId;
			}
			// End

			// Version 1.1.1 supports an array of sub issue types
			$aAdditionalATESIds = Configure::read( 'MIT.subIssueTypeIds' );

			if( !empty( $aAdditionalATESIds ) ) {
				$aATESIds = array_merge( $aATESIds, $aAdditionalATESIds );
			}
			// End

			$aTickets = array();
			$aATESTickets = $this->find( 'all', array(
					'conditions' => array(
							'Ticket.subissuetype_id' => $aATESIds
					)
			) );

			if( !empty( $aQueueIds ) ) {

				if( !empty( $aATESTickets ) ) {

					foreach ( $aATESTickets as $aTicket ) {
						
						if( in_array( $aTicket['Ticket']['queue_id'], $aQueueIds ) ) {
							$aTickets[] = $aTicket;
						}

					}

				}

			} else {
				$aTickets = $aATESTickets;
			}

			return count( $aTickets );

		}


		public function getSLAViolations( $aQueueIds = array() ) {

			if( !empty( $aQueueIds ) ) {

				$iSLAViolationTickets = $this->find( 'count', array(
						'conditions' => array(
								'Ticket.queue_id' => $aQueueIds
							,	'Ticket.has_met_sla !=' => 1
							,	'Ticket.ticketstatus_id !=' => 5
						)
				) );

			} else {

				$iSLAViolationTickets = $this->find( 'count', array(
						'conditions' => array(
								'Ticket.has_met_sla !=' => 1
							,	'Ticket.ticketstatus_id !=' => 5
						)
				) );

			}

			$aSLAViolationTotals = array(
					'name' => 'SLA Violations'
				,	'count' => $iSLAViolationTickets
			);
			// End
			
			return $aSLAViolationTotals;

		}


		public function getKillRate( $aQueueIds = array() ) {

			$aConditions['created'] = array(
					'Ticket.created >=' => date( 'Y-m-d' )
			);

			$aConditions['completed'] = array(
					'Ticket.completed >=' => date( 'Y-m-d' )
			);

			if( !empty( $aQueueIds ) ) {

				$aConditions['created']['Ticket.queue_id'] = $aQueueIds;
				$aConditions['completed']['Ticket.queue_id'] = $aQueueIds;

			}

			$iTicketsCreatedToday = $this->find( 'count', array(
					'conditions' => $aConditions['created']
			) );

			$iTicketsCompletedToday = $this->find( 'count', array(
					'conditions' => $aConditions['completed']
			) );

			$iFullWidth = 100;

			$iKillRateDivider = $iTicketsCreatedToday;
			if( 0 == $iTicketsCreatedToday ) {
				$iKillRateDivider = 1;
			}

			$aKillRate = array(
					'created' => $iTicketsCreatedToday
				,	'completed' => $iTicketsCompletedToday
				,	'kill_rate' => number_format( ( ( 100*$iTicketsCompletedToday ) / $iKillRateDivider ), 0, ',', '.' )
				,	'new_progress_width_%' => $iFullWidth
				,	'killed_progress_width_%' => $iFullWidth
			);

			if( 0 == $iTicketsCompletedToday ) {
				$iTicketsCompletedToday = 1;
			}

			if( 0 == $iTicketsCreatedToday ) {
				$iTicketsCreatedToday = 1;
			}

			if( $iTicketsCreatedToday > $iTicketsCompletedToday ) {
				$aKillRate['killed_progress_width_%'] = $iFullWidth * ( ( ( 100*$iTicketsCompletedToday ) / $iTicketsCreatedToday ) / 100 );
			} else {
				$aKillRate['new_progress_width_%'] = $iFullWidth * ( ( ( 100*$iTicketsCreatedToday ) / $iTicketsCompletedToday ) / 100 );
			}

			return $aKillRate;

		}


		private function _findOpenInAutotask( Array $aQuery ) {

			$aConditions = array(
					'NotEqual' => array(
							'Status' => 5
					)
			);

			if( !empty( $aQuery['conditions'] ) ) {
				$aQuery['conditions'] = array_merge_recursive( $aQuery['conditions'], $aConditions );
			} else {
				$aQuery['conditions'] = $aConditions;
			}

			return $this->queryAutotask( 'Ticket', $aQuery );

		}


		private function _findClosedInAutotask( Array $aQuery ) {

			$aConditions = array(
					'Equals' => array(
							'Status' => 5
					)
			);

			if( !empty( $aQuery['conditions'] ) ) {
				$aQuery['conditions'] = array_merge_recursive( $aQuery['conditions'], $aConditions );
			} else {
				$aQuery['conditions'] = $aConditions;
			}

			return $this->queryAutotask( 'Ticket', $aQuery );

		}


		private function _findWaitingCustomerInAutotask( Array $aQuery ) {

			$aConditions = array(
					'Equals' => array(
							'Status' => 7
					)
			);

			if( !empty( $aQuery['conditions'] ) ) {
				$aQuery['conditions'] = array_merge_recursive( $aQuery['conditions'], $aConditions );
			} else {
				$aQuery['conditions'] = $aConditions;
			}

			return $this->queryAutotask( 'Ticket', $aQuery );

		}

	}