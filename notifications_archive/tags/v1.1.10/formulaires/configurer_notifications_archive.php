<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_notifications_archive_saisies_dist(){
	include_spip('inc/config');
	$config=lire_config('notifications_archive');
	$notifications=charger_fonction('notifications_archiver','inc',true);
	$notifications=$notifications();
   

	$saisies=array();
	foreach($notifications AS $notification=>$options){
		$saisies[]=array(
				'saisie' => 'fieldset',
				'options' => array(
						'nom' => 'fieldset_'.$notification,
						'label' => _T('notifications_archive:fieldset_notification',array('notification'=>$notification))
						),
				'saisies' => array(
						array(
							'saisie'=>'oui_non',
							'options'=>array(
								'nom' => $notification.'[activer]',
								'datas'=>array('oui'=>'oui'),
								'defaut'=>$config[$notification]['activer'],
								'label' => _T('notifications_archive:label_activer',array('notification'=>$notification))                                  
								 )
							),
						array(
								'saisie'=>'input',
								'options'=>array(
										'nom' => $notification.'[duree]',
										'defaut'=>$config[$notification]['duree'],
										'label' => _T('notifications_archive:label_duree'),
										'explication' => _T('notifications_archive:explication_duree'),
										'afficher_si' => '@'.$notification.'[activer]@ == "on"'
								),	
							
							)
						)
					);   
		}
	
	return $saisies;
}

?>