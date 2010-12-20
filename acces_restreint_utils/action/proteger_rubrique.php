<?php
/**
 * Plugin acces_restreint_utils pour Spip 2.0
 * Des utilitaires pour faciliter l'utilisation du plugin Acces Restreint
 * Auteur : Cyril Marion
 * TODO : à intégrer au plugin de base dès que possible !
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_once(_DIR_PLUGIN_AR_UTILS."/ar_utils.php");

function action_proteger_rubrique_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// On récupère le id_rubrique à partir de l'argurment 2 de la balise #URL_ACTION_AUTEUR
	$id_rubrique = intval($arg);
	
	// Les auteurs à autoriser par défaut
	$les_auteurs = array(1,2,45);

	// Protection rubrique; on stocke ne N° de la zone dans $id_zone
	if (!$id_zone=proteger_rubrique($id_rubrique,$les_auteurs)) {
		return false;
	}

	// Retour vers l'url indiquée dasn l'argurment 3 de la balise #URL_ACTION_AUTEUR
	$redirect = parametre_url(urldecode(_request('redirect')),'id_rubrique', $id_rubrique, '&');
	include_spip('inc/headers');
	redirige_par_entete($redirect);

}

?>
