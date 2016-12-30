<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function metas_opengraph_declarer_champs_extras($champs = array()){
	$champs['spip_auteurs']['twitter'] = array(
		'saisie' => 'input',//Type du champs (voir plugin Saisies)
		'options' => array(
			'nom' => 'twitter', 
			'label' => "Compte Twitter", 
			'explication' => "de la forme: @Nom",
			'sql' => "varchar(300) NOT NULL DEFAULT ''",
			'defaut' => '',// Valeur par défaut
			'restrictions'=>array(	'voir' 		=> array('auteur'=>''),//Tout le monde peut voir
									'modifier'	=> array('auteur'=>''))),//Seul les webmestre peuvent modifier
        'verifier' => array());
	$champs['spip_auteurs']['facebook'] = array(
		'saisie' => 'input',//Type du champs (voir plugin Saisies)
		'options' => array(
			'nom' => 'facebook', 
			'label' => "Compte Facebook", 
			'placeholder' => 'https://www.facebook.com/mon.nom',
			'explication' => 'de la forme: https://www.facebook.com/mon.nom',
			'sql' => "varchar(300) NOT NULL DEFAULT ''",
			'defaut' => '',// Valeur par défaut
			'restrictions'=>array(	'voir' 		=> array('auteur'=>''),//Tout le monde peut voir
									'modifier'	=> array('auteur'=>''))),//Seul les webmestre peuvent modifier
        'verifier' => array());

	return $champs;	
}
