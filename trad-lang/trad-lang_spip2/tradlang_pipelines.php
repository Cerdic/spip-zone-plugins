<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline declarer_tables_objets_surnoms (base/connect_sql.php)
 * La table spip_tradlangs est une table ancienne, et n'a pas de S final ...
 * Pour éviter les problèmes liés à cela, on surnomme les objets
 * 
 * @param array $flux La liste des surnoms
 * @return array Le $flux complété
 */
function tradlang_declarer_tables_objets_surnoms($flux){
	//$flux['tradlang'] = 'tradlang';
	return $flux;
}

/**
 * Insertion dans le pipeline revisions_liste_objets du plugin revisions (2.3)
 * Definir la liste des tables possibles
 * @param array $array
 * @return
 */
function tradlang_revisions_liste_objets($array){
	$array['tradlang'] = 'tradlang:chaines_langue';
	return $array;
}

/**
 * Insertion dans le pipeline forum_objets_depuis_env (Plugin Forum)
 * On ajoute la possibilité d'avoir des forums sur les chaines de langue
 * @param array $array
 */
function tradlang_forum_objets_depuis_env($array){
	$array['tradlang'] = id_table_objet('tradlang');
	return $array;
}

/**
 * Insertion dans le pipeline post_edition
 * Si configuré comme tel on regénère les fichiers à chaque modification de chaine de langue
 * 
 * @param object $flux
 * @return
 */
function tradlang_post_edition($flux){
	if($flux['args']['table'] == "spip_tradlangs"){
		$config = @unserialize($GLOBALS['meta']['tradlang']);
		if (!is_array($config))
			return $flux;
		if(($config['sauvegarde_locale'] == 'on') && ($config['sauvegarde_post_edition'] == 'on')){
			include_spip('tradlang_fonctions');
			if($dir_lang = tradlang_dir_lang()){
				$infos = sql_fetsel('*',$flux['args']['table'],'id_tradlang='.intval($flux['args']['id_objet']));
				$module = sql_fetsel('*','spip_tradlang_modules','module='.sql_quote($infos['module']));
				$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
				$sauvegarder_module($module['module'],$infos['lang'],$dir_lang);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head
 * On ajoute les javascript dans le head
 */
function tradlang_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/tradlang.js').'" ></script>'."\n";
	if(defined('_DIR_PLUGIN_TOOLTIP')){
		$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/tradlang_tooltip.js').'" ></script>'."\n";
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_css
 * On ajoute les deux feuilles de style dans le head
 */
function tradlang_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/tradlang.css').'" type="text/css" />';
		$flux .= '<link rel="stylesheet" href="'.generer_url_public('tradlang.css').'" type="text/css" />';
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_boucle
 * Si on est dans la fonction calculer_langues_utilisees, on ne renvoit pas les langues des tradlang
 * pour éviter de bloquer ces langues dans la configuration du multilinguisme
 */
function tradlang_pre_boucle($boucle){
	if(($boucle->nom == 'calculer_langues_utilisees') && $boucle->id_boucle == 'tradlang'){
		array_unshift($boucle->where,array("'='", "'$id_table." ."id_tradlang'", "'0'"));
	}
	return $boucle;
}

/**
 * Insertion dans le pipeline affiche_milieu
 * Sur la fiche des auteurs, on ajoute la liste des révisions de chaines de l'auteur
 */
function tradlang_affiche_milieu($flux){
	if (($flux['args']['exec'] == 'auteur') && (intval($flux['args']['id_auteur']) > 0)){
		$texte = recuperer_fond(
			'prive/objets/liste/versions',
			array(
				'objet'=>'tradlang',
				'id_auteur'=>intval($flux['args']['id_auteur'])
			)
		);
		$flux['data'] .= $texte;
	}
	return $flux;
}

/**
 * ajouter un champ langues préférées sur le formulaire CVT editer_auteur
 *
 * @param array $flux
 * @return array
 */
function tradlang_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		$langue_preferee = recuperer_fond('formulaires/inc-langues_preferees', $flux['args']['contexte']);
		$flux['data'] = preg_replace('%(<li class=["\'][^"\']*editer_bio(.*?)</li>)%is', "\n".$langue_preferee."\n".'$1', $flux['data']);
	}
	return $flux;
}

/**
 * Ajouter la valeur langues_preferees dans la liste des champs de la fiche auteur
 *
 * @param array $flux
 */
function tradlang_formulaire_charger($flux){
	// si le charger a renvoye false ou une chaine, ne rien faire
	if (is_array($flux['data'])){
		if ($flux['args']['form']=='editer_auteur'){
			$flux['data']['langues_preferees'] = '';
			if ($id_auteur = intval($flux['data']['id_auteur'])){
				$flux['data']['langues_preferees'] = sql_getfetsel('langues_preferees','spip_auteurs','id_auteur='.intval($id_auteur));
			}
		}
	}
	return $flux;
}

/**
 * ajouter les langues_preferees soumises lors de la soumission du formulaire CVT editer_auteur
 * 
 * @param array $flux
 * @return array
 */
function tradlang_pre_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		if (is_array($langues_preferees = _request('langues_preferees'))) {
			$flux['data']['langues_preferees'] = serialize($langues_preferees);
		}else{
			$flux['data']['langues_preferees'] = serialize(array());
		}
	}
	return $flux;
}
?>