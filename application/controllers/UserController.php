<?php

class UserController extends Zend_Controller_Action
{
    private function _outputAjaxCallResult($result) {
    	
    	switch($result) {
    		case true:
	            echo 'true';
	            exit;
	         break;

            case false:
	            echo 'false';
	            exit;
            break;

            default:
            	echo $result;
            break;
    	}
    }

    /**
     * Enter description here...
     *
     */
    public function indexAction()
    {
        $this->_forward('new');
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }    

    public function editAction()
    {
        $this->_helper->layout()->disableLayout();

        
        $id = $this->_request->getParam('id', null);
        
        if($id === null) {
        	//no id set, must be a new user
        	//do nothing
        	
        	$this->view->userData = array(
				                        'id'        =>  '',
				                        'firstname' =>  '',
				                        'secondname' => '',
				                        'birthdate' =>  ''
				                    );

        } else {
        	//fetch user data from DB
        	$userData = Bc_UserDTO::fetchAsArray(array('id' => $id ));
        	if(!isset($userData[0])) {
        		$this->_outputAjaxCallResult(false);
        	}
        	$this->view->userData = $userData[0];
        	echo json_encode($userData[0]);
        }
        
        exit;
        
    }
    
    public function saveAction()
    {
        //save user data to DB
        $user = new Bc_UserDTO();
        $user->set('id', $this->_request->getParam('id', null));
        $user->set('firstname', $this->_request->getParam('firstname', null));
        $user->set('secondname', $this->_request->getParam('secondname', null));
        $user->set('birthdate', $this->_request->getParam('birthdate', null));
        $result = $user->save();
  
        if($result === false) {
            $this->_outputAjaxCallResult(false);
        } else {
            $this->_outputAjaxCallResult(true);
        }
    	
    }
    
    public function deleteAction()
    {
    	$userId = $this->_request->getParam('id', null);
    	
    	if($userId === null) {
            $this->_outputAjaxCallResult(false);
    	} else {
    		
	        $result = Bc_UserDTO::remove($userId);
	  
    		if($result === false) {
    			$this->_outputAjaxCallResult(false);
    		} else {
    			$this->_outputAjaxCallResult(true);
    		}
    	}
    }
    
}

