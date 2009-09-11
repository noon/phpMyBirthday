<?php

/**
 * ImportController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Date.php';

class ImportController extends Zend_Controller_Action {
	
	/**
	 * Deactivate the default layout.
	 */
	public function preDispatch() {
		$this->_helper->layout ()->disableLayout ();
	}
	
	/**
	 * The import action
	 */
	public function indexAction() {
	
	}
	
	public function uploadAction() {
		
		if (isset ( $_FILES ['importfile'] ) && ! empty ( $_FILES ['importfile'] ['name'] )) {
			$content = file_get_contents ( $_FILES ['importfile'] ['tmp_name'] );
			$lines = explode ( "\n", $content );
			
			$persons = array ();
			
			//'BDAY'
			
			$state = 0;
			
			foreach ( $lines as $line ) {
				
				$line = str_replace("\r", "", $line);
				
				$pos = strpos($line, ':');
				
				if($pos === false){
					continue;
				}
				
				$key = substr($line, 0, $pos);
				$value = substr($line, $pos + 1);
				
				switch ($state) {
					case 0 :
						if ($key === 'BEGIN' && $value === 'VCARD') {
						  $state = 1;
						  $person = array();
						}
						break;
					case 1 :
						if ($key === 'END' && $value === 'VCARD') {
                            $state = 0;
                            $persons[] = $person;
                        } else {
                        	if($key === 'FN' || substr($key, 0, 3) === 'FN;'){
                        		$name = explode(' ', $value);
                        		
                                $person['firstname'] = strip_tags($name[0]);
                                $person['secondname'] = strip_tags($name[1]);
                        	}
                        	if($key === 'BDAY' || substr($key, 0, 5) === 'BDAY;'){
                        		$value = substr($value, 0, 10);
                        		$date = new Zend_Date();
                        		if($date->isDate($value, 'yyyy-MM-dd')){
                        		  $date->set($value);
                        		  $person['birthdate'] = $date->toString('yyyy-MM-dd');
                        		}
                        	}
                        }
                        break;	
				}
			
			}
		}
		
		foreach ($persons as $person) {
			if(isset($person['firstname']) && isset($person['secondname']) && isset($person['birthdate'])){
                $user = new Bc_UserDTO();
                $user->set('firstname',  $person['firstname']);
                $user->set('secondname', $person['secondname']);
                $user->set('birthdate',  $person['birthdate']);
                $user->save();
            }
		}
		
		$this->_redirect('/');
	}

}
