<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'ajout de création d'une langue pour un module
 * 
 * Par exemple, l'activation de la langue italienne (it) pour le module "ecrire"
 * dupliquera les chaînes du module "ecrire" dans sa langue originale ("fr" par exemple)
 * tout en mettant le statut "NEW"
 * 
 * @param array $module
 * 		Les informations du module en base (on a besoin des champs "id_tradlang_module","module","lang_mere")
 * @param string $lang
 * 		La langue dans laquelle on souhaite créer la nouvelle version 
 */
function inc_tradlang_ajouter_code_langue($module,$lang){
	/**
	 * Sélection des chaînes de la langue mère du module
	 * 
	 * On ne sélectionne que les champs qui seront définitivement insérés tels quel en base pour simplifier le tableau
	 */
	$chaines_mere = sql_allfetsel('str,id,comm','spip_tradlangs',"id_tradlang_module=".intval($module['id_tradlang_module'])." AND lang=".sql_quote($module['lang_mere']));
	$total = count($chaines_mere);
	$chaines_inserees = array();
	$date = date('Y-m-d H:i:s');
	foreach($chaines_mere as $id => $chaine){
		/**
		 * On ajoute une entrée au tableau $chaines_inserees qui insèrera toutes les chaînes d'un coup
		 * - On crée un titre qui doit être unique
		 * - On change la langue avec le $lang passé en paramètre
		 * - On recrée le md5
		 * - On met la date_modif à tout de suite
		 * - On met langue_choisie à "oui"
		 * - les champs orig, statut, traducteur prennent les valeurs par défaut (0 et NEW), id_tradlang et maj sont incrémentés par mysql 
		 */
		$chaine['id_tradlang_module'] = intval($module['id_tradlang_module']);
		$chaine['titre'] = $chaine['id'].' : '.$module['module'].' - '.$lang;
		$chaine['module'] = $module['module'];
		$chaine['lang'] = $lang;
		$chaine['langue_choisie'] = 'oui';
		$chaine['statut'] = 'NEW';
		$chaine['md5'] = md5($chaine['str']);
		$chaine['date_modif'] = $date;
		$chaines_inserees[] = $chaine;
		unset($chaines_mere[$id]);
	}
	if(intval($total) > 0)
		$res = sql_insertq_multi('spip_tradlangs',$chaines_inserees);
	/**
	 * On génère le fichier correspondant si la configuration de tradlang le demande
	 */
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	if((lire_config('tradlang/sauvegarde_locale') == 'on') && (lire_config('tradlang/sauvegarde_post_edition') == 'on')){
		include_spip('tradlang_fonctions');
		if($dir_lang = tradlang_dir_lang()){
			$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
			$sauvegarder_module($module,$lang,$dir_lang);
		}
	}
	
	/**
	 * On ajoute un job tout de suite pour générer les premières révisions
	 */
	$job_description = _T('tradlang:job_creation_revisions_modules',array('module' => $module['module']));
	job_queue_add("tradlang_creer_premieres_revisions", $job_description, array('module'=>$module['module'],'lang'=>$lang),'inc/', false, 0, 10);
	
	/**
	 * On ajoute la ligne du bilan
	 */
	$bilan = array(
				'id_tradlang_module' => $module['id_tradlang_module'],
				'module' => $module['module'],
				'lang'=> $lang,
				'chaines_total' => $total,
				'chaines_ok' => 0,
				'chaines_relire' => 0,
				'chaines_modif' => 0,
				'chaines_new' => $total
			);
	sql_insertq('spip_tradlangs_bilans',$bilan);

	/**
	 * On invalide
	 */
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return true;
}
?>