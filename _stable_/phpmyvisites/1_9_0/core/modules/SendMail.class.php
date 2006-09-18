<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: SendMail.class.php,v 1.18 2005/12/24 02:59:45 matthieu_ Exp $

require_once INCLUDE_PATH."/core/include/ViewModule.class.php";
require_once INCLUDE_PATH."/core/include/PmvConfig.class.php";
require_once INCLUDE_PATH."/libs/Cache/Lite.php";
require_once INCLUDE_PATH."/core/views/ViewVisitsRss.class.php";		
require_once INCLUDE_PATH."/core/include/myMailer.class.php";	
require_once INCLUDE_PATH."/core/include/UserConfigDb.class.php";

class SendMail extends Module
{
	var $defaultAction = "showAll";
	
	function SendMail()
	{
		parent::Module();
	}
	
	function showAll()
	{	
		$this->tpl->setMainTemplate("structure_mail.tpl");
		
		
		$this->request->setModuleName( 'view_visits_rss');
		$allSiteArchive =  DataModel::getSites();
		
		/**
		 * Cache Lite
		 */
		$options = array(
		    'cacheDir' => DIR_CACHE_MAIL,
		    'lifeTime' => CACHE_MAIL_LIFETIME
		);
		$Cache_Lite = new Cache_Lite($options);
		
		
		/**
		 * php Mailer
		 */
		$mail = new MyMailer();
		
		
		/**
		 * Compute mails
		 */				
		
		$o_config =& PmvConfig::getInstance();
		
		$mail->IsHTML(true);
	
		$imgUrl = DIR_IMG_THEMES . "phpmv.png";
		//if($o_mod->data->getContent('nb_vis') != 0)
			$mail->AddEmbeddedImage($imgUrl, "my-attach", "Bande", "base64", "image/png");
			//$mail->AddStringAttachment()
		
		foreach($allSiteArchive as $infoSite) 
		{
			$uniqCacheId = md5(serialize($infoSite).date("Y-m-d")) . '.mail';
			
			// Test if thereis a valide cache for this id
			if (true)//!$allData = $Cache_Lite->get($uniqCacheId)) 
			{
				$o_mod = new ViewVisitsRss($infoSite);
				$o_mod->init($this->request);
				
				$dateLiteral = $o_mod->data->archive->getLiteralDate();
						
				$body = '<html xml:lang="fr" >
						<head>
							<meta http-equiv="content-type" content="text/html; charset=utf-8" />
						</head>
						
						<body>
						';

				$body .= $o_mod->showAll( true, true);
				$body .= '</body></html>';
				$textBody = strip_tags($body);
				
				$subject = vsprintf($GLOBALS['lang']['rss_titre'], 
									array($infoSite->getName(), $dateLiteral));
				
				print("<br>Subject : $subject<hr>");
				print("<br>Content : $body<hr>");
				
				//$Cache_Lite->save($body);
			}
			
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $textBody;
			$mail->CharSet = $GLOBALS['lang']['charset'];	
			
				
			$user = new UserConfigDb();
			$groups = $user->getGroups();
			
			$users = array_merge(	
								array(0 => array( 'email' => SU_EMAIL, 'alias' => 'phpMyVisites Administrator')),
								$user->getUserByGroup( 1, $infoSite->getId() ),
								$user->getUserByGroup( 2, $infoSite->getId() ));
			foreach($users as $userInfo)
			{
				if(!empty($userInfo['email']))
				{
					$mail->AddAddress( $userInfo['email'], $userInfo['alias'] );
					
					if(!@$mail->Send())
					{
					   echo "<u><b>There was an error sending the message to ".$userInfo['email']."</u></b><br>";
					}
					else
					{
						echo "<u><b>Message was sent successfully to ".$userInfo['email']."</u></b><br>";
					}
					$mail->ClearAddresses();
				}
			}
		}
	}
}
?>