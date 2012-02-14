<?php
/**
 * Plugin Contacts & Organisations 
 * Licence GPL (c) 2010-2011 Matthieu Marcillaud, Cyril Marion
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_creer_auteur_lie_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// on attend le type ($arg[0]) et l'id ($arg[1])
	$arg = explode('/', $arg);

	if ($arg[0] and is_numeric($arg[1])) {

		switch($arg[0]) {

			case 'contact':
				$contact = sql_fetsel("nom, prenom", "spip_contacts", "id_contact=$arg[1]"); 
				$nom = trim($contact['prenom'] . " " . $contact['nom']);
				$id_auteur = sql_insertq("spip_auteurs", array(
						"nom"    =>  $nom,
						"statut" => "1comite"
				));
				include_spip('action/editer_contact');
				contact_modifier($arg[1], array("id_auteur" => $id_auteur));
				break;

			case 'organisation': 
				
				// Code pour le cas present ou le id_auteur est dans la table organisations...
				$organisation = sql_getfetsel("nom", "spip_organisations", "id_organisation=$arg[1]"); 
				$nom = trim($organisation);
				$id_auteur = sql_insertq("spip_auteurs", array(
						"nom"    =>  $nom,
						"statut" => "1comite"
				));
				include_spip('action/editer_organisation');
				organisation_modifier($arg[1], array("id_auteur" => $id_auteur));
				break;
		}
	} else {
		spip_log("erreur creation auteur lie a l objet ".$arg[0],"contacts");
	}
}

?>
