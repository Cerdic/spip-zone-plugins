<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function mots_techniques_declarer_champs_extras($champs = array()){
	$champs['spip_groupes_mots']['technique'] = array(
		'saisie' => 'oui_non', // Type du champs (voir plugin Saisies)
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


function mots_techniques_declarer_tables_objets_sql($tables){
	// ajout de la jointure pour {technique=...} sur boucle MOT
#	$tables['spip_mots']['join']["id_mot"] = "id_mot";
#	$tables['spip_mots']['join']["id_groupe"] = "id_groupe";
	
#	$tables['spip_mots']['tables_jointures']['spip_mots'][] = 'groupes_mots';
	
	// ajout des restrictions de statuts simples
	/*$tables['spip_mots']['statut'] => array(
			 array('champ'=>'technique','publie'=>'', 'exception'=>'tout')
	);*/
	// ajout des restrictions de statuts simples
#	$tables['spip_groupes_mots']['statut'] = array(
#			 array('champ'=>'technique','publie'=>'', 'exception'=>'tout')
#	);
	
	return $tables;
}

?>
