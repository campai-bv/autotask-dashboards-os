<?php

	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Ticketsourcecount extends AutotaskAppModel {

		public $name = 'Ticketsourcecount';

		public $belongsTo = array(
				'Autotask.Ticketsource'
		);
	}