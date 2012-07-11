<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer le champs extras
 *
 * Ajoute un champ «rubrique_on» sur les groupes de mots
 *
 * @param array $champs
 *     Description des champs extras pour chaque table SQL
 * @return array
 *     Description des champs extras complétée 
**/
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
		),
		'verifier' => array());

	return $champs;
}

?>
