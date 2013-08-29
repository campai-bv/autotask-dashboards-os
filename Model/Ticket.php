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

			// Set the default width of the progress bar to 0. That way the bars appear empty when no tickets have been created or completed.
			$aKillRate = array(
					'created' => $iTicketsCreatedToday
				,	'completed' => $iTicketsCompletedToday
				,	'kill_rate' => 0
				,	'new_progress_width_%' => 0
				,	'killed_progress_width_%' => 0
			);

			// When there are tickets created and/or completed we calculate the proper progress bar width and kill rate percentage.
			if( $iTicketsCreatedToday > 0 || $iTicketsCompletedToday > 0 ) {

				// 1. There are new tickets but no completed ones yet.
				if( $iTicketsCreatedToday > 0 && 0 == $iTicketsCompletedToday ) {

					$aKillRate['new_progress_width_%'] = 100;

				// 2. There are completed tickets but no new ones yet.
				} elseif( $iTicketsCompletedToday > 0 && 0 == $iTicketsCreatedToday ) {

					$aKillRate['killed_progress_width_%'] = 100;
					$aKillRate['kill_rate'] = $iTicketsCompletedToday * 100;

				// 3. There's been quite the activity! Both new and completed tickets are available.
				} elseif( $iTicketsCreatedToday > 0 && $iTicketsCompletedToday > 0 ) {

					$aKillRate['kill_rate'] = number_format( 100 * ( ( ( 100*$iTicketsCompletedToday ) / $iTicketsCreatedToday ) / 100 ), '0', '.', ',' );

					// We're catching up on all those new tickets
					if( $iTicketsCreatedToday > $iTicketsCompletedToday ) {

						$aKillRate['new_progress_width_%'] = 100;
						$aKillRate['killed_progress_width_%'] = 100 * ( ( ( 100*$iTicketsCompletedToday ) / $iTicketsCreatedToday ) / 100 );

					// We're ahead of things, sweet!
					} else {

						$aKillRate['new_progress_width_%'] = 100 * ( ( ( 100*$iTicketsCreatedToday ) / $iTicketsCompletedToday ) / 100 );
						$aKillRate['killed_progress_width_%'] = 100;

					}

				}

			}

			return $aKillRate;

		}




	}