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
	class TimeConverterTask extends Shell {

		public function execute() {
		}


		/**
		 * Converts a date from the Autotask API to your own timezone.
		 * @param  string $sDate - Date from the Autotask API
		 * @param  string $sFormat - The format you want to have returned.
		 * @return string - Reformatted date
		 */
		public function convertToOwnTimezone( $sDate, $sFormat = 'Y-m-d H:i:s' ) {

			$oDate = new DateTime( $sDate, new DateTimeZone( 'EST' ) );
			$oDate->setTimezone( new DateTimeZone( date_default_timezone_get() ) );
			return $oDate->format( $sFormat );

		}


		/**
		 * Converts a date from your own timezone to the one used by the Autotask API.
		 * @param  string $sDate - Date in your own timezone
		 * @param  string $sFormat - The format you want to have returned.
		 * @return string - Reformatted date
		 */
		public function convertToAutotaskTimezone( $sDate, $sFormat = 'Y-m-d H:i:s' ) {

			$oDate = new DateTime( $sDate, new DateTimeZone( date_default_timezone_get() ) );
			$oDate->setTimezone( new DateTimeZone( 'EST' ) );
			return $oDate->format( $sFormat );

		}

	}