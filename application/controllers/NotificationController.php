<?php
/**
 * Business Abstraction Layer for the Notifications
 *
 * @category  Bom
 * @author    Max Köhler <max.koehler@mayflower.de>
 * @author    Daniel Hallmann <daniel.hallmann@mayflower.de>
 * @license   <PLACEHOLDER_MAYFLOWER>
 * @version   SVN: $Id:$
 * @link      <PLACEHOLDER_MAYFLOWER>
 * @since     Invitation available since Release 1.0
 */


/**
 * NotificationController
 *
 * @category  Bom
 * @author    Max Köhler <max.koehler@mayflower.de>
 * @author    Daniel Hallmann <daniel.hallmann@mayflower.de>
 * @license   <PLACEHOLDER_MAYFLOWER>
 * @version   SVN: $Id:$
 * @link      <PLACEHOLDER_MAYFLOWER>
 * @since     Invitation available since Release 1.0
 */
class NotificationController extends Zend_Controller_Action {

	/**
	* Initialize action controller here
	*
	* @return unknown
	*/
	public function init() {}

    /**
     * Deactivates the default layout.
     * 
     */
    public function preDispatch() {
        $this->_helper->layout()->disableLayout();
    }	
	
    /**
     * Notification Action
     *
     */
	public function indexAction() {
		
		// pattern for list-view
		$pattern = ' - %s %s(%s)';

        // define subject for mail
        $subject = 'Geburtstags-Erinnerung';

        $from = 'root@localhost';

        $to = 'root@localhost';		
		
        // email body
        $body = null;
        
		// upcoming birthdates
		$upcomingBirthdates = Bc_UserDTO::getUsersWithBirthdayInXDays(1);
        if (count($upcomingBirthdates) > 0) {
			foreach($upcomingBirthdates as $upcomingBirthdate) {
                $upcomingUsers[] = sprintf($pattern, 
				    $upcomingBirthdate->get('firstname'), 
				    $upcomingBirthdate->get('secondname'), 
				    'wird ' . (date('Y') - $upcomingBirthdate->get('birthdate')));
			}
			
			// define mail content
			$cBody = "Geburtstage morgen:\n\n";
			$cBody .= implode("\n", $upcomingUsers);
        }
		
		// todays birthdates
		$todaysBirthdates = Bc_UserDTO::getUsersWithBirthdayInXDays(0);
        if (count($todaysBirthdates) > 0) {
			foreach($todaysBirthdates as $todaysBirthdate) {
				$todaysUsers[] = sprintf($pattern, 
			         $todaysBirthdate->get('firstname'), 
			         $todaysBirthdate->get('secondname'), 
			         'ist ' . (date('Y') - $todaysBirthdate->get('birthdate')));
			}
			
			// define mail content
    	    $tBody  = "Geburtstage heute:\n\n";
    	    $tBody .= implode("\n", $todaysUsers);
        }		

		
		if (isset($todaysUsers)) {
			$body .= $tBody;
		}
		if (isset($upcomingUsers) && isset($todaysUsers)) {
			$body .= "\n\n--------------------------------\n\n";
			$body .= $cBody;
		} else if (isset($upcomingUsers)) {
			$body .= $cBody;
		}
           
		if (null != $body) {
			// set view message
			$this->view->message = "E-Mail mit Geburstagserinnerungen wurde mit folgendem Inhalt versendet: \n\n" . $body;
	        
	        // build mailer and send
	        $mail = Bc_Notification::getInstance();
	        $mail->setMailOptions($body, $from, $to, $subject);
	        $mail->send();		
		} else {
			// set view message
            $this->view->message = 'Derzeit gibt es keine Geburtstage.';
		}
	
	}
}

?>
