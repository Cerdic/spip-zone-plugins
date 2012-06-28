<?php
/**
 * FACD
 * File d'Attente de Conversion de Documents
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline taches_generales_cron
 *
 * Vérifie la présence à intervalle régulier de fichiers à convertir
 * dans la file d'attente
 *
 * @param array $taches_generales Un array des tâches du cron de SPIP
 * @return L'array des taches complété
 */
function facd_taches_generales_cron($taches_generales){
	$taches_generales['facd_conversion'] = 2*60;
	return $taches_generales;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * On ajoute deux javascript dans le head
 * 
 * @param array $plugins
 * 		L'array des js insérés
 * @return array $plugins
 * 		L'array des js insérés modifié
 */
function facd_jquery_plugins($plugins){
	if(test_espace_prive()){
		if(!in_array(_DIR_LIB_FLOT.'/jquery.flot.js',$plugins)){
			$plugins[] = _DIR_LIB_FLOT.'/jquery.flot.js';
		}
		$plugins[] = 'javascript/facd_flot_extras.js';
	}
	return $plugins;
}

/**
 * Insertion dans le pipeline post-edition (SPIP)
 *
 * Intervient à chaque modification d'un objet de SPIP
 * notamment lors de l'ajout d'un document
 *
 * @return $flux Le contexte de pipeline complété
 * @param array $flux Le contexte du pipeline
 */
function facd_post_edition($flux){
	if($flux['args']['operation'] == 'supprimer_documents'){
		sql_delete('spip_facd_conversions','id_document = '.$flux['args']['id_objet'].' AND statut!='.sql_quote('oui'));
	}
	return $flux;
}
?>