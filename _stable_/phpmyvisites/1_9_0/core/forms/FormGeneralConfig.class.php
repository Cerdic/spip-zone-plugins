<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: FormGeneralConfig.class.php,v 1.7 2005/11/20 15:02:35 matthieu_ Exp $


require_once INCLUDE_PATH . "/core/forms/Form.class.php";

class FormGeneralConfig extends Form
{
	function FormGeneralConfig( &$template )
	{
		parent::Form( $template );
	}
	
	function process()
	{
		$cst = array(
			'SU_LOGIN',
			'SU_PASSWORD',
			'SU_EMAIL',
			'PHPMV_URL',
			'SEND_MAIL',
			);

		foreach($cst as $name)
		{
			if(defined( $name ))
				$values[$name] = constant($name);
			elseif($name=='PHPMV_URL')
				$values[$name] = Request::getCurrentCompletePath();
			else
				$values[$name] = '';
		}
		$formElements = array(
			array('text', 'form_login', $GLOBALS['lang']['install_loginadmin'], 'value='.$values['SU_LOGIN']),
			array('password', 'form_password', $GLOBALS['lang']['install_mdpadmin'], 'value='.$values['SU_PASSWORD']),
			array('password', 'form_password2', $GLOBALS['lang']['admin_type_again'], 'value='.$values['SU_PASSWORD']),
			array('text', 'form_email', $GLOBALS['lang']['admin_admin_mail'], 'value='.$values['SU_EMAIL']),
			array('radio', 'form_send_mail', $GLOBALS['lang']['install_send_mail'], $GLOBALS['lang']['install_oui'], 'yes'),
			array('radio', 'form_send_mail', null, $GLOBALS['lang']['install_non'], 'no'),
			array('text', 'form_phpmvurl', $GLOBALS['lang']['admin_phpmv_path'], 'size=40 value='.$values['PHPMV_URL']),
		);

		$this->addElements( $formElements , 'General - phpMyVisites');
	
		$this->setChecked( 'form_send_mail', defined('SEND_MAIL') ? SEND_MAIL:'yes' );

		$formRules = array(
			array('form_email', $GLOBALS['lang']['admin_valid_email'], 'email', '', 'server'),
			array('form_email', sprintf($GLOBALS['lang']['admin_required'], $GLOBALS['lang']['admin_admin_mail']), 'required'),
			
			array('form_login', sprintf($GLOBALS['lang']['admin_required'], $GLOBALS['lang']['install_loginadmin']), 'required'),
			
			array('form_password', sprintf($GLOBALS['lang']['admin_required'], $GLOBALS['lang']['install_mdpadmin']), 'required'),
			array('form_password', $GLOBALS['lang']['admin_valid_pass'], 'complexPassword'),
			array('form_password', $GLOBALS['lang']['admin_match_pass'], 'compareField', 'form_password2'),
			
			array('form_phpmvurl', sprintf($GLOBALS['lang']['admin_required'], $GLOBALS['lang']['admin_phpmv_path']), 'required'),
						
			);

		$this->addRules( $formRules );
		
		return parent::process('install_general_setup');
	}
	
	function postProcess()
	{		
		$configPhpFileContent = array(
			'su_login' => $this->getElementValue('form_login'),
			'su_email' => $this->getElementValue('form_email'),
			'send_mail' => $this->getSubmitValue('form_send_mail'),			
			'phpmv_url' => $this->getElementValue('form_phpmvurl'),
		);
		
		$c =& PmvConfig::getInstance();
		$c->update( $configPhpFileContent );
		
		$passwordPost = $this->getElementValue('form_password');
		if( !defined('SU_PASSWORD') 
			|| $passwordPost != SU_PASSWORD )
		{
			$c->update( array( 'su_password' => md5($passwordPost) ) );
		}
		$c->write();	
	}
}
?>