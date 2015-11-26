<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function titrecourt_declarer_champs_extras($champs = array()){
	$champs['spip_rubriques']['titre_court'] = array(
		'saisie' => 'input',//Type du champs (voir plugin Saisies)
		'options' => array(
			'nom' => 'titre_court', 
			'label' => _T('titrecourt:titre_court'), 
			'sql' => "varchar(300) NOT NULL DEFAULT ''",
			'defaut' => '',// Valeur par défaut
			'restrictions'=>array(	'voir' 		=> array('auteur'=>''),//Tout le monde peut voir
									'modifier'	=> array('auteur'=>'webmestre'))),//Seul les webmestre peuvent modifier
		'verifier' => array());

	return $champs;	
}
?>
