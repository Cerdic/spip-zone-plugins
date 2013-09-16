<?php
/**
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Fichier des pipelines utilisés par le plugin
 * 
 * @package SPIP\Tradlang\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipelines taches_generales_cron (SPIP)
 * 
 * On ajoute une tache cron toutes les 4 minutes afin de créer les premières révisions des 
 * tradlang pour éviter de perdre du temps par la suite.
 * 
 * @param array $taches_generales
 * 		Le tableau des taches à réaliser
 * @return array $taches_generales
 * 		Le tableau des taches complété
 */
function tradlang_taches_generales_cron($taches_generales) {
	$taches_generales['tradlang_verifier_versions'] = 240;
	return $taches_generales;
}
/**
 * Insertion dans le pipeline pre_edition (SPIP)
 * 
 * Ajouter les langues_preferees soumises lors de la soumission du formulaire CVT editer_auteur
 * Si quelque chose est sélectionné, on le serialize pour le mettre en base, sinon on serialize un array 
 * pour toujours avoir quelquechose
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline auquel on a ajouté ce que l'on souhaite
 */
function tradlang_pre_edition($flux){
	if ($flux['args']['table'] == 'spip_auteurs') {
		if (is_array($langues_preferees = _request('langues_preferees')))
			$flux['data']['langues_preferees'] = serialize($langues_preferees);
		else
			$flux['data']['langues_preferees'] = serialize(array());
	}
	return $flux;
}

/**
 * Insertion dans le pipeline post_edition (SPIP)
 * 
 * Si configuré comme tel on regénère les fichiers à chaque modification de chaine de langue
 * On n'agit que sur les conditions suivantes :
 * -* on modifier la table spip_tradlangs
 * -* on a activé la sauvegarde locale
 * -* on a activé la sauvegarde locale au moment de la post-edition
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline qui n'est jamais modifié
 */
function tradlang_post_edition($flux){
	if($flux['args']['table'] == "spip_tradlangs"){
		include_spip('inc/config');
		$config = lire_config('tradlang');
		if (!is_array($config))
			return $flux;
		if(($config['sauvegarde_locale'] == 'on') && ($config['sauvegarde_post_edition'] == 'on')){
			include_spip('tradlang_fonctions');
			if($dir_lang = tradlang_dir_lang()){
				$infos = sql_fetsel('lang,module',$flux['args']['table'],'id_tradlang='.intval($flux['args']['id_objet']));
				$module = sql_fetsel('*','spip_tradlang_modules','module='.sql_quote($infos['module']));
				$sauvegarder_module = charger_fonction('tradlang_sauvegarde_module','inc');
				$sauvegarder_module($module['module'],$infos['lang'],$dir_lang);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_boucle (SPIP)
 * 
 * Si on est dans la boucle calculer_langues_utilisees (utilisée dans un formulaire de configuration de l'espace privé), 
 * on ne renvoit pas les langues des tradlangs pour éviter de bloquer ces langues dans la configuration du multilinguisme
 * 
 * @param object $boucle
 * @return object $boucle
 */
function tradlang_pre_boucle($boucle){
	if(isset($boucle->nom) && ($boucle->nom == 'calculer_langues_utilisees') && ($boucle->id_boucle == 'tradlangs'))
		array_unshift($boucle->where,array("'='", "'$id_table." ."id_tradlang'", "'0'"));
	return $boucle;
}

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 * 
 * Sur la fiche des auteurs, on ajoute la liste des révisions de chaines de l'auteur
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte modifié si besoin
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
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * 
 * Ajouter un champ langues préférées sur le formulaire CVT editer_auteur
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
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * Ajouter la valeur langues_preferees dans la liste des champs de la fiche auteur
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function tradlang_formulaire_charger($flux){
	// si le charger a renvoye false ou une chaine, ne rien faire
	if (is_array($flux['data']) && ($flux['args']['form']=='editer_auteur')){
		$flux['data']['langues_preferees'] = '';
		if ($id_auteur = intval($flux['data']['id_auteur']))
			$flux['data']['langues_preferees'] = sql_getfetsel('langues_preferees','spip_auteurs','id_auteur='.intval($id_auteur));
	}
	return $flux;
}

/**
 * Insertion dans le pipeline forum_objets_depuis_env (Plugin Forum)
 * On ajoute la possibilité d'avoir des forums sur les chaines de langue
 * 
 * @param array $array
 * @return array $array
 */
function tradlang_forum_objets_depuis_env($array){
	$array['tradlang'] = id_table_objet('tradlang');
	return $array;
}

/**
 * Insertion dans le pipeline revisions_chercher_label (Plugin révisions)
 * Utiliser de bons labels pour nos champs sous révision
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function tradlang_revisions_chercher_label($flux){
	if($flux['args']['objet'] == 'tradlang' && in_array($flux['args']['champ'],array('str','traducteur','statut')))
		$flux['data'] = 'tradlang:info_'.$flux['args']['champ'];
	return $flux;
}
?>