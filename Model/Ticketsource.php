<?php

	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Ticketsource extends AutotaskAppModel {

		public $name = 'Ticketsource';

		public $hasMany = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketsourcecount'
		);


		
		public function getTotals(Array $aQueueIds) {

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

			$count = count($aSourceList);

			$newArray = array();
			
			for ($x=0;$x<$count;$x++) {

				$aHistory = $this->Ticketsourcecount->find('all', array(
					'conditions' => array(
							'ticketsource_id' => $aSourceList[$x]['Ticketsource']['id']
						,	'created >=' => date('Y-m-d', strtotime("-" . $iDaysToGoBack . " days"))
						,	'queue_id' => $aQueueIds
					)
				,	'order' => array(
							'created ASC'
					)
				));

				if (!empty($aHistory)) {
					array_unshift($newArray,$aHistory);
				}

			}

			$aList = array();

			for ($x=0;$x<$count;$x++){

				for ($y=0;$y<count($newArray[0]);$y++){

					$aList['dates'][$y] = $newArray[$x][$y]['Ticketsourcecount']['created'];
					if ($y==0){
						$aList[$x][$y] = $newArray[$x][$y]['Ticketsource']['name'];
					}

					$aList[$x][$y+1] = $newArray[$x][$y]['Ticketsourcecount']['count'];

				}

			}

			// End
			return $aList;

		}

	}