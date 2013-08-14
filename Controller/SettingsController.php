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
	App::uses('AutotaskAppController', 'Autotask.Controller');

	class SettingsController extends AutotaskAppController {

		public function edit() {

			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {

				if( $this->Setting->save( $this->request->data ) ) {

					if( $this->__writeHtaccess( $this->request->data['Setting']['ips'] ) ) {
						$this->Session->setFlash( 'Setting changes have been saved.' );
					}

					$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'settings'
						,	'action' => 'edit'
						,	$this->Setting->id
					) );

					exit();

				}

			}

			$this->request->data = $this->Setting->read( null, 1 );
			$this->request->data['Setting']['ips'] = $this->__readHtaccess();

		}


		private function __readHtaccess() {

			$sIps = '';

			$sFile = ROOT . '/.htaccess';
			$handle = fopen( $sFile, 'r' );
			$sContents = fread( $handle, filesize( $sFile ) );
			fclose( $handle );

			$aContents = explode( "\n", $sContents );
			
			foreach ( $aContents as $sLine ) {
				
				if( 0 === stripos( $sLine, 'allow from ' ) ) {
					$sIps .= str_ireplace( 'allow from ', '', $sLine ) . "\n";
				}

			}

			return $sIps;

		}

		private function __writeHtaccess( $sAccessRules ) {

			$sHtaccessContent = '';

			$sDefaultHtaccessContent = '<IfModule mod_rewrite.c>' . "\n";
			$sDefaultHtaccessContent .= "\t" . 'RewriteEngine on' . "\n";
			$sDefaultHtaccessContent .= "\t" . 'RewriteRule    ^$ app/webroot/    [L]' . "\n";
			$sDefaultHtaccessContent .= "\t" . 'RewriteRule    (.*) app/webroot/$1 [L]' . "\n";
			$sDefaultHtaccessContent .= '</IfModule>';

			$aAccessRules = array();
			if( !empty( $sAccessRules ) ) {
				$aAccessRules = explode( "\n", trim( $sAccessRules ) );
			}

			if( !empty( $aAccessRules ) ) {

				$sHtaccessContent .= 'order deny,allow' . "\n";
				$sHtaccessContent .= 'deny from all' . "\n";

				foreach ( $aAccessRules as $sRule ) {
					$sHtaccessContent .= 'allow from ' . $sRule . "\n";
				}

				$sHtaccessContent .= "\n";
				$sHtaccessContent .= $sDefaultHtaccessContent;

			} else {
				$sHtaccessContent = $sDefaultHtaccessContent;
			}

			// Write the new .htaccess
			$sFile = ROOT . '/.htaccess';

			// Let's make sure the file exists and is writable first.
			if ( is_writable( $sFile ) ) {

				if ( !$handle = fopen( $sFile, 'w') ) {
					$this->Setting->invalidate( 'ips', "Cannot open file ( $sFile )" );
					return false;
				}

				if ( false === fwrite( $handle, $sHtaccessContent ) ) {

					$this->Session->setFlash( 'Could not write to the .htaccess file.' );
					$this->Session->write( 'Message.flash.element', 'flash_error' );
					return false;

				}

				fclose( $handle );

			} else {

				$this->Session->setFlash( '.htaccess is not writeable.' );
				$this->Session->write( 'Message.flash.element', 'flash_error' );
				return false;

			}

		}

	}