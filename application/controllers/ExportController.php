<?php

/**
 * ExportController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Date.php';

class ExportController extends Zend_Controller_Action {
	
	/**
	 * Deactivate the default layout.
	 */
	public function preDispatch() {
		$this->_helper->layout ()->disableLayout ();
	}
	
	/**
	 * The export action
	 */
	public function indexAction() {
		
		header ( "Content-Type: text/Calendar" );
		header ( "Content-Disposition: inline; filename=Geburtstage.ics" );
		
		$uid = $this->getRequest()->getParam('uid', null);
		
		$filter = array();
		
		if($uid !== null){
			$filter['id'] = $uid;
		}
		
        $userDTOs = Bc_UserDTO::fetch($filter);
        
        $users = array();
        foreach ($userDTOs as $userDTO) {
        	
        	$birthdate = new Zend_Date();
        	$birthdate->set($userDTO->get('birthdate'), Zend_Date::ISO_8601);
        	
        	$nextdate = new Zend_Date();
        	$nextdate->set($userDTO->get('birthdate'), Zend_Date::ISO_8601);
        	$nextdate->addDay('1', Zend_Date::DAY);
        	

            $users[] = array(
                'id'         => $userDTO->get('id'),
                'birthYear'  => $birthdate->get(Zend_Date::YEAR),
                'birthDate'  => $birthdate->toString('yyyyMMdd'),
                'birthMonth' => $birthdate->get(Zend_Date::MONTH),
                'birthDay'   => $birthdate->get(Zend_Date::DAY),
                'nextDate'   => $nextdate->toString('yyyyMMdd'),
                'firstName'  => $userDTO->get('firstname'),
                'secondName' => $userDTO->get('secondname')
            );
        }
        
        $this->view->users        = $users;
		$this->view->timeStamp    = date("Ymd") . 'T' . date("His") . 'Z';
	
	}
}
