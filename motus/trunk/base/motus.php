<?php
/**
 * Déclarations relatives à la base de données
 *
 * @package SPIP\Motus\SQL
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer le champs extras
 *
 * Ajoute un champ «rubrique_on» sur les groupes de mots
 *
 * @pipeline declarer_champs_extras
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
			'label' => '<:motus:rubriques_on:>', 
			'explication' => '<:motus:explication_rubriques_on:>',
			'sql' => "text NOT NULL DEFAULT ''",
			'defaut' => '',// Valeur par défaut
			'whitelist' => array('rubriques'),
			'multiple' => 'oui',
		),
		'verifier' => array());

	return $champs;
}


/**
 * Ajoute rubriques_on dans les champs hérités des groupes arborescents
 *
 * Lorsque le plugin de groupes arborescents est présent, on fait hériter
 * automatiquement les valeurs définies dans les restrictions de rubrique
 * du groupe de mot racine.
 *
 * L'autorisation du champs extras le cache dans les groupes enfants.
 *
 * @pipeline groupes_mots_arborescents_heritages
 * 
 * @param array $champs
 *     Liste des champs à hériter aux groupes enfants
 * @return
 *     Liste des champs complété de rubriques_on
**/
function motus_groupes_mots_arborescents_heritages($champs) {
	$champs[] = 'rubriques_on';
	return $champs;
}
