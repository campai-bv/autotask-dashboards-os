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

	class Resource extends AutotaskAppModel {

		public $name = 'Resource';

		public $actsAs = array(
				'Autotask.Autotask'
		);

		public $hasMany = array(
				'Autotask.Ticket'
			,	'Autotask.Timeentry'
		);


		/**
		 * @param  string $sType  'all'
		 * @param  array  $aConditions [description]
		 * 
		 * @return object
		 */
		public function findInAutotask($sType = 'all', $aConditions = array()) {

			$aQuery = array(
				'queryxml' => array(
						'entity' => 'Resource',
						'query' => array(
								'condition' => array()
						)
				)
			);

			$aQuery['queryxml']['query']['condition'] = array_merge($aQuery['queryxml']['query']['condition'], $aConditions);

			switch ($sType) {

				case 'all':
				default:

					$aQuery['queryxml']['query']['condition'][] = array(
							'@operator' => 'AND',
							'field' => array(
									'expression' => array(
											'@op' => 'equals',
											'@' => 1
									),
									'@' => 'Active'
							)
					);

				break;

			}

			return $this->queryAutotask($aQuery);

		}


		public function getTotals($aQueueIds = array(), $aResourceIds = array()) {

			$aResourceTotals = array(
					'time_totals' => array(
							'hours_to_bill' => 0
						,	'hours_worked' => 0
					)
				,	'Resource' => array()
			);

			// First we take care of the resources,
			// in the end we check the unassigned tickets.
			if (!empty($aResourceIds)) {

				$aResources = $this->find('all', array(
						'conditions' => array(
								'Resource.id' => $aResourceIds
						)
					,	'contain' => array(
								'Ticket' => array(
										'Timeentry'
									,	'conditions' => array(
												'Ticket.queue_id' => $aQueueIds
										)
								)
						)
				));

			} else {

				$aResources = $this->find('all', array(
						'contain' => array(
								'Ticket' => array(
										'Timeentry'
									,	'conditions' => array(
												'Ticket.queue_id' => $aQueueIds
										)
								)
						)
				));

			}

			foreach ($aResources as $aResource) {

				$iTotalDaysOpen = 0;
				$iTicketsToDivideBy = 0;
				$iTicketsClosedToday = 0;
				$aTimeTotals = array(
						'hours_to_bill' => 0
					,	'hours_worked' => 0
				);

				foreach ($aResource['Ticket'] as $aTicket) {

					if (5 != $aTicket['ticketstatus_id']) { // Not completed

						$start = strtotime($aTicket['created']);
						$end = strtotime(date('Y-m-d h:I:s'));
						$iTotalDaysOpen += round(abs($end-$start)/86400,0);
						$iTicketsToDivideBy += 1;

					// Closed/completed today
					} else {

						if (stristr($aTicket['completed'], date('Y-m-d'))) {
							$iTicketsClosedToday ++;
						}

					}

				}

				if (0 == $iTicketsToDivideBy) {

					$aResourceTotals['Resource'][$aResource['Resource']['id']] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => 0
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => 0
						,	'time_totals' => $aTimeTotals
					);

				} else {

					$aResourceTotals['Resource'][$aResource['Resource']['id']] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => $iTicketsToDivideBy
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => number_format($iTotalDaysOpen/$iTicketsToDivideBy, 0, ',', '.')
						,	'time_totals' => $aTimeTotals
					);

				}
				
				// Calculate the time spent
				$aResourceTimeEntries = array();
				$aFilteredResult = Hash::extract($aResource['Ticket'], '{n}.Timeentry');

				foreach ($aFilteredResult as $aTimeEntries) {
					if (!empty($aTimeEntries)) {
						$aResourceTimeEntries = array_merge($aResourceTimeEntries, $aTimeEntries);
					}
				}

				if (!empty($aResourceTimeEntries)) {

					foreach ($aResourceTimeEntries as $iKey => $aTimeEntry) {

						$aTimeTotals['hours_worked'] += $aTimeEntry['hours_worked'];
						$aResourceTotals['time_totals']['hours_worked'] += $aTimeEntry['hours_worked'];

						if (0 == $aTimeEntry['non_billable']) {

							// Don't use hours_to_bill - Autotask calculates totals in a weird way :S
							$aTimeTotals['hours_to_bill'] += $aTimeEntry['hours_worked'];
							$aResourceTotals['time_totals']['hours_to_bill'] += $aTimeEntry['hours_worked'];

						}

					}

				}
				// End

				if (0 == $iTicketsToDivideBy) {

					$aResourceTotals['Resource'][$aResource['Resource']['id']] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => 0
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => 0
						,	'time_totals' => $aTimeTotals
					);

				} else {

					$aResourceTotals['Resource'][$aResource['Resource']['id']] = array(
							'name' => $aResource['Resource']['name']
						,	'count' => $iTicketsToDivideBy
						,	'closed' => $iTicketsClosedToday
						,	'average_days_open' => number_format($iTotalDaysOpen/$iTicketsToDivideBy, 0, ',', '.')
						,	'time_totals' => $aTimeTotals
					);

				}

			}
			// End

			$aResourceTotals['Resource'] = Hash::sort($aResourceTotals['Resource'], '{n}.time_totals.hours_worked', 'desc', 'numeric');
			return $aResourceTotals;

		}

	}