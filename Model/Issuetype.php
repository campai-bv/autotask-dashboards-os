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

	class Issuetype extends AutotaskAppModel {

		public $name = 'Issuetype';

		public $hasMany = array(
			'Autotask.Ticket',
			'Autotask.Issuetypecount',
		);


		public function getTotals(Array $aQueueIds) {

			$aList = array(
				'dates' => array()
			);

			$iDaysToGoBack = Configure::read('Widget.RollingWeek.daysOfHistory')-1;
			if (-1 == $iDaysToGoBack) {
				$iDaysToGoBack = 6;
			}

			$aIssuetypeList = $this->Issuetypecount->find('all', array(
				'fields' => array(
					'DISTINCT (Issuetype.name) AS name',
					'Issuetype.id AS id',
				),
				'order' => array(
					'Issuetype.name ASC',
				),
			));

			if (!empty($aIssuetypeList)) {

				foreach ($aIssuetypeList as $aIssuetype) {

					$aHistory = $this->Issuetypecount->find('all', array(
						'conditions' => array(
							'issuetype_id' => $aIssuetype['Issuetype']['id'],
							'created >=' => date('Y-m-d', strtotime("-" . $iDaysToGoBack . " days")),
							'queue_id' => $aQueueIds,
						),
						'order' => array(
							'created ASC',
						),
					));

					// All history records are kept in an array of their respective issuetype.
					$sIssuetypeName = $aIssuetype['Issuetype']['name'];

					if (!empty($aHistory)) {

						foreach ($aHistory as $aHistoryRecord) {

							// Add the date to the list of available dates.
							// You should only have to do this for the first issuetype - all other issuetype have the same dates (I know, assumption..)
							$sRecordDate = $aHistoryRecord['Issuetypecount']['created'];

							if (!in_array($sRecordDate, $aList['dates'])) {
								$aList['dates'][] = $sRecordDate;
							}

							if (!isset($aList[$sIssuetypeName])) {
								$aList[$sIssuetypeName] = array();
							}
							// End

							// Add the # of tickets for the issuetype to the list.
							if (!isset($aList[$sIssuetypeName][$sRecordDate])) {
								$aList[$sIssuetypeName][$sRecordDate] = 0;
							}

							$aList[$sIssuetypeName][$sRecordDate] += $aHistoryRecord['Issuetypecount']['count'];

						}

					}

					// If we're missing any data (because you've added a new issuetype for example) we fill
					// it up with 0's.
					if (count($aList[$sIssuetypeName]) < count($aList['dates'])) {

						foreach ($aList['dates'] as $sDate) {

							if (!isset($aList[$sIssuetypeName][$sDate])) {
								$aList[$sIssuetypeName][$sDate] = 0;
							}

						}

					}

					ksort($aList[$sIssuetypeName]);

				}

			}

			return $aList;

		}

	}