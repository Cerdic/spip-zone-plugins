<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2016
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function simplecal_autoriser(){} 


// bouton du bandeau
function autoriser_evenements_menu_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}
function autoriser_evenementcreer_menu_dist($faire, $type, $id, $qui, $opt){
	$whos = simplecal_profils_autorises_a_creer();
	return in_array($qui['statut'], $whos);
}


function autoriser_simplecal_lister($faire, $type, $id, $qui, $opt){
	return in_array($qui['statut'], array('0minirezo', '1comite'));
}

function autoriser_simplecal_demo($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo'));
}


// Remarque : Cette fonction est aussi appelee au niveau du core (API des listings - cf. inc/afficher_objets.php)
// Le plugin "Acces restreint 3" la declare egalement mais ne l'utilise pas (surement pour le plugin Agenda 2).
// Ce qui pose probleme car base sur une table evenement differente de celle du plugin "simple-calendrier" !
// il est donc normal que le plugin "simple-calendrier" la declare pour son usage propre.
// => celle du plugin "Acces restreint" ne sera donc pas utilisee. 
// => cela tombe bien puisqu'il ne s'en sert pas lui-meme 
//    et que les plugins "simple-calendrier" et "agenda 2" sont naturellement incompatibles)
function autoriser_evenement_voir($faire, $type, $id, $qui, $opt) {
	if (!defined('_DIR_PLUGIN_ACCESRESTREINT')){
		return in_array($qui['statut'], array('0minirezo', '1comite'));
	} 
	// ------------------------------------------
	// si le plugin Acces restreint est actif 
	// ------------------------------------------
	else {
		include_spip('public/quete');
		include_spip('inc/accesrestreint');
		
		$publique = isset($options['publique']) ? $options['publique'] : !test_espace_prive();
		$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
		
		// Si l'évènement fait partie des contenus restreints directement, c'est niet
		if (in_array($id, accesrestreint_liste_objets_exclus('evenements', $publique, $id_auteur))) {
			return false;
		}
		
		if (!$id_rubrique = $options['id_rubrique']){
			$evenement = quete_parent_lang('spip_evenements', $id);
			$id_rubrique = $evenement['id_rubrique'];
		}
		
		return autoriser_rubrique_voir('voir', 'rubrique', $id_rubrique, $qui, $options);
	}
}



function simplecal_profils_autorises_a_creer(){
	if ($GLOBALS['meta']['simplecal_autorisation_redac'] == 'oui'){
		$whos = array('0minirezo', '1comite');
	} else {
		$whos = array('0minirezo');
	}
	return $whos;
}

// Proprietaire de l'evenement ?
function simplecal_auteur_evenement($id, $id_auteur){
	$b = false;
	$nb = sql_countsel('spip_auteurs_liens as lien', "lien.objet='evenement' and lien.id_objet=".$id." and lien.id_auteur = ".$id_auteur);
	if ($nb>0){
		$b = true;
	}
	return $b;
}

// Creer un evenement
function autoriser_evenement_creer($faire, $type, $id, $qui, $opt) {
	$whos = simplecal_profils_autorises_a_creer();
	return in_array($qui['statut'], $whos);
}

// Modifier l'evenement $id
// Redacteur : Comme pour les articles : on ne peut plus le modifier une fois publie
function autoriser_evenement_modifier($faire, $type, $id, $qui, $opt) {
	$autorise = false;

	// Administrateur ?
	if ($qui['statut'] == '0minirezo'){
		$autorise = true;
	} else {
		// Redacteur ? (+ si config l'autorise)
		if ($qui['statut'] == '1comite' && $GLOBALS['meta']['simplecal_autorisation_redac'] == 'oui'){
			
			// Si l'autorisation n'est pas fonction d'un statut, ou que ce statut n'est pas 'publie'
			if (!isset($opt['statut']) OR $opt['statut']!=='publie') {
				// Le statut de l'objet n'est pas publie
				$row = sql_fetsel("statut", "spip_evenements", "id_evenement=$id");
				if (in_array($row['statut'], array('prop','prepa', 'poubelle'))){
					// Auteur = proprietaire de l'objet.
					if (simplecal_auteur_evenement($id, $qui['id_auteur'])){
						$autorise = true;
					}
				}
			} else {
				// l'autorisation est fonction d'un statut (cf. autoriser:instituer_objet).
				// ET ce statut est publie
				// => False
			}
		}
	}
	return $autorise;
}

/**
 * Autorisation de créer un événement dans une rubrique
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_rubrique_creerevenementdans_dist($faire, $type, $id, $qui, $opt) {
	return ($id and autoriser('voir', 'rubrique', $id) and autoriser('creer', 'evenement', $id));
}

/* Compatibilité avec le plugin LIM : restriction par rubrique */
if (!function_exists('autoriser_rubrique_creerevenementdans') AND test_plugin_actif('lim')) {
	function autoriser_rubrique_creerevenementdans($faire, $type, $id, $qui, $opt) {
		$quelles_rubriques = lire_config('lim_rubriques/evenement');
		is_null($quelles_rubriques) ? $lim_rub = true : $lim_rub = !in_array($id,$quelles_rubriques);
		
		return
			$lim_rub
			AND autoriser_rubrique_creerevenementdans_dist($faire, $type, $id, $qui, $opt);
	}
}

?>