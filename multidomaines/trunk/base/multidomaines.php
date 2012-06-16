<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function multidomaines_declarer_champs_extras($champs = array()){
	$champs['spip_rubriques']['host'] = array(
		'saisie' => 'input',//Type du champs (voir plugin Saisies)
		'options' => array(
			'nom' => 'host', 
			'label' => _T('multidomaines:label_url'), 
			'label' => _T('multidomaines:precisions_url'), 
			'sql' => "text NOT NULL DEFAULT ''",
			'defaut' => '',// Valeur par défaut
			'restrictions'=>array(	'voir' 		=> array('auteur'=>''),//Tout le monde peut voir
									'modifier'	=> array('auteur'=>'webmestre'))),//Seul les webmestre peuvent modifier
        'verifier' => array());

	return $champs;	
}
?>
