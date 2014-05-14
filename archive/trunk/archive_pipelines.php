<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 *
 * Sur les pages de rubriques dans le privé, afficher les articles archivés de la rubrique
 *
 * @param $flux array
 * 	Le contexte du pipeline
 * @return $flux array
 * 	Le contexte du pipeline modifié
 */
function archive_affiche_milieu($flux){
	if($flux['args']['exec'] == 'rubrique'){
		$flux['data'] .= recuperer_fond('prive/objets/liste/articles', array('titre'=>_T('archive:titre_archives_rubrique'),'statut'=>'archive','id_rubrique'=>$flux['args']["id_rubrique"]));
	}
	return $flux;
}

// Lancement des taches cron pour l'archivage
function archive_taches_generales_cron($taches_generales){ 
	$taches_generales['archive_cron'] = 1*24*3600;
	return $taches_generales;
}

/**
 * Insertion dans le pipeline post_edition (SPIP)
 * 
 * Lors du changement de statut vers "archive", on met la date dans le champs date_archive
 * 
 * @param $flux array
 * 	Le contexte du pipeline
 * @return $flux array
 * 	Le contexte du pipeline modifié
 */
function archive_post_edition($flux){
	if($flux['args']['action'] == 'instituer' && $flux['args']['statut_ancien'] != 'archive'  && $flux['args']['statut_nouveau'] == 'archive'){
		sql_updateq($flux['args']['table'],array('archive_date' => date(),'statut_archive'=>$flux['args']['statut_ancien']),id_table_objet($flux['args']['table'])."=".intval($flux['args']['id_objet']));
	}
	return $flux;
}
?>