<?php

	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Ticketsource extends AutotaskAppModel {

		public $name = 'Ticketsource';

		public $hasMany = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketsourcecount'
		);


		
		public function getTotals(Array $aQueueIds) {

			$aList = array(
					'dates' => array()
			);

			$iDaysToGoBack = Configure::read('Widget.RollingWeek.daysOfHistory')-1;
			if (-1 == $iDaysToGoBack) {
				$iDaysToGoBack = 6;
			}

			$aSourceList = $this->Ticketsourcecount->find('all', array(
					'fields' => array(
						'DISTINCT (Ticketsource.name) AS name'
					,	'Ticketsource.id AS id')
				,	'order' => array(
							'Ticketsource.name ASC'
					)
			));

			if (!empty($aSourceList)) {

				foreach ($aSourceList as $aSource) {

					$aHistory = $this->Ticketsourcecount->find('all', array(
							'conditions' => array(
									'ticketsource_id' => $aSource['Ticketsource']['id']
								,	'created >=' => date('Y-m-d', strtotime("-" . $iDaysToGoBack . " days"))
								,	'queue_id' => $aQueueIds
							)
						,	'order' => array(
									'created ASC'
							)
					));

					if (!empty($aHistory)) {

						foreach ($aHistory as $aHistoryRecord) {

							// Add the date to the list of available dates.
							// You should only have to do this for the first source - all other sources have the same dates (I know, assumption..)
							$sRecordDate = $aHistoryRecord['Ticketsourcecount']['created'];

							if (!in_array($sRecordDate, $aList['dates'])) {
								$aList['dates'][] = $sRecordDate;
							}
							// End

							// Add the # of tickets for the source to the list.
							$sSourceName = $aHistoryRecord['Ticketsource']['name'];

							if (!isset($aList[$sSourceName][$sRecordDate])) {

								$aList[$sSourceName][$sRecordDate] = 0;

							}

							$aList[$sSourceName][$sRecordDate] += $aHistoryRecord['Ticketsourcecount']['count'];
							// End

						}

					}

				}

			}

			return $aList;

		}

	}