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
 * 		Les informations du module en base (on a besoin des champs "module","lang_mere")
 * @param string $lang
 * 		La langue dans laquelle on souhaite créer la nouvelle version 
 */
function inc_tradlang_ajouter_code_langue($module,$lang){
	/**
	 * Sélection des chaînes de la langue mère du module
	 */
	$chaines_mere = sql_select('*','spip_tradlangs',"module=".sql_quote($module['module'])." AND lang=".sql_quote($module['lang_mere']));
	while($chaine = sql_fetch($chaines_mere)){
		/**
		 * Insertion en base :
		 * - On crée un titre qui doit être unique
		 * - On change la langue avec le $lang passé en paramètre
		 * - On vide les traducteurs
		 * - On recrée le md5
		 * - On met la date_modif à tout de suite
		 * - On met langue_choisie à "oui"
		 * - On vire "maj" et "id_tradlang" qui sont des champs automatiquement incrémentés 
		 */
		$chaine['titre'] = $chaine['id'].' : '.$chaine['module'].' - '.$lang;
		$chaine['lang'] = $lang;
		$chaine['statut'] = 'NEW';
		$chaine['orig'] = 0;
		$chaine['traducteur'] = '';
		$chaine['md5'] = md5($chaine['str']);
		$chaine['date_modif'] = date('Y-m-d H:i:s');
		$chaine['langue_choisie'] = 'oui';
		unset($chaine['maj']);
		unset($chaine['id_tradlang']);
		$res = sql_insertq('spip_tradlangs',$chaine);
	}
	
	/**
	 * On invalide
	 */
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	
	/**
	 * On génère le fichier correspondant si la configuration de tradlang le demande
	 */
	include_spip('inc/config');
	if((lire_config('tradlang/sauvegarde_locale') == 'on') && (lire_config('tradlang/sauvegarde_post_edition') == 'on')){
		include_spip('tradlang_fonctions');
		if($dir_lang = tradlang_dir_lang()){
			$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
			$sauvegarder_module($module,$lang,$dir_lang);
		}
	}
	return true;
}
?>