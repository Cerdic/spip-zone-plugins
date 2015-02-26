<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

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
	if ($boucle->type_requete=='articles'){
		$boucle->having[] = array($crit->not?"'='":"'<>'","'magnet'","0");
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
	if (isset($crit->param[0][0])
	  AND $crit->param[0][0]->type=="texte"
		AND ($pile = $crit->param[0][0]->texte)){
		$boucle->modificateur['magnet_pile'] = $pile;
	}
}

/**
 * Critere {ignore_magnet} permet de desactiver la magnetisation des articles
 * qui retrouvent leur ordre naturel
 * @param string $idb
 * @param array $boucles
 * @param Object $crit
 */
function critere_ignore_magnet_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if ($boucle->type_requete=='articles'){
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
	if (($_pile = interprete_argument_balise(1,$p))===NULL)
		$_pile = "''";
	else {
		$_pile_arg = ",".addslashes($_pile);
	}

	$_id = champ_sql('id_article', $p);
	$_objet = "\'article\'";

		$p->code = "
'<'.'?php
	if (isset(\$GLOBALS[\'visiteur_session\'][\'statut\'])
	  AND \$GLOBALS[\'visiteur_session\'][\'statut\']==\'0minirezo\'
		AND (\$id = '.intval($_id).')
		AND	include_spip(\'inc/autoriser\')
		AND autoriser(\'administrermagnet\',$_objet,\$id)
		AND include_spip(\'magnet_fonctions\')) {
			echo \"<div class=\'boutons spip-admin actions magnets pile-'.$_pile.'\'>\"
			. magnet_html_boutons_admin($_objet,\$id,\'admin-magnet\'$_pile_arg)
			. \"<style>.bouton_action_post.spip-admin-boutons{display:none;}</style></div>\";
		}
?'.'>'";

	$p->interdire_scripts = false;
	return $p;
}


/**
 * Inserer la clause order : le champ magnet prend 0 pour les articles non magnet et un indice croissant pour les articles magnet
 * le dernier magnetize arrive en premier
 * pour remonter un article magnet en tete il faut le demagnetizer/remagnetizer
 * @param $boucle
 * @return mixed
 */
function magnet_pre_boucle(&$boucle){
	if (!isset($boucle->modificateur['ignore_magnet'])
	  AND (!test_espace_prive() OR isset($boucle->modificateur['criteres']['magnet']) OR isset($boucle->modificateur['criteres']['magnet_pile']))){
		if ($boucle->type_requete=='articles'){
			$pile = (isset($boucle->modificateur['magnet_pile'])?$boucle->modificateur['magnet_pile']:'');
			$meta_magnet = "magnet_" .($pile?$pile."_":""). $boucle->type_requete;
			$_id = $boucle->id_table . "." . $boucle->primary;
			$magnet = true;
			// si la boucle a un critere id_article=xx non conditionnel on ne magnet pas (perf issue)
			if (isset($boucle->modificateur['criteres']['id_article'])){
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
				$_list = "implode(',',array_reverse(array_map('intval',explode(',',isset(\$GLOBALS['meta']['$meta_magnet'])?\$GLOBALS['meta']['$meta_magnet']:'0'))))";
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
 * @return string
 */
function magnet_html_boutons_admin($objet, $id_objet, $class="", $pile=''){
	static $done = false;
	if (!function_exists('generer_action_auteur'))
		include_spip('inc/actions');
	if (!function_exists('bouton_action'))
		include_spip('inc/filtres');

	$pile_arg = ($pile?"-$pile":"");
	$magnet_rang = magnet_rang($objet, $id_objet, $pile);
	$ur_action = generer_action_auteur("magnetize",$objet."-".$id_objet."-".($magnet_rang?"off":"on").$pile_arg,self());
	$balise_img = chercher_filtre("balise_img");
	$bclass = $class . " magnet ";
	if ($magnet_rang) {
		$bclass .= "magnetized";
		$label = "<i></i>($magnet_rang) <span>"._T('magnet:label_demagnetize')."</span>";
		$boutons = bouton_action($label,$ur_action,$bclass);
		if ($magnet_rang>1){
			$ur_action = generer_action_auteur("magnetize",$objet."-".$id_objet."-"."up".$pile_arg,self());
			$boutons = bouton_action($balise_img(_DIR_PLUGIN_MAGNET."img/magnet-up-24.png","monter"),$ur_action, $class ." magnet-up",'',_T('magnet:label_up')) . $boutons;
		}
		if ($magnet_rang<magnet_count($objet)){
			$ur_action = generer_action_auteur("magnetize",$objet."-".$id_objet."-"."down".$pile_arg,self());
			$boutons = bouton_action($balise_img(_DIR_PLUGIN_MAGNET."img/magnet-down-24.png","descendre"),$ur_action, $class ." magnet-down",'',_T('magnet:label_down')) . $boutons;
		}
	}
	else {
		$bclass .= "demagnetized";
		$label = "<i></i><span>"._T('magnet:label_magnetize')."</span>";
		$boutons = bouton_action($label,$ur_action,$bclass);
	}

	if ($pile){
		$boutons = "<strong>"._T("magnets_pile:".strtolower($pile))."</strong> " . $boutons;
	}

	if (!$done){
		$done = true;
		$css_file = find_in_path("css/magnet-admin.css");
		$css = spip_file_get_contents($css_file);
		$css = urls_absolues_css($css,$css_file);
		$boutons .= "<style>$css</style>";
	}


	return $boutons;
}


/**
 * Ajouter un bouton pour magnetiser/demagnetiser un article
 * @param $flux
 * @return mixed
 */
function magnet_formulaire_admin($flux){
	if ($flux['args']['contexte']['objet']=='article'
	  AND $objet = $flux['args']['contexte']['objet']
	  AND $id_objet = intval($flux['args']['contexte']['id_objet'])
	  AND isset($GLOBALS['visiteur_session']['statut'])
		AND $GLOBALS['visiteur_session']['statut']=='0minirezo'
	  AND include_spip('inc/autoriser')
	  AND autoriser('administrermagnet',$objet,$id_objet)){
		$boutons = magnet_html_boutons_admin($objet, $id_objet,"spip-admin-boutons admin-magnet") . " ";
		$p = strpos($flux['data'],"<a");
		$flux['data'] = substr_replace($flux['data'],$boutons,$p,0);
	}
	return $flux;
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
	$meta_magnet = "magnet_" .($pile?$pile."_":""). table_objet($objet);
	$magnets = (isset($GLOBALS['meta'][$meta_magnet])?$GLOBALS['meta'][$meta_magnet]:'0');
	$magnets = explode(',',$magnets);
	if (!in_array($id_objet, $magnets))
		return false;
	return array_search($id_objet,$magnets)+1;
}

/**
 * Compter le nombre d'objet magnetises
 * @param string $objet
 * @param string $pile
 * @return int
 */
function magnet_count($objet, $pile=''){
	$meta_magnet = "magnet_" .($pile?$pile."_":""). table_objet($objet);
	$magnets = (isset($GLOBALS['meta'][$meta_magnet])?$GLOBALS['meta'][$meta_magnet]:'');
	$magnets = explode(',',$magnets);
	return count($magnets);
}

function magnet_boite_infos($flux){
	if ($flux['args']['type']=='article'
	  AND $id_article = $flux['args']['id']){
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/magnet-admin',array('id_article'=>$id_article));
	}
	return $flux;
}