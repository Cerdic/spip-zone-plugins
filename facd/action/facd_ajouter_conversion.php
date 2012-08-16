<?php
/**
 * Action d'ajout de document dans la file d'attente
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('inc/actions');

/**
 * Fonction d'ajout dans la file d'attente
 * 
 * @return array
 * 	Un tableau contenant l'identifiant dans la liste d'attente
 */
function action_facd_ajouter_conversion_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($id_document,$fonction,$format,$options,$mode) = explode('/',$arg);
	$id_facd_conversion = facd_ajouter_conversion_file($id_document,$fonction,$format,$options,$mode);
	$convertir_direct = charger_fonction('facd_convertir_direct','inc');
	$convertir_direct();
	return array($id_facd_conversion);
}

/**
 * Fonction d'ajout des versions dans la file d'attente
 *
 * @param int $id_document l'id du document original
 * @param string $objet
 * @param int $id_objet
 */
function facd_ajouter_conversion_file($id_document,$fonction='',$format='',$options='',$mode='conversion'){
	$infos_doc = sql_fetsel('extension,mode','spip_documents','id_document='.intval($id_document));
	$extension = $infos_doc['extension'];
	$mode_orig = $infos_doc['mode'];
	
	if(is_array($options)){
		$options = serialize($options);
	}
	$invalider = false;
	if($mode_orig != $mode){
		$en_file = sql_getfetsel("id_facd_conversion","spip_facd_conversions","id_document=".intval($id_document)." AND extension =".sql_quote($format)." AND statut IN ('en_cours,non,erreur')");
		if(!$en_file){
			$id_facd_conversion = sql_insertq("spip_facd_conversions", 
									array(
											'id_document'=>$id_document,
											'id_auteur'=> $GLOBALS['visiteur_session']['id_auteur'],
											'fonction' => $fonction,
											'extension'=>$format,
											'options'=>$options,
											'statut'=>'non'
									)
								);
			spip_log("On ajoute le document $id_document dans la file d'attente : $id_facd_conversion","facd");
			$invalider = true;
		}
		else{
			spip_log("Ce document existe deja dans la file d'attente","facd");
		}
		if($invalider){
			include_spip('inc/invalideur');
			suivre_invalideur(1);
		}
	}
	return $id_facd_conversion;
}

?>