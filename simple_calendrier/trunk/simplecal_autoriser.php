<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function simplecal_autoriser(){} 

/*
autoriser('creer', 'evenement', null);
=> � la recherche des fonctions suivantes 
1 - autoriser_$type_$faire
2 - autoriser_$type
3 - autoriser_$faire

$type  = 'evenement'
*/



// Bouton defini dans plugin.xml
function autoriser_bt_simplecal_accueil($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo', '1comite'));
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
		static $evenements_statut;
		$publique = isset($opt['publique'])?$opt['publique']:!test_espace_prive();
		$id_auteur = isset($qui['id_auteur']) ? $qui['id_auteur'] : $GLOBALS['visiteur_session']['id_auteur'];
		if (!isset($evenements_statut[$id_auteur][$publique][$id])){
			$id_rubrique = sql_getfetsel('id_rubrique','spip_evenements','id_evenement='.intval($id));
			$evenements_statut[$id_auteur][$publique][$id] = autoriser_rubrique_voir('voir', 'rubrique', $id_rubrique, $qui, $opt);
		}
		return $evenements_statut[$id_auteur][$publique][$id];
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

// Creer un evenement
function autoriser_evenement_creer($faire, $type, $id, $qui, $opt) {
	$whos = simplecal_profils_autorises_a_creer();
	return in_array($qui['statut'], $whos);
}

// Modifier l'evenement $id
function autoriser_evenement_modifier($faire, $type, $id, $qui, $opt) {
	$autorise = false;
	
	// Administrateur ?
	if ($qui['statut'] == '0minirezo'){
		$autorise = true;
	} else {
		// Redacteur ? (+ si config l'autorise)
		if ($qui['statut'] == '1comite' && $GLOBALS['meta']['simplecal_autorisation_redac'] == 'oui'){
			
			// si l'evenement n'est pas publie
			$row = sql_fetsel("statut", "spip_evenements", "id_evenement=$id");
			if ($row['statut'] != 'publie') {
				// Proprietaire ?
				$nb = sql_countsel('spip_auteurs_liens as lien', "lien.objet='evenement' and lien.id_objet=".$id." and lien.id_auteur = ".$qui['id_auteur']);
				if ($nb>0){
					$autorise = true;
				}
			}            
		}
	}
	return $autorise;
}


// Afficher uniquement les groupes de mots cles specifies dans evenement_voir.
// Sur le modele de ecrire/inc/autoriser.php (appele par ecrire/inc/editer_mots.php)
function autoriser_evenement_editermots_dist($faire,$quoi,$id,$qui,$opts){
	return autoriser_rubrique_editermots_dist($faire,'evenement',0,$qui,$opts);
}


// Le bloc "joindre un document" du core est protege par cette permission.
// cf. inc/documents.php : afficher_documents_colonne
// ET UTILISE UNIQUEMENT POUR LES REDACTEURS...
function autoriser_evenement_joindredocument($faire, $type, $id, $qui, $opt) {
	$whos = simplecal_profils_autorises_a_creer();
	return in_array($qui['statut'], $whos);
}

// Pour la suppression du LOGO : 
// Customisation de l'autorisation du core (autoriser_iconifier_dist)
// (sinon, crash lie au fait qu'il recherche la rubrique de l'objet...)
// autorisation renomme avec _evenement_ pour qu'il ne matche que sur ce type (autoriser_$type_$faire)
function autoriser_evenement_iconifier($faire,$quoi,$id,$qui,$opts){
	$droit = autoriser('modifier', 'evenement', $id);
	return $droit;
}

function autoriser_evenement_demo($faire, $type, $id, $qui, $opt) {
	return in_array($qui['statut'], array('0minirezo'));
}

?>
