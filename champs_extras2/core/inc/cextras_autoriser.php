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
 * @param mixed  $rubriques  identifiant des rubriques sur lesquelles s'appliquent les champs
 * @param bool   $recursif   application recursive sur les sous rubriques ? ATTENTION, c'est gourmand en requetes SQL :)
 *
 * @return bool : true si on a fait quelque chose
 */
function restreindre_extras($objet, $noms=array(), $rubriques=array(), $recursif=false) {
	if (!$objet or !$noms or !$rubriques) {
		return false;
	}

	if (!is_array($noms))      { $noms      = array($noms); }
	if (!is_array($rubriques)) { $rubriques = array($rubriques); }

	$objet = objet_type($objet);
	$rubriques = var_export($rubriques, true);
	$recursif = var_export($recursif, true);

	$m = '_modifierextra_dist';
	$v = '_voirextra_dist';
	foreach ($noms as $nom) {
		$f = "autoriser_$objet" . "_$nom";
		$code = "
			if (!function_exists('$f$m')) {
				function $f$m(\$faire, \$type, \$id, \$qui, \$opt) {
					return _restreindre_extras_objet('$objet', \$id, \$opt, $rubriques, $recursif);
				}
			}
			if (!function_exists('$f$v')) {
				function $f$v(\$faire, \$type, \$id, \$qui, \$opt) {
					return autoriser('modifierextra', \$type, \$id, \$qui, \$opt);
				}
			}
		";
		eval($code);
	}

	return true;
}

/**
 *
 * Fonction d'autorisation interne a la fonction restreindre_extras()
 * Teste si un objet a le droit d'afficher des champs extras
 * en fonction de la rubrique dans laquelle il se trouve et des rubriques autorisees
 *
 * On cache pour eviter de plomber le serveur SQL, vu que la plupart du temps
 * un hit demandera systematiquement le meme objet/id_objet lorsqu'il affiche
 * un formulaire.
 *
 * @param string $objet      objet possedant les extras
 * @param int    $id_objet   nom des extras a restreindre
 * @param array  $opt        options des autorisations
 * @param mixed  $rubriques  identifiant des rubriques sur lesquelles s'appliquent les champs
 * @param bool   $recursif   application recursive sur les sous rubriques ? ATTENTION, c'est gourmand en requetes SQL :)
 *
 * @return bool : autorise ou non .
 */
function _restreindre_extras_objet($objet, $id_objet, $opt, $rubriques, $recursif=false) {
	static $autorise = null;

	if ( $autorise === null )        { $autorise = array(); }
	if ( !isset($autorise[$objet]) ) { $autorise[$objet] = array(); }

	$cle = 'r' . implode('-', $rubriques);
	if (isset($autorise[$objet][$id_objet][$cle])) {
		return $autorise[$objet][$id_objet][$cle];
	}
	
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
		return $autorise[$objet][$id_objet][$cle] = false;
	}

    if (in_array($id_rubrique, $rubriques)) {
        return $autorise[$objet][$id_objet][$cle] = true;
    }

	// on teste si l'objet est dans une sous rubrique de celles mentionnee...
	// cela dit : ca cree de nombreuses requetes...
	if ($id_rubrique and $recursif) {
		// on teste si la rubrique $id_rubrique est en dessous de la rubrique $id_rubrique_ok
		// dans la hierarchie, en remontant cette hierarchie jusqu'a
		// trouver $id_rubrique_ok, ou a arriver au sommet
		$id_parent = $id_rubrique;
		while ($id_parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=" . sql_quote($id_parent))) {
			if (in_array($id_parent, $rubriques)) {
				return $autorise[$objet][$id_objet][$cle] = true;
			}
		}
	}
		  
    return $autorise[$objet][$id_objet][$cle] = false;
}

?>
