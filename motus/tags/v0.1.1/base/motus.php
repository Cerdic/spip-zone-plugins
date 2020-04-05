<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function motus_declarer_champs_extras($champs = array()){
	
	$champs[] = new ChampExtra(array(
		'table' => 'groupes_mot', 
		'champ' => 'rubriques_on', 
		'label' => 'motus:rubriques_on', 
		'type' => 'selecteur_rubrique', // necissite bonux et saisies
		'sql' => "varchar(255) NOT NULL DEFAULT ''", 

		// experimental
		'saisie_externe' => true, // saisies
		'saisie_parametres' => array(
			'explication' => 'motus:explication_rubriques_on', 
			'multiple' => 'oui',
		),
	));
	
		
	return $champs;
}
?>
