<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function motus_declarer_champs_extras($champs = array()){
	$champs['spip_groupes_mots']['rubriques_on'] = array(
		'saisie' => 'selecteur', // Type du champs (voir plugin Saisies)
		'options' => array(
			'nom' => 'rubriques_on', 
			'label' => _T('motus:rubriques_on'), 
			'explication' => _T('motus:explication_rubriques_on'),
			'sql' => "varchar(255) NOT NULL DEFAULT ''",
			'defaut' => '',// Valeur par défaut
			'whitelist' => array('rubriques'),
			'multiple' => 'oui',
			'restriction'=>array(	'voir' 		=> array('auteur'=>''),//Tout le monde peut voir
									'modifier'	=> array('auteur'=>'0minirezo'))),//Seul les admins peuvent modifier
        'verifier' => array());

	return $champs;	
}

?>
