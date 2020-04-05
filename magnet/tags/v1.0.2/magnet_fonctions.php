<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * informer si les magnets sont actifs sur un objet ou non (par defaut sur les articles uniquement)
 *
 * @param string $type
 * @return bool
 */
function magnet_actif_sur_objet($type){
	static $actifs;
	if (is_null($actifs)){
		if (!function_exists('lire_config')){
			include_spip('inc/config');
		}
		$actifs = lire_config("magnet/objets",array('spip_articles'));
		$actifs = array_map('objet_type',$actifs);
	}
	$type = objet_type($type);
	if (in_array($type,$actifs)){
		return true;
	}
	return false;
}


/**
 * Recuperer la liste des ids d'un type d'objet
 * #MAGNET_LISTE_IDS{article}
 * #MAGNET_LISTE_IDS{article, maselection}
 *
 * Peut etre utilisee pour faire {id_article IN #MAGNET_LISTE_IDS{article}}
 * (alors equivalent a {magnet})
 *
 * @param object $p
 * @return object
 */
function balise_MAGNET_LISTE_IDS_dist($p) {
	$_objet = interprete_argument_balise(1,$p);
	if (!$_objet) {
		$err_b_s_a = array('zbug_balise_sans_argument', array('balise' => 'MAGNET_LISTE_IDS'));
		erreur_squelette($err_b_s_a, $p);
	}
	else {
		$_pile = interprete_argument_balise(2,$p);
		if (!$_pile) {
			$_pile = "''";
		}
		$p->code = "magnet_liste_ids($_objet, $_pile, '')";
		var_dump($p->code);
	}

	$p->interdire_scripts = false;
	return $p;
}


/**
 * Critere {magnet}
 * {magnet} permet de selectionner uniquement les magnet
 * {!magnet} permet d'exclure les magnet
 * @param string $idb
 * @param array $boucles
 * @param Object $crit
 */
function critere_magnet_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (magnet_actif_sur_objet($boucle->type_requete)){
		$w = array($crit->not?"'='":"'<>'","'magnet'","0");
		$serveur = $boucle->sql_serveur;
		$connexion = $GLOBALS['connexions'][$serveur ? strtolower($serveur) : 0];
		if (strncmp($connexion['type'], 'sqlite', 6) == 0) {
			$boucle->where[] = $w;
		}
		else {
			$boucle->having[] = $w;
		}
		$boucle->modificateur['criteres']['magnet'] = true;
	}
}

/**
 * Critere {magnet_pile nomdelapile}
 * permet de selectionner une pile nommee plutot que la pile par defaut
 * @param string $idb
 * @param array $boucles
 * @param Object $crit
 */
function critere_magnet_pile_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0]) and
			$_pile = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent)) {
		$boucle->modificateur['magnet_pile'] = $_pile;
		$boucle->modificateur['criteres']['magnet_pile'] = true;
	}
}

/**
 * Critere {ignore_magnet} permet de desactiver la magnetisation des objets
 * qui retrouvent leur ordre naturel
 * @param string $idb
 * @param array $boucles
 * @param Object $crit
 */
function critere_ignore_magnet_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (magnet_actif_sur_objet($boucle->type_requete)){
		$boucle->modificateur['ignore_magnet'] = true;
	}
}

/**
 * Generer les boutons pour d'admin magnet selon les droits du visiteur
 *
 * @param object $p
 * @return object
 */
function balise_BOUTONS_ADMIN_MAGNET_dist($p) {
	$_pile_arg = '';
	if (($_pile = interprete_argument_balise(1,$p))===NULL){
		$_pile = "''";
	} else {
		$_pile_arg = ",\''.addslashes(".$_pile.").'\'";
		if (($_label = interprete_argument_balise(2,$p))!==NULL) {
			$_pile_arg .= ",\''.addslashes(".$_label.").'\'";
		}
	}

	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	if ($table = $p->boucles[$b]->type_requete){
		$type = objet_type($table);
		$_id = champ_sql(id_table_objet($type), $p);
		$_objet = "'$type'";
	}
	else {
		$_id = champ_sql('id_objet', $p);
		$_objet = champ_sql('objet', $p);
	}

		$p->code = "
'<'.'?php
	if (isset(\$GLOBALS[\'visiteur_session\'][\'statut\'])
	  AND \$GLOBALS[\'visiteur_session\'][\'statut\']==\'0minirezo\'
		AND (\$id = '.intval($_id).')
		AND include_spip(\'inc/autoriser\')
		AND autoriser(\'administrermagnet\','.sql_quote($_objet).',\$id)
		AND include_spip(\'magnet_fonctions\')) {
			echo \"<div class=\'boutons spip-admin actions magnets pile-'.$_pile.'\'>\"
			. magnet_html_boutons_admin('.sql_quote($_objet).',\$id,\'admin-magnet\'$_pile_arg)
			. \"<style>.bouton_action_post.spip-admin-boutons.admin-magnet-'.$_objet.'{display:none;}</style></div>\";
		}
?'.'>'";

	$p->interdire_scripts = false;
	return $p;
}


/**
 * Inserer la clause order : le champ magnet prend 0 pour les objets non magnet et un indice croissant pour les objets magnet
 * le dernier magnetize arrive en premier
 * pour remonter un objet magnet en tete il faut le demagnetizer/remagnetizer
 * @param $boucle
 * @return mixed
 */
function magnet_pre_boucle($boucle){
	if (!isset($boucle->modificateur['ignore_magnet'])
	  AND !defined('_IGNORE_MAGNET')
	  AND (!test_espace_prive() OR isset($boucle->modificateur['criteres']['magnet']) OR isset($boucle->modificateur['criteres']['magnet_pile']))){
		if (magnet_actif_sur_objet($boucle->type_requete)){
			$_pile = (isset($boucle->modificateur['magnet_pile'])?$boucle->modificateur['magnet_pile']:"''");
			$_id = $boucle->id_table . "." . $boucle->primary;
			$magnet = true;
			// si la boucle a un critere id_xxx=yy non conditionnel on ne magnet pas (perf issue)
			if (isset($boucle->modificateur['criteres'][$boucle->primary])){
				foreach($boucle->where as $where){
					if (is_array($where)
						AND $where['0']=="'='"
						AND $where['1']=="'".$_id."'"){
						$magnet = false;
						break;
					}
				}
			}
			if ($magnet){
				$_list = "implode(',',array_reverse(magnet_liste_ids('".addslashes($boucle->type_requete)."', $_pile)))";
				$boucle->select[] = "FIELD($_id,\".$_list.\") as magnet";
				if (count($boucle->default_order) AND !count($boucle->order)){
					while(count($boucle->default_order)){
						$boucle->order[] = array_shift($boucle->default_order);
					}
				}
				array_unshift($boucle->order, "'magnet DESC'");
			}
		}
	}
	return $boucle;
}

/**
 * Generer le HTML des boutons d'admin magnet
 *
 * @param $objet
 * @param $id_objet
 * @param string $class
 * @param string $pile
 * @param string $libelle
 * @return string
 */
function magnet_html_boutons_admin($objet, $id_objet, $class="", $pile='', $libelle=''){
	static $done = false;
	if (!function_exists('generer_action_auteur'))
		include_spip('inc/actions');
	if (!function_exists('bouton_action'))
		include_spip('inc/filtres');
	$bouton_action = chercher_filtre("bouton_action");

	$pile_arg = ($pile?"-$pile":"");
	$magnet_rang = magnet_rang($objet, $id_objet, $pile);
	$ur_action = generer_action_auteur("magnetize",$objet."-".$id_objet."-".($magnet_rang?"off":"on").$pile_arg,self());
	$balise_img = chercher_filtre("balise_img");
	$bclass = $class . " magnet ";
	if ($magnet_rang) {
		$bclass .= "magnetized";
		$label = "<i></i>($magnet_rang) <span>"._T('magnet:label_demagnetize')."</span>";
		$boutons = $bouton_action($label,$ur_action,$bclass);
		$ext = "png";
		if (defined('_SPIP_VERSION_ID') and _SPIP_VERSION_ID>=30205) {
			$ext = "svg";
		}
		if ($magnet_rang>1){
			$ur_action = generer_action_auteur("magnetize",$objet."-".$id_objet."-"."up".$pile_arg,self());
			$b = $bouton_action($balise_img(_DIR_PLUGIN_MAGNET."img/magnet-up.$ext","monter"),$ur_action, '','',_T('magnet:label_up'));
			$boutons =  "<span class=\"$class magnet-up\">$b</span>" . $boutons;
		}
		if ($magnet_rang<magnet_count($objet, $pile)){
			$ur_action = generer_action_auteur("magnetize",$objet."-".$id_objet."-"."down".$pile_arg,self());
			$b = $bouton_action($balise_img(_DIR_PLUGIN_MAGNET."img/magnet-down.$ext","descendre"),$ur_action, '' ,'',_T('magnet:label_down'));
			$boutons =  "<span class=\"$class magnet-down\">$b</span>" . $boutons;
		}
	}
	else {
		$bclass .= "demagnetized";
		$label = "<i></i><span>"._T('magnet:label_magnetize')."</span>";
		$boutons = $bouton_action($label,$ur_action,$bclass);
	}

	if ($pile or $libelle){
		$boutons = "<strong>".($libelle ? $libelle : _T("magnets_pile:".strtolower($pile)))."</strong> " . $boutons;
	}

	if (!$done){
		$done = true;
		$css_file = direction_css(find_in_path("css/magnet-admin.css"));
		$css = spip_file_get_contents($css_file);
		$css = urls_absolues_css($css,$css_file);
		$boutons .= "<style>$css</style>";
	}


	return $boutons;
}


/**
 * Ajouter un bouton pour magnetiser/demagnetiser un objet
 * @param $flux
 * @return mixed
 */
function magnet_formulaire_admin($flux){
	if (
		isset( $flux['args']['contexte']['objet'])
		AND $objet = $flux['args']['contexte']['objet']
		AND magnet_actif_sur_objet($objet)
		AND $id_objet = intval($flux['args']['contexte']['id_objet'])
		AND isset($GLOBALS['visiteur_session']['statut'])
		AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
		AND include_spip('inc/autoriser')
		AND autoriser('administrermagnet',$objet,$id_objet))
	{
		$class = "spip-admin-boutons admin-magnet admin-magnet-$objet";
		if (test_espace_prive()) {
			$class .= " spip-admin-ecrire";
		}
		$boutons = magnet_html_boutons_admin($objet, $id_objet, $class) . " ";
		$p = strpos($flux['data'],"<a");
		$flux['data'] = substr_replace($flux['data'],$boutons,$p,0);
	}
	return $flux;
}

/**
 * Nom de la meta qui stocke les id magnets d'un objet et d'une pile
 * @param string $objet
 * @param string $pile
 *   nom de la pile ou sinon pile par defaut
 * @return string
 */
function magnet_nom_meta_pile($objet, $pile = '') {
	if (!function_exists('table_objet')) {
		include_spip('base/objets');
	}
	$meta_magnet = "magnet_"
		. ($pile ? $pile . "_" : "" )
		. table_objet($objet);

	return $meta_magnet;
}

/**
 * Lister les ids magnetises d'un objet et d'une pile
 * @param string $objet
 * @param string $pile
 * @param string $defaut
 *   valeur par defaut de la meta si non definie ou zero
 * @return array|string
 */
function magnet_liste_ids($objet, $pile, $defaut = '0') {
	$meta_magnet = magnet_nom_meta_pile($objet, $pile);
	// ne pas renvoyer une liste vide
	$ids = $defaut;
	if (isset($GLOBALS['meta'][$meta_magnet]) and $GLOBALS['meta'][$meta_magnet]) {
		$ids = trim($GLOBALS['meta'][$meta_magnet]);
	}
	if (!strlen($ids)) {
		return array();
	}
	$ids = explode(',', $ids);
	$ids = array_map('trim', $ids);
	$ids = array_map('intval', $ids);
	return $ids;
}

/**
 * Renvoie le rang de l'objet :
 * 1 si arrive en tete de la boucle,
 * 2 ensuite ....
 * 0 si n'est pas magnetise
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $pile
 * @return bool|mixed
 */
function magnet_rang($objet, $id_objet, $pile=''){
	$magnets = magnet_liste_ids($objet, $pile, '');
	if (!in_array($id_objet, $magnets)) {
		return false;
	}
	return array_search($id_objet,$magnets)+1;
}

/**
 * Compter le nombre d'objet magnetises
 * @param string $objet
 * @param string $pile
 * @return int
 */
function magnet_count($objet, $pile=''){
	$magnets = magnet_liste_ids($objet, $pile, '');
	return count($magnets);
}

function magnet_boite_infos($flux){
	if (magnet_actif_sur_objet($flux['args']['type'])
	  AND $id = $flux['args']['id']){
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/magnet-admin',array('id_objet'=>$id,'objet'=>$flux['args']['type']));
	}
	return $flux;
}
