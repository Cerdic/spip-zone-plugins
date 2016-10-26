<?php
/**
 * Plugin Inscription3 pour SPIP
 * © cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Fichier des fonctions spécifiques du plugin
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 *
 * Donne le nom d'un pays en fonction de son id
 *
 * @return false|string false dans le cas ou il ne reçoit pas de paramètres ou si le paramètre n'est pas bon
 * @param int $id_pays L'id_pays de la table spip_geo_pays
 */
if (!function_exists('id_pays_to_pays')) {
	function id_pays_to_pays($id_pays) {
		if ((is_numeric($id_pays)) and ($id_pays != 0)) {
			$pays = sql_getfetsel('nom', 'spip_geo_pays', 'id_pays ='.$id_pays);
			return typo($pays);
		} else {
			return;
		}
	}
}

/**
 * Si pas de fonction lcfirst => PHP < 5.3
 * On définit la fonction comme http://www.php.net/manual/fr/function.lcfirst.php#87176
 */
if (!function_exists('lcfirst')) {
	function lcfirst($str) {
		return strtolower(substr($str, 0, 1)). substr($str, 1);
	}
}
/**
 *
 * Fonction utilisée par le critère i3_recherche
 *
 * @return array Le tableau des auteurs correspondants aux critères de recherche
 * @param string $quoi[optional] Le contenu textuel recherché
 * @param object $ou[optional] Le champs dans lequel on recherche
 * @param object $table[optional]
 */
function i3_recherche($quoi = null, $ou = null, $table = null) {
	if (isset($quoi) and isset($ou)) {
		$quoi = texte_script(trim($quoi));
		include_spip('base/serial'); // aucazou !
		global $tables_principales;

		if (isset($tables_principales[table_objet_sql($table)]['field'][$ou])) {
			$auteurs = sql_get_select('id_auteur', table_objet_sql($table), "$ou LIKE '%$quoi%'");
		} else {
			global $tables_jointures;
			if (isset($tables_jointures[table_objet_sql($table)])
				and ($jointures=$tables_jointures[table_objet_sql($table)])) {
				foreach ($jointures as $jointure => $val) {
					if (isset($tables_principales[table_objet_sql($val)]['field'][$ou])) {
						$auteurs = sql_get_select('id_auteur', table_objet_sql($table)." AS $table LEFT JOIN ".table_objet_sql($val)." AS $val USING(id_auteur)", "$val.$ou LIKE '%$quoi%'");
					}
				}
			}
		}
		return "($auteurs)";
	}
}

/**
 *
 * Critère utilisé pour rechercher dans les utilisateurs (page ?exec=inscription2_adherents)
 *
 */
function critere_i3_recherche_dist($idb, &$boucles) {
	$boucle = &$boucles[$idb];
	$primary = $boucle->primary;
	$ou = '@$Pile[0]["case"]';
	$quoi = '@$Pile[0]["valeur"]';
	$table = $boucle->type_requete;
	$boucle->hash .= "
	\$auteurs = i3_recherche($quoi,$ou,$table);
	";
	$boucle->where[] = array("'IN'","'$boucle->id_table." . "$primary'",'$auteurs');
}

include_spip('inc/cextras_autoriser');
if (isset($GLOBALS['visiteur_session']['statut'])
	and ($GLOBALS['visiteur_session']['statut'] != '0minirezo')
	and function_exists('restreindre_extras')) {
	if (isset($GLOBALS['inscription3'])) {
		$inscription3 = is_array(@unserialize($GLOBALS['inscription3'])) ? unserialize($GLOBALS['inscription3']) : array();
		$champ_testes = array();
		foreach ($inscription3 as $clef => $val) {
			$cle = preg_replace('/_(obligatoire|fiche|table).*/', '', $clef);
			if (!in_array($cle, $champ_testes) and ($val == 'on')) {
				/**
				 * Si on n'autorise pas la modification dans la configuration
				 * ou si le champ en question est "creation"
				 */
				if ($inscription3[$cle.'_fiche_mod'] != 'on') {
					restreindre_extras('auteurs', $cle, '*');
				}
				$champ_testes[] = $cle;
			}
		}
	}
}

if (function_exists('restreindre_extras')) {
	restreindre_extras('auteurs', 'creation', '*');
}

/**
 * Un critère règlement permettant de :
 * - Trouver les pages uniques avec le champ page reglement
 * - Sinon retourner sinon l'article de règlement sélectionné dans la conf
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
function critere_reglement_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_article = false;
	if (defined('_DIR_PLUGIN_PAGES')
		and ($id_article = sql_getfetsel('id_article', 'spip_articles', 'page="reglement"'))) {
		$where = "array('=', '".$boucle->id_table.".".$boucle->primary."', '".$id_article."')";
	}

	if (!$id_article) {
		if (!function_exists('lire_config')) {
			include_spip('inc/config');
		}
		$reglement = lire_config('inscription3/reglement_article', 0);
		if (is_array($reglement)) {
			$reglement = str_replace('article|', '', $reglement[0]);
		}
		if (is_numeric($reglement) and intval($reglement) > 0) {
			$where = "array('=', '".$boucle->id_table.".id_article', '".$reglement."')";
		}
	}
	if (!$where) {
		$where = "array('=', '".$boucle->id_table.".id_article', '0')";
	}
	if ($where) {
		$boucle->where[]= $where;
	}
}

function envoyer_inscription3($desc, $nom, $mode) {
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']['nom_site']);
	$adresse_site = $GLOBALS['meta']['adresse_site'];
	if ($mode == '6forum') {
		$adresse_login = generer_url_public('login');
		$msg = 'form_forum_voici1';
	} else {
		$adresse_login = $adresse_site .'/'. _DIR_RESTREINT_ABS;
		$msg = 'form_forum_voici2';
	}

	$msg = _T('form_forum_message_auto')."\n\n"
		. _T('form_forum_bonjour', array('nom'=>$nom))."\n\n"
		. _T($msg, array('nom_site_spip' => $nom_site_spip,
			'adresse_site' => $adresse_site . '/',
			'adresse_login' => $adresse_login)) . "\n\n- "
		. _T('form_forum_login').' ' . $desc['login'] . "\n- "
		. _T('form_forum_pass'). ' ' . $desc['pass'] . "\n\n";

	return array("[$nom_site_spip] "._T('form_forum_identifiants'), $msg);
}

/**
 *
 * Récupère la valeur d'un champs d'un auteur si on ne possède que le nom du champs
 * Dans le cas de la boucle DATA par exemple
 *
 * @return
 * @param object $champs
 * @param object $id_auteur
 */
function inscription3_recuperer_champs($champs, $id_auteur) {
	if ($champs == 'login') {
		$champs = 'spip_auteurs.login';
	}
	if ($champs == 'pays') {
		$resultat = sql_getfetsel(
			'b.nom',
			'spip_auteurs a LEFT JOIN spip_geo_pays b on a.pays = b.id_pays',
			"a.id_auteur=$id_auteur"
		);
		return typo($resultat);
	}
	$resultat = sql_getfetsel($champs, 'spip_auteurs', 'id_auteur='.intval($id_auteur));
	return typo($resultat);
}
