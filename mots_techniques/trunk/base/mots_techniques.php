<?php

/**
 * Déclarations relatives à la base de données
 *
 * @package Mots_Techniques\SQL
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclarer le champs extra
 *
 * Ajoute un champ «technique» sur les groupes de mots
 *
 * @param array $champs
 *     Description des champs extras pour chaque table SQL
 * @return array
 *     Description des champs extras complétée 
**/
function mots_techniques_declarer_champs_extras($champs = array()){
	$champs['spip_groupes_mots']['technique'] = array(
		'saisie' => 'oui_non_strict', // Type du champs (voir plugin Saisies)
		'options' => array(
			'nom' => 'technique', 
			'label' => _T('motstechniques:info_mots_cles_techniques'), 
			'explication' => _T('motstechniques:bouton_mots_cles_techniques'),
			'sql' => "varchar(15) NOT NULL DEFAULT ''",
			'defaut' => '',// Valeur par défaut
		),
        'verifier' => array());

	return $champs;
}


/**
 * Déclarer des jointures pour le champ technique
 *
 * @param array $tables
 *     Description des l'objets éditoriaux
 * @return array
 *     escription complétée des l'objets éditoriaux
**/
function mots_techniques_declarer_tables_objets_sql($tables){
	// ajout de la jointure pour {technique=...} sur boucle MOT
	$tables['spip_mots']['join']["id_mot"] = "id_mot";
	$tables['spip_mots']['join']["id_groupe"] = "id_groupe";
	
	$tables['spip_mots']['tables_jointures'][] = 'groupes_mots';
	
	return $tables;
}

/**
 * Ajoute technique dans les champs hérités des groupes arborescents
 *
 * Lorsque le plugin de groupes arborescents est présent, on fait hériter
 * automatiquement les valeurs définies dans le champ technique
 * du groupe de mot racine.
 *
 * L'autorisation du champs extras le cache dans les groupes enfants.
 * 
 * @param array $champs
 *     Liste des champs à hériter aux groupes enfants
 * @return
 *     Liste des champs complété de technique
**/
function mots_techniques_groupes_mots_arborescents_heritages($champs) {
	$champs[] = 'technique';
	return $champs;
}

?>
