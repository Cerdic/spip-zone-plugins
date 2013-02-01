<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 *
 * Fonctions spécifiques à Diogene
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Redéfinition de la balise #URL_ARTICLE
 * http://doc.spip.org/@balise_URL_ARTICLE_dist
 * 
 * Si l'article n'existe pas ou n'est pas publier, on envoie vers la page publique de publication
 * Pratique pour les liens vers associé à une auteur mais pas encore publiés
 * 
 * @param unknown_type $p
 */
function balise_URL_ARTICLE($p) {
	include_spip('balise/url_');
	// Cas particulier des boucles (SYNDIC_ARTICLES)
	if ($p->type_requete == 'syndic_articles') {
		$code = champ_sql('url', $p);
		$p->code = "vider_url($code)";
	} else{
		$code = generer_generer_url('article', $p);
		$_id = interprete_argument_balise(1,$p);
		if (!$_id){
			$_id = champ_sql('id_article', $p);
		}
		$p->code = "generer_url_publier($_id,'article','',false)";

		$p->interdire_scripts = false;
	}
	return $p;
}

/**
 * Génération d'une url vers la page de publication d'un objet
 * 
 * @param int $id Identifiant numérique de l'objet
 * @param string $objet Le type de l'objet
 * @param boolean $forcer Dans le cas où l'objet est déjà publié cela renverra vers la page de l'objet. Si $forcer = true,
 * cela forcera le fait d'aller sur la page de modification de l'objet
 * @return string $url L'URL de la page que l'on souhaite
 */
function generer_url_publier($id,$objet='article',$id_secteur=0,$forcer=true){
	include_spip('inc/urls');
	$id_table_objet = id_table_objet($objet) ? id_table_objet($objet) : 'id_article';
	$table = table_objet_sql($objet);
	if(is_numeric($id)){
		$infos_objet = sql_fetsel('statut,id_secteur',$table,$id_table_objet."=".intval($id));
		$id_secteur = $infos_objet['id_secteur']?$infos_objet['id_secteur']:0;
	}
	/**
	 * Si on ne force pas, on envoit vers la page de l'objet
	 */
	if(!$forcer){
		if($infos_objet['statut'] == 'publie'){
			return generer_url_entite($id,$objet);
		}
	}
	$objets[] = $objet;
	if($objet == 'article'){
		$objets[] = 'emballe_media';
		$objets[] = 'page';
	}
	$type_objet = sql_getfetsel('type','spip_diogenes','id_secteur='.$id_secteur.' AND '.sql_in("objet",$objets));
	if($type_objet){
		$url = generer_url_public('publier','type_objet='.$type_objet,'',true);
		if(is_numeric($id)){
			$url = parametre_url($url,$id_table_objet,intval($id));
		}
	}else{
		$url = 'generer_url_ecrire_'.$objet;
		$url = str_replace('&amp;', '&', $url($id, '','', 'prop'));
	}
	return $url;
}

/**
 * Fonction retournant la chaine de langue depuis un statut
 *
 * @param string $statut Le statut de l'objet
 * @param string $type Le type d'objet SPIP
 * @return string La locution adéquate pour le statut 
 */
function diogene_info_statut($statut, $type='article') {
	switch ($type) {
		case 'article':
			$etats = array_flip($GLOBALS['liste_des_etats']);
			return _T($etats[$statut]);
		case 'rubrique':
			$etats = array_flip($GLOBALS['liste_des_etats']);
			if(isset($etats[$statut])){
				return _T($etats[$statut]);
			}
			elseif($statut == 'new'){
				return _T('diogene:info_rubrique_new');
			}
			/**
			 * Rubrique qui a été dépubliée
			 * cf depublier_rubrique_if() dans inc/rubriques
			 */
			elseif($statut == 0){
				return _T('diogene:info_rubrique_vide');
			}
			else{
				return $statut;
			}
	}
	return;
}

if(!function_exists('puce_statut')){
	include_spip('inc/puce_statut');
}

if(!function_exists('puce_statut_rubrique')){
	/**
	 * Surcharge de la fonction puce_statut_dist() de inc/puce_statut
	 *
	 * @param $id int L'id_rubrique
	 * @param $statut string Le statut de la rubrique
	 * @param $id_rubrique int
	 * @param $type string 'rubrique'
	 * @param $ajax
	 *
	 * @return un tag image <img src... /> ou le string du statut
	 */
	function puce_statut_rubrique($id, $statut, $id_rubrique, $type, $ajax='') {
		if(test_espace_prive()){
			return puce_statut_rubrique_dist($id, $statut, $id_rubrique, $type, $ajax='');
		}else{
			switch ($statut) {
				case 'publie':
					$img = 'puce-verte.gif';
					$alt = _T('diogene:info_rubrique_publie');
					return http_img_pack($img, $alt, $atts);
				/**
				 * Nouvelle rubrique cr&eacute;&eacute;e
				 */
				case 'new':
					$img = 'puce-blanche.gif';
					$alt = _T('diogene:info_rubrique_new');
					return http_img_pack($img, $alt, $atts);
				/**
				 * Rubrique qui a été dépubliée
			 	 * cf depublier_rubrique_if() dans inc/rubriques
				 */
				case '0':
					$img = 'puce-blanche.gif';
					$alt = _T('diogene:info_rubrique_new');
					return http_img_pack($img, $alt, $atts);
				default:
					return $statut;
			}
		}
	}
}

function diogene_puce_statut($id_objet,$type,$statut,$id_rubrique=''){
	$puce_statut = charger_fonction('puce_statut','inc');
	return $puce_statut($id_objet, $statut, $id_rubrique, $type);
}

?>