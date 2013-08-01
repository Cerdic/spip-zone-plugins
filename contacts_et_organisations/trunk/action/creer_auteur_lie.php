<?php

/**
 * Gestion de l'action `creer_auteur_lie`
 * 
 * Crée et lie un auteur à un contact ou une organisation
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action qui, pour une organisation ou un contact, crée un auteur
 * et le lie à ce contact ou organisation.
 *
 * Le couple `type/id` (comme `organisation/8`) est donné en paramètre de
 * cette fonction ou en argument de l'action sécurisée et indique sur
 * qui est lié l'auteur créé.
 *
 * 
 * @param null|string
 *     Couple `type/id` où `type` est le type d'objet (organisation ou contact)
 *     et `id` son identifiant. En absence utilise l'argument de l'action sécurisée.
 * @return void
**/
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

				// créer l'auteur en suivant l'API pour que les pipelines s'activent
				include_spip('action/editer_objet');
				$id_auteur = objet_inserer('auteur');
				autoriser_exception('modifier', 'auteur', $id_auteur);
				objet_modifier('auteur', $id_auteur, array(
						"nom"    =>  $nom,
						"statut" => "1comite"
				));
				autoriser_exception('modifier', 'auteur', $id_auteur, false);

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
