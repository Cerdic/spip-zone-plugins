<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: FormGroup.class.php,v 1.2 2005/11/21 06:07:25 matthieu_ Exp $



require_once INCLUDE_PATH . "/core/forms/Form.class.php";
require_once INCLUDE_PATH . "/core/include/SiteConfigDb.class.php";

class FormGroup extends Form
{
	
	var $valueName = '';
	var $valueMainUrl = '';
	var $valueLogo = 1;
	var $valueRecordGet = 'all';
	var $valueVariableNames = '';
	var $site;
	
	function FormGroup( &$template, $siteAdmin)
	{
		parent::Form( $template);
		$this->idSite = $siteAdmin;
	}
	
	function process()
	{		
		$this->user = new UserConfigDb();
		
		$groups = $this->user->getGroups();
		
		$groups['no_permission'] = "No permission";
		foreach($groups as $id => $name)
		{
			$usersInGroup = $this->user->getUserByGroup( $id, $this->idSite );
			
			$formElements = array();
			
			foreach($usersInGroup as $info)
			{
				
				// general input
				$formElements[] = array('checkbox',
										$info['login'],
										' ',
										$info['alias'] . " (login : ". $info['login'].")"
										// ". $info['email']
									);
			}
			
			$groupCopy = $groups;
			$groupCopy = array_merge( array( 0 => $GLOBALS['lang']['admin_move_select']), 
							$groupCopy);
			unset($groupCopy[$id]);
			
			$formElements[] = array('select',
									'group_to_move_to_'.$id,
									$GLOBALS['lang']['admin_move_group'],
									$groupCopy
								);
			
			$description = array();
			if($name == 'admin')
			{
				$description['name'] = $GLOBALS['lang']['admin_group_admin_n'];
				$description['description'] = $GLOBALS['lang']['admin_group_admin_d'];
			}
			elseif($name == 'view')
			{
				$description['name'] = $GLOBALS['lang']['admin_group_view_n'];
				$description['description'] = $GLOBALS['lang']['admin_group_view_d'];
			}
			else
			{
				$description['name'] = $GLOBALS['lang']['admin_group_noperm_n'];
				$description['description'] = $GLOBALS['lang']['admin_group_noperm_d'];
			}				
			$descriptionTxt = "<u>".$GLOBALS['lang']['generique_name']."</u> <b>$name</b> (".$description['name'] . ")<br><u>".$GLOBALS['lang']['generique_description'].
								"</u> " . $description['description']."<br>";	
			$this->addElements( $formElements , $descriptionTxt);
			
		}
		
		// launche process
		return parent::process( 'admin_group_title' );
	}
	
	function postProcess()
	{		
		$submitValues = $this->getSubmitValues();
		$loginValidated = array();
		//var_dump($submitValues);
		
		foreach($submitValues as $name => $value)
		{
			// new group detected
			if(substr_count( $name, 'group_to_move_to_') > 0)
			{
				if($value !== '0')
				{
					//print("<br>$name validated! move to group $value <br>");
					//print_r($loginValidated);
					
					$this->user->setSiteGroups( $this->idSite, $value, $loginValidated);
					
					$loginValidated = array();
				}
			}
			else
			{
				$loginValidated[] = $name;
			}
		}
		$infoSite = array(	
			// db field name => new value
			'name' => $this->getSubmitValue('form_name'),
			'logo' => $this->getSubmitValue('form_logo'),
			'params_choice' => $this->getSubmitValue('form_params'),
		);
		
		$urlSite = $this->getSubmitValue('form_url');
		
		$params_names = $this->getSubmitValue('form_params_names');
		
		if(!empty($params_names))
		{
			$infoSite['params_names'] = $params_names;
		}
			
			
	}
}
?>