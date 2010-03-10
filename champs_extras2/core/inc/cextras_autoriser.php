<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline autoriser
function cextras_autoriser(){
	include_spip('config/mes_autorisations');
}


/**
  * Autorisation de voir un champ extra
  * autoriser('voirextra','auteur_prenom', $id_auteur);
  * -> autoriser_auteur_prenom_voirextra_dist() ...
  */
function autoriser_voirextra_dist($faire, $type, $id, $qui, $opt){
	return true;
}

/**
  * Autorisation de modifier un champ extra
  * autoriser('modifierextra','auteur_prenom', $id_auteur);
  * -> autoriser_auteur_prenom_modifierextra_dist() ...
  */
function autoriser_modifierextra_dist($faire, $type, $id, $qui, $opt){
	return true;
}



/**
 *
 * API pour aider les plus demunis
 * Permet d'indiquer que tels champs extras se limitent a telle ou telle rubrique
 * et cela en creant a la volee les fonctions d'autorisations adequates.
 * 
 * Exemples :
 *   restreindre_extras('article', array('nom', 'prenom'), array(8, 12));
 *   restreindre_extras('site', 'url_doc', 18, true); // recursivement aux sous rubriques
 *
 * @param string $objet      objet possedant les extras
 * @param mixed  $noms       nom des extras a restreindre
 * @param mixed  $ids        identifiant (des rubriques par defaut) sur lesquelles s'appliquent les champs
 * @param bool   $recursif   application recursive sur les sous rubriques ? ATTENTION, c'est gourmand en requetes SQL :)
 * @param string $cible      type de la fonction de test qui sera appelee, par defaut "rubrique". Peut aussi etre "groupe" ou des fonctions definies
 *
 * @return bool : true si on a fait quelque chose
 */
function restreindre_extras($objet, $noms=array(), $ids=array(), $recursif=false, $cible='rubrique') {
	if (!$objet or !$noms or !$ids) {
		return false;
	}

	if (!is_array($noms)) { $noms = array($noms); }
	if (!is_array($ids))  { $ids  = array($ids); }

	$objet = objet_type($objet);
	$ids = var_export($ids, true);
	$recursif = var_export($recursif, true);

	$m = '_modifierextra_dist';
	$v = '_voirextra_dist';
	foreach ($noms as $nom) {
		$f = "autoriser_$objet" . "_$nom";
		$code = "
			if (!function_exists('$f$m')) {
				function $f$m(\$faire, \$type, \$id, \$qui, \$opt) {
					return _restreindre_extras_objet('$objet', \$id, \$opt, $ids, $recursif, '$cible');
				}
			}
			if (!function_exists('$f$v')) {
				function $f$v(\$faire, \$type, \$id, \$qui, \$opt) {
					return autoriser('modifierextra', \$type, \$id, \$qui, \$opt);
				}
			}
		";
#		echo $code;
		eval($code);
	}

	return true;
}

/**
 *
 * Fonction d'autorisation interne a la fonction restreindre_extras()
 * Teste si un objet a le droit d'afficher des champs extras
 * en fonction de la rubrique (ou autre defini dans la cible)
 * dans laquelle il se trouve et des rubriques autorisees
 *
 * On cache pour eviter de plomber le serveur SQL, vu que la plupart du temps
 * un hit demandera systematiquement le meme objet/id_objet lorsqu'il affiche
 * un formulaire.
 *
 * @param string $objet      objet possedant les extras
 * @param int    $id_objet   nom des extras a restreindre
 * @param array  $opt        options des autorisations
 * @param mixed  $ids        identifiant(s) (en rapport avec la cible) sur lesquelles s'appliquent les champs
 * @param bool   $recursif   application recursive sur les sous rubriques ? ATTENTION, c'est gourmand en requetes SQL :)
 *
 * @return bool : autorise ou non .
 */
function _restreindre_extras_objet($objet, $id_objet, $opt, $ids, $recursif=false, $cible='rubrique') {
	static $autorise = null;

	if ( $autorise === null )        { $autorise = array(); }
	if ( !isset($autorise[$objet]) ) { $autorise[$objet] = array(); }

	$cle = $cible . implode('-', $ids);
	if (isset($autorise[$objet][$id_objet][$cle])) {
		return $autorise[$objet][$id_objet][$cle];
	}

	$f = charger_fonction("restreindre_extras_objet_sur_$cible", "inc", true);
	if ($f) {
		return $autorise[$objet][$id_objet][$cle] =
			$f($objet, $id_objet, $opt, $ids, $recursif);
	}

	// pas trouve... on n'affiche pas... Pan !
	return $autorise[$objet][$id_objet][$cle] = false;
}


/**
 *
 * Fonction d'autorisation interne a la fonction restreindre_extras()
 * specifique au test d'appartenance a une rubrique
 *
 * @param string $objet      objet possedant les extras
 * @param int    $id_objet   nom des extras a restreindre
 * @param array  $opt        options des autorisations
 * @param mixed  $ids        identifiant(s) des rubriques sur lesquelles s'appliquent les champs
 * @param bool   $recursif   application recursive sur les sous rubriques ? ATTENTION, c'est gourmand en requetes SQL :)
 *
 * @return bool : autorise ou non .
 */
function inc_restreindre_extras_objet_sur_rubrique_dist($objet, $id_objet, $opt, $ids, $recursif) {

    $id_rubrique = $opt['contexte']['id_rubrique'];

    if (!$id_rubrique) {
		// on tente de le trouver dans la table de l'objet
		$table = table_objet_sql($objet);
		$id_table = id_table_objet($table);
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table);
		if (isset($desc['field']['id_rubrique'])) {
			$id_rubrique = sql_getfetsel("id_rubrique", $table, "$id_table=".sql_quote($id_objet));
		}
    }

	if (!$id_rubrique) {
		// on essaie aussi dans le contexte d'appel de la page
		$id_rubrique = _request('id_rubrique');
	}

	if (!$id_rubrique) {
		return false;
	}

    if (in_array($id_rubrique, $ids)) {
        return true;
    }

	// on teste si l'objet est dans une sous rubrique de celles mentionnee...
	if ($id_rubrique and $recursif) {
		$id_parent = $id_rubrique;
		while ($id_parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=" . sql_quote($id_parent))) {
			if (in_array($id_parent, $ids)) {
				return true;
			}
		}
	}
		  
    return false;
}




/**
 *
 * Fonction d'autorisation interne a la fonction restreindre_extras()
 * specifique au test d'appartenance a une rubrique
 *
 * @param string $objet      objet possedant les extras
 * @param int    $id_objet   nom des extras a restreindre
 * @param array  $opt        options des autorisations
 * @param mixed  $ids        identifiant(s) des rubriques sur lesquelles s'appliquent les champs
 * @param bool   $recursif   application recursive sur les sous rubriques ? ATTENTION, c'est gourmand en requetes SQL :)
 *
 * @return bool : autorise ou non .
 */
function inc_restreindre_extras_objet_sur_groupemot_dist($objet, $id_objet, $opt, $ids, $recursif) {

    $id_groupe = $opt['contexte']['id_groupe'];

    if (!$id_groupe) {
		// on tente de le trouver dans la table de l'objet
		$table = table_objet_sql($objet);
		$id_table = id_table_objet($table);
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table);
		if (isset($desc['field']['id_groupe'])) {
			$id_groupe = sql_getfetsel("id_groupe", $table, "$id_table=".sql_quote($id_objet));
		}
    }

	if (!$id_groupe) {
		// on essaie aussi dans le contexte d'appel de la page
		$id_groupe = _request('id_groupe');
	}

	if (!$id_groupe) {
		return false;
	}

    if (in_array($id_groupe, $ids)) {
        return true;
    }

	// on teste si l'objet est dans un sous groupe de celui mentionne...
	// sauf qu'il n'existe pas encore de groupe avec id_parent :) - sauf avec plugin
	// on desactive cette option si cette colonne est absente
	if ($id_groupe and $recursif) {
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table("spip_groupes_mots");
		if (isset($desc['field']['id_parent'])) {
			$id_parent = $id_groupe;
			while ($id_parent = sql_getfetsel("id_parent", "spip_groupes_mots", "id_parent=" . sql_quote($id_parent))) {
				if (in_array($id_parent, $ids)) {
					return true;
				}
			}
		}
	}
		  
    return false;
}
?>
