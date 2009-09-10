<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * ... for holding an instance of Zend_Config_Ini
     *
     * @var String
     */
    protected $_config;

    /**
     * Get instance of Zend_Confog_Ini and set this value to the "global" config.
     *
     */
    protected function _initConfig()
    {
        // config
        $this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'all');
        Zend_Registry::set('config', $this->_config);
    }

    /**
     * Initialisation for the database, values see configs/application.ini
     *
     */
    protected function _initDb()
    {
        if ($this->_config->db) {
            $db = Zend_Db::factory($this->_config->db);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
        }
    }

    /**
     * Init for the logger.
     *
     */
    protected function _initLogger()
    {
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../log/' . $this->_config->log->file);
        $logger->addWriter($writer);

        Zend_Registry::set('log', $logger);
    }

    /**
     * Init the autoloader
     *
     */
    protected function _initLoader()
    {
        require_once 'Zend/Loader/Autoloader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Bc_');
    }

    /**
     * Set initial timezone
     *
     */
    protected function _initDate()
    {
        date_default_timezone_set('Europe/Berlin');
    }

}

