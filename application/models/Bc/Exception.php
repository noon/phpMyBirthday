<?php

/**
 * Exception
 *
 * @package   Bom
 * @author    Daniel Hallmann <daniel.hallmann@mayflower.de>
 * @license   <PLACEHOLDER_MAYFLOWER>
 * @version   SVN: $Id: $
 * @since     File available since Release 1.0
 */

/**
 * Birthday Calendar Exception
 *
 * @package   Bom
 * @author    Daniel Hallmann <daniel.hallmann@mayflower.de>
 * @copyright 2008 <PLACEHOLDER_MAYFLOWER>
 * @license   <PLACEHOLDER_MAYFLOWER>
 * @version   SVN: $Id: Exception.php 9990 2008-10-16 12:54:38Z martin $
 * @link      <PLACEHOLDER_MAYFLOWER>
 * @since     File available since Release 1.0
 */
class Bc_Exception extends Exception
{

    /**
     * Contains the backtrace
     *
     * @var array
     * @since 1.0
     */
    private $bTrace;

    /**
     * Constructor for the Birthday_Exception class (that inherits from Exception).
     *
     * @param string $message The error message
     * @param int    $code    (optional) The error code for a better description.
     * @param array  $trace   (optional) Trace for more informations about the error.
     * @param int    $log     Every exception will be logged per default as an error. For other possibilies take a look
     *
     * @return void
     */
    public function __construct($message = null, $code = 0, $trace = array(), $log = Zend_Log::ERR)
    {
        parent::__construct($message, $code);

        if (empty($trace)) {
            $trace = $this->getTraceAsString();
        }

        $this->setTrace($trace);

        if ($log) {
            if (!Zend_Registry::isRegistered('log')) {
                error_log("Exception-Code: " . $code . " - Message: " . $message . " - Trace: "  .
                          $this->getTrace(),3,Zend_Registry::get('config')->log->file);
            } else {
                Zend_Registry::get('log')->log("Exception-Code: " . $code . " - Message: " . $message . " - Trace: "  .
                                               $this->getTrace(), $log);
            }
        }
    }

    /**
     * Setter method to ensure the trace for detailed evaluation in the frontend. That will be need, when the current
     * exception is mapped to a new.
     *
     * @param array $trace The current trace of the Exception is thrown
     *
     * @return void
     */
    private function setTrace($trace)
    {
        $this->bTrace = $trace;
    }

    /**
     * Getter method to deliver the current trace for analyzing in the frontend
     *
     * @return array Return the current backtrace
     */
    public function getDlsTrace()
    {
        return $this->bTrace;
    }
}