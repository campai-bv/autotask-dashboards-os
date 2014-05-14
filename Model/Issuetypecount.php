<?php
	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Issuetypecount extends AutotaskAppModel {

		public $name = 'Issuetypecount';

		public $belongsTo = array(
			'Autotask.Issuetype',
		);

	}