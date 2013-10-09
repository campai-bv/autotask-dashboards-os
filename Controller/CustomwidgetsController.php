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

	class CustomwidgetsController extends AutotaskAppController {

		public function edit( $iCustomwidgetId = null ) {
		
			if(
				$this->request->is( 'put' )
				||
				$this->request->is( 'post' )
			) {
				
				// prevents editing default widgets
				if ( !empty($iCustomwidgetId) && $iCustomwidgetId > 13 ) {
				
					$this->Customwidget->id = $iCustomwidgetId;
					
					
					/*$this->Customwidget->updateAll( $this->request->data['Customwidget']);
					$element = str_replace(	' ', '_', $this->request->data['Customwidget']['default_name']);
					$element = preg_replace('/[^A-Za-z0-9\-]/', '', $element);
					$element = "Widgets/CustomWidgets/" . $element . $this->Customwidget->id;
					$this->Customwidget->saveField('element', $element );
				
					$file = "../Plugin/Autotask/View/Elements/Widgets/CustomWidgets/_default_template.ctp";
					$newfile = "../Plugin/Autotask/View/Elements/" . $element . ".ctp";
					copy($file, $newfile);*/
				} //else {
				
				
				
					$this->Customwidget->save( $this->request->data['Customwidget']);
					$element = str_replace(	' ', '_', $this->request->data['Customwidget']['default_name']);
					$element = preg_replace('/[^A-Za-z0-9\-]/', '', $element);
					$element = "Widgets/CustomWidgets/" . $element . $this->Customwidget->id;
					$this->Customwidget->saveField('element', $element );
				
					$file = "../Plugin/Autotask/View/Elements/Widgets/CustomWidgets/_default_template.ctp";
					$newfile = "../Plugin/Autotask/View/Elements/" . $element . ".ctp";
					copy($file, $newfile);
				//}
				
				$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'customwidgets'
						,	'action' => 'index'
					) );
			}

			$aWidgets = $this->Customwidget->find(	'all'
														// filters out default widgets
														,	array( 'conditions' => array('id >' => 13))
														,	array( 'fields' => array( 'id'
																					, 'default_name'
																					, 'data_sizex'
																					, 'data_sizey'
												)));
			if ( !empty($iCustomwidgetId) && $iCustomwidgetId > 13 ) {
				$aEdit = $this->Customwidget->find(	'first'
														,	array( 'conditions' => array('id' => $iCustomwidgetId))
												);
				$this->set( 'aEdit', $aEdit );
			}			
			$this->set( 'aWidgets', $aWidgets );			
		}

		public function delete( $iWidgetId = null ) {
			
			// prevents deleting default widgets
			if ( !empty($iWidgetId) && $iWidgetId > 13 ) {
						
				$element = $this->Customwidget->read('element', $iWidgetId);
			
				if( $this->Customwidget->delete( $iWidgetId ) ) {

					unlink("../Plugin/Autotask/View/Elements/".$element['Customwidget']['element'].".ctp");
			
					$this->Session->setFlash( '<strong>Success!</strong> Custom widget has been deleted.' );
					$this->redirect( array(
							'plugin' => 'autotask'
						,	'controller' => 'customwidgets'
						,	'action' => 'index'
					) );
					exit();
				}	

			}
		}
		
	}