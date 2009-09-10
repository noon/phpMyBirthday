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
 * Bc_Notification
 *
 * @category  Bom
 * @author    Max Köhler <max.koehler@mayflower.de>
 * @author    Daniel Hallmann <daniel.hallmann@mayflower.de>
 * @license   <PLACEHOLDER_MAYFLOWER>
 * @version   SVN: $Id:$
 * @link      <PLACEHOLDER_MAYFLOWER>
 * @since     Invitation available since Release 1.0
 */
class Bc_Notification {
      /**
       * Contains the instances model
       *
       * @var object $_instances
       */
      private static $_instance = null; 
      
      /**
       * Contains the Zend_Mail object
       *
       * @var Zend_Mail
       */
      private static $_mail = null;
      
      /**
       * Retrieves notification object
       *
       * @return unknown
       */
      public static function getInstance()
      {  
         if (null == self::$_instance) {
             self::$_instance = new self;
         } 
         return self::$_instance; 
    }  
 
     /**
      * Constructor isn't used
      *
      */
     protected function __construct() 
     {} 
      
     /**
      * Clone method isn't also uses
      *
      */
     final private function __clone() 
     {}
     
     /**
      * Get Zend Mail Object
      *
      * @return Zend_Mail
      */
     public static function getMailObj() 
     {
         if (null == self::$_mail) {
            self::_setMailObj();
         }
         
         return self::$_mail;
     }
     
     /**
      * Initialize Zend_Mail object
      *
      */
     private static function _setMailObj() 
     {
         self::$_mail = new Zend_Mail();
     }
     /**
      * Set mail options method
      *
      * @param string $body    Body string
      * @param array $from     Sender emails
      * @param array $addTo    Recipients emails
      * @param string $subject Subject of the mail
      */
     public static function setMailOptions($body, $from, $addTo, $subject)
     {
     	if (self::$_mail == null) {
     		self::_setMailObj();
     	}
     	
     	// Validation Classes:
     	$validMail 		= new Zend_Validate_EmailAddress();
     	$validString 	= new Zend_Validate_StringLength(8);
     	
     	// Validate email body
     	if ($validString->isValid($body)) {
     		self::$_mail->setBodyText($body);			
     	} else {
     		throw new Exception(implode($validString->getMessages(), '\n'));
     	}

     	// Validate sender email
     	
   		if ($validMail->isValid($from)) {
   			$emailFrom = $from;
   		} else {
   			throw new Exception(implode($validMail->getMessages(), '\n'));
   		}
     	
        self::$_mail->setFrom($emailFrom);
     	
     	// Validate recipient email
   		if ($validMail->isValid($addTo)) {
   			$emailTo = $addTo;
   		} else {
   			throw new Exception(implode($validMail->getMessages(), '\n'));
   		}

   		self::$_mail->addTo($emailTo);

        // Validte subject
        if ($validString->isValid($subject)) {
	        self::$_mail->setSubject($subject);
        } else {
        	throw new Exception(implode($validString->getMessages(), '\n'));
        }
     }
     /**
      * Send method to trigger Zend_Mail::send
      */
     public function send ()
     {
         self::$_mail->send();
     }
}