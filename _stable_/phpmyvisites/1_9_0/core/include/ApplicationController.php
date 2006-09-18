<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: ApplicationController.php,v 1.27 2005/12/24 02:59:43 matthieu_ Exp $


/**
 * controleur de l'application
 *
 *
 * @author  Matthieu Aubry <matthieu@phpmyvisites.net>
 * @author  xavier Lembo <xav@elix-dev.com>
 * @since Thu Sep 01 21:07:21 CEST 2005
 * @version $Id: ApplicationController.php,v 1.27 2005/12/24 02:59:43 matthieu_ Exp $
 * @package 
 *
 */
require_once INCLUDE_PATH . '/core/include/Request.class.php';
require_once INCLUDE_PATH . '/core/include/Module.class.php';
require_once INCLUDE_PATH . '/core/include/global.php';
require_once INCLUDE_PATH . '/core/include/Lang.class.php';
require_once INCLUDE_PATH . '/core/include/User.class.php';

class ApplicationController
{
    
    var $module;
    
    var $defaultModuleName = 'index';
    
    var $request;
    
    var $lang;
    
    
    /**
     * Constructeur
     */
    function ApplicationController(){}
    
    
    /**
     * Singleton
     */
    function &getInstance()
    {
        static $instance;
        
        if (!isset($instance)){
            $c = __CLASS__;
            $instance = new $c;
        }
        return $instance;
        
    }
    
    /**
     * Point d'entr�e de l'application
     *
     */
    function init()
    {
		setIncludePath();
		
		//ob_start();
		{
			$c =& PmvConfig::getInstance();
			
			$db =& Db::getInstance();

			if(defined( 'DB_HOST' ))
			{
				$db->connect();
			}
			// try to set memory limit to MEMORY_LIMIT 
			setMemoryLimit();
			
	        $controller =& ApplicationController::getInstance();
	        
			$controller->loadLang();
			
	        $controller->parseRequest();

			$controller->loadModule();
		}        
		//ob_flush();
		$controller->executeAction();
	}
     
    /**
     * Analyse et definit la requete utilisateur
     *
     */
    function parseRequest()
    {
         $this->request  =& Request::getInstance();
        if (!is_a( $this->request , 'Request'))
            trigger_error('Unable to parse current request... error', E_USER_ERROR);

    }
    
    /**
     * retourne la request 
     *
     */
    function &getRequest()
    {
        return $this->request;
    }
    
    
    function loadLang()
    {
        $this->lang = new Lang();
    }
    
    function &getLang()
    {
        return $this->lang;
    }
    
    /**
     * Charge le module demand�
     *
     */
    function loadModule()
    {
		// TODO print("Database problems. Please check that all tables are installed! Try an update or a new install, your data will be kept.");
		
        $moduleName = $this->request->getModuleName();
		$db =& Db::getInstance();
		//print($moduleName);

		if(ereg('admin', $moduleName))
		{
			$this->lang->reloadLangFile();
		}
		if (!$moduleName)
		{
            $moduleName = $this->defaultModuleName;
		}
		
		
		if($moduleName == 'list_logos')
		{
			$authorized = true;
		}
		else
		{
			$b_writeDir = checkDirWritable( );
			
			/**
			 * very first : logo selection is allowed
			 */
			/**
			 * first look if installation is needed 
			 */
			if(!is_file( _PHPMV_DIR_CONFIG . "/config.php")
				|| !defined('DB_HOST')
				|| !defined('SU_LOGIN')
				|| !defined('INSTALL_OK'))
			{
				if( !Request::isCurrentModuleAnInstallModule() )
				{
					Request::redirectToModule('install_welcome');
				}
				$authorized = true;
			}
			
			/**
			 * second, look if database configuration only is needed
			 */
			else if(!$db->isReady() 
					&& substr_count( Request::getCurrentCompleteUrl(), 'mod=login' ) === 0
					)
			{
				Request::redirectToModule('admin_db_config');			
			}
			
			/**
			 * third, look if there is a problem relative to writable directories
			 */
			else if( $b_writeDir === false)
			{	
				// case there is a write problem, we load server_info page to indicate the user the problem
				$moduleName = 'admin_server_info';
				$authorized = true;
			}
			else if($db->isReady())
			{
				if( version_compare($db->getVersion(), PHPMV_VERSION) == -1)
				{
					$moduleName = 'admin_update';
					$authorized = true;
				}
				else if( version_compare($db->getVersion(), PHPMV_VERSION) == 1)
				{
					trigger_error("There is a problem : your database is more recent than your phpMyVisites files! Try to upload the last release of phpMyVisites on your server.", E_USER_ERROR);
				}
			}
		}
		/**
		 * else it's ok! Load module
		 */
		
		// update module in the object request, used for hidden field in the login form
		$this->request->setModuleName($moduleName);
		
		// manage the user, is he authorized to see this module ?
		$me =& User::getInstance();
		
		if( isset($authorized)
			|| $me->isAuthorized( $moduleName )  
			|| $this->request->isCrontabAllowed()
			)
		{
	        $module = Module::factory($moduleName);
		}
		else
		{
	        $module = Module::factory('login');
		}
		
  		if (!is_subclass_of($module, 'Module'))
            trigger_error('Unable to load: ' . $moduleName . ' module', E_USER_ERROR);
        
         $module->init($this->request, null);
		
         $this->module =& $module;
	}
    
    
    function executeAction()
    {
		
        $this->module->doAction();
    }
}
?>