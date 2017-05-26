<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * b_b
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * Distribue sous licence GNU/GPL
 *
 * Fonctions spécifiques à Diogene
 *
 * @package SPIP\Diogene\Fonctions
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Redéfinition de la balise #URL_ARTICLE
 * https://code.spip.net/@balise_URL_ARTICLE_dist
 *
 * Si l'article n'existe pas ou n'est pas publié, on envoie vers la page publique de publication
 * Pratique pour les liens vers associé à une auteur mais pas encore publiés
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée du code compilé
 */
function balise_URL_ARTICLE($p) {
	include_spip('balise/url_');
	// Cas particulier des boucles (SYNDIC_ARTICLES)
	if ($p->type_requete == 'syndic_articles') {
		$code = champ_sql('url', $p);
		$p->code = "vider_url($code)";
	} else {
		$code = generer_generer_url('article', $p);
		$_id = interprete_argument_balise(1, $p);
		if (!$_id) {
			$_id = champ_sql('id_article', $p);
		}
		$p->code = "generer_url_publier($_id,'article','',false)";

		$p->interdire_scripts = false;
	}
	return $p;
}

/**
 * Fonction calculant le nombre d'objets qu'un utilisateur peut encore créer
 * Utilisé que sur les objets à base d'articles
 *
 * @param int
 * 		$id_diogene : l'identifiant numérique du diogene
 * @return string|int|false
 * 		le retour à trois types de valeurs :
 * 		- string "infinite" : le nombre est infini
 * 		- boolean false : il y a une erreur, pas de limite pour ce genre d'objet
 * 		- int : le nombre possible
 */
function diogene_nombre_attente($id_diogene) {
	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	if (!intval($id_auteur) || ($id_auteur < 1)) {
		return false;
	}
	if ($GLOBALS['visiteur_session']['statut'] == '0minirezo') {
		return 'infinite';
	}

	$diogene = sql_fetsel(
		'id_secteur,nombre_attente',
		'spip_diogenes',
		'id_diogene='.intval($id_diogene).' AND objet IN ("article","emballe_media")'
	);

	if ($diogene['nombre_attente'] == 0) {
		return 'infinite';
	}

	$nb_articles = sql_countsel(
		'spip_articles as art LEFT JOIN spip_auteurs_liens as lien ON lien.objet="article"
			AND art.id_article=lien.id_objet',
		'lien.id_auteur='.intval($id_auteur).'
			AND art.id_secteur='.intval($diogene['id_secteur']).'
			AND statut NOT IN ("publie","poubelle")'
	);

	$nombre_attente = ($diogene['nombre_attente'] - $nb_articles);
	if ($nombre_attente < 0) {
		$nombre_attente = 0;
	}

	return intval($nombre_attente);
}

// TODO : passer le define dans une valeur de config
if (!test_espace_prive() and (defined('_DIOGENE_MODIFIER_PUBLIC') ? _DIOGENE_MODIFIER_PUBLIC : true)) {
	function generer_url_ecrire_article($id, $args, $ancre, $public, $connect) {
		return url_absolue(generer_url_publier($id, 'article', null, true));
	}
}

/**
 * Génération d'une url vers la page de publication d'un objet
 *
 * @param int $id
 * 		Identifiant numérique de l'objet
 * @param string
 * 		$objet Le type de l'objet
 * @param boolean
 * 		$forcer Dans le cas où l'objet est déjà publié cela renverra vers la page de l'objet. Si $forcer = true,
 * 		cela forcera le fait d'aller sur la page de modification de l'objet
 * @return string $url
 * 		L'URL de la page que l'on souhaite
 */
function generer_url_publier($id = null, $objet = 'article', $id_secteur = 0, $forcer = true, $forcer_ecrire = 'non') {
	include_spip('inc/urls');
	
	if (!function_exists('objet_test_si_publie')) {
		include_spip('base/objets');
	}
	
	if (!function_exists('objet_info')) {
		include_spip('inc/filtres');
	}
	
	/**
	 * Si on ne force pas et si l'objet est publie
	 * on envoit vers la page publique de l'objet
	 */
	if ($forcer === false and objet_test_si_publie($objet, $id)) {
		return generer_url_entite($id, $objet);
	} else if (($forcer_ecrire == 'non' or !$forcer_ecrire) and is_numeric($id)) {
		$fields = objet_info($objet, 'field');
		if (isset($fields['id_secteur'])) {
			$table = table_objet_sql($objet);
			$id_table_objet = id_table_objet($objet) ? id_table_objet($objet) : 'id_article';
			
			$objets[] = $objet;
			if ($objet == 'article') {
				$objets[] = 'emballe_media';
				$objets[] = 'page';
			}
			
			$id_secteur = sql_getfetsel('id_secteur', $table, $id_table_objet.'='.intval($id));
			if (intval($id_secteur) > 0) {
				$type_objet = sql_getfetsel(
					'type',
					'spip_diogenes',
					'id_secteur='.intval($id_secteur).' AND '.sql_in('objet', $objets)
				);
				if ($type_objet) {
					$page_publier = defined('_PAGE_PUBLIER') ? _PAGE_PUBLIER : 'publier';
					$url = generer_url_public($page_publier, 'type_objet='.$type_objet, '', true);
				}
			}
		}
	}
	
	$a = id_table_objet($objet) . '=' . intval($id);
	$url = generer_url_ecrire(objet_info($objet, 'url_voir'), $a);
	
	return $url;
}

/**
 * Fonction retournant la chaine de langue depuis un statut
 *
 * @param string $statut
 * 		Le statut de l'objet
 * @param string $type
 * 		Le type d'objet SPIP
 * @return string
 * 		La locution adéquate pour le statut
 */
function diogene_info_statut($statut, $type = 'article') {
	$statuts = objet_info($type, 'statut_titres');
	if (!is_array($statuts)) {
		$statuts = objet_info($type, 'statut_textes_instituer');
	}
	if (is_array($statuts) && array_key_exists($statut, $statuts)) {
		return _T($statuts[$statut]);
	} else {
		switch ($type) {
			case 'article':
				$etats = array_flip($GLOBALS['liste_des_etats']);
				return _T($etats[$statut]);
			case 'rubrique':
				$etats = array_flip($GLOBALS['liste_des_etats']);
				if (isset($etats[$statut])) {
					return _T($etats[$statut]);
				} elseif ($statut == 'new') {
					return _T('diogene:info_rubrique_new');
				} elseif ($statut == 0) {
					/**
					 * Rubrique qui a été dépubliée
					 * cf depublier_rubrique_if() dans inc/rubriques
					 */
					return _T('diogene:info_rubrique_vide');
				} else {
					return $statut;
				}
		}
	}
	return;
}

/**
 * Être sûr d'avoir les fonctions des puces
 */
if (!function_exists('puce_statut')) {
	include_spip('inc/autoriser');
	include_spip('inc/puce_statut');
}

if (!function_exists('puce_statut_rubrique')) {
	/**
	 * Surcharge de la fonction puce_statut_dist() de inc/puce_statut
	 *
	 * @param $id_objet int L'id_rubrique
	 * @param $statut string Le statut de la rubrique
	 * @param $id_parent int
	 * @param $type string 'rubrique'
	 * @param $ajax
	 *
	 * @return un tag image <img src... /> ou le string du statut
	 */
	function puce_statut_rubrique($id_objet, $statut, $id_parent, $type, $ajax = '') {
		if (test_espace_prive()) {
			return puce_statut_rubrique_dist($id_objet, $statut, $id_parent, $type, $ajax = '');
		} else {
			switch ($statut) {
				case 'publie':
					$img = 'puce-verte.gif';
					$alt = _T('diogene:info_rubrique_publie');
					return http_img_pack($img, $alt);
				/**
				 * Nouvelle rubrique cr&eacute;&eacute;e
				 */
				case 'new':
					$img = 'puce-blanche.gif';
					$alt = _T('diogene:info_rubrique_new');
					return http_img_pack($img, $alt);
				/**
				 * Rubrique qui a été dépubliée
			 	 * cf depublier_rubrique_if() dans inc/rubriques
				 */
				case '0':
					$img = 'puce-blanche.gif';
					$alt = _T('diogene:info_rubrique_new');
					return http_img_pack($img, $alt);
				default:
					return $statut;
			}
		}
	}
}

/**
 * Generer un lien d'aide (icone + lien)
 *
 * @param string $aide
 * 		cle d'identification de l'aide souhaitee
 * @param string $skel
 * 		Nom du squelette qui appelle ce bouton d'aide
 * @param array $env
 * 		Environnement du squelette
 * @param bool $aide_spip_directe
 * 		false : Le lien genere est relatif a notre site (par defaut)
 * 		true : Le lien est realise sur spip.net/aide/ directement ...
 * @return
**/
function inc_aider($aide = '', $skel = '', $env = array(), $aide_spip_directe = false) {
	global $spip_lang, $aider_index;
	include_spip('inc/aider');
	if (($skel = basename($skel))
		and isset($aider_index[$skel])
		and isset($aider_index[$skel][$aide])) {
		$aide = $aider_index[$skel][$aide];
	}

	if ($aide_spip_directe) {
		// on suppose que spip.net est le premier present
		// dans la liste des serveurs. C'est forcement le cas
		// a l'installation tout du moins
		$help_server = $GLOBALS['help_server'];
		$url = array_shift($help_server) . '/';
		$url = parametre_url($url, 'exec', 'aide');
		$url = parametre_url($url, 'aide', $aide);
		$url = parametre_url($url, 'var_lang', $spip_lang);
	} else {
		$args = "aide=$aide&var_lang=$spip_lang";
		if (test_espace_prive()) {
			$url = generer_url_ecrire('aide', $args);
		} else {
			$url = generer_url_public('aide_spip', $args);
		}
	}
	return aider_icone($url);
}

function diogene_puce_statut($id_objet, $type, $statut, $id_parent = '0') {
	$puce_statut = charger_fonction('puce_statut', 'inc');
	return $puce_statut($id_objet, $statut, $id_parent, $type, false, null);
}

/**
 * Pour PHP < 5.3.0
 *
 * Une définition de la fonction lcfirst
 *
 * @param string $texte
 * 		Le texte que l'on souhaite modifier
 * @return string $texte
 * 		Le texte modifié
 */
if (!function_exists('lcfirst')) {
	function lcfirst($texte) {
		$texte{0} = strtolower($texte{0});
		return $texte;
	}
}
