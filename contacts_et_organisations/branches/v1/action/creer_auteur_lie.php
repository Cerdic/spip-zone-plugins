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
				$nom = ltrim($contact['prenom'] . " " . $contact['nom']);

				// créer l'auteur en suivant l'API pour que les pipelines s'activent
				include_spip('action/editer_auteur');
				$id_auteur = insert_auteur();
				autoriser_exception('modifier', 'auteur', $id_auteur);
				auteurs_set($id_auteur, array(
						"nom"    =>  $nom,
						"statut" => "1comite"
				));
				autoriser_exception('modifier', 'auteur', $id_auteur, false);

				sql_updateq("spip_contacts", 
						array("id_auteur" => $id_auteur),
						"id_contact =" . $arg[1]
					);
				//compatibilite
					$champs = array(
						'id_contact' => $arg[1],
						'objet' => 'auteur',
						'id_objet' => $id_auteur
					);
					sql_updateq("spip_contacts_liens", $champs);
	
				break;

			case 'organisation': 
				
				// Code pour le cas present ou le id_auteur est dans la table organisations...
				$organisation = sql_getfetsel("nom", "spip_organisations", "id_organisation=$arg[1]"); 
				$nom = ltrim($organisation);

				// créer l'auteur en suivant l'API pour que les pipelines s'activent
				include_spip('action/editer_auteur');
				$id_auteur = insert_auteur();
				autoriser_exception('modifier', 'auteur', $id_auteur);
				auteurs_set($id_auteur, array(
						"nom"    =>  $nom,
						"statut" => "1comite"
				));
				autoriser_exception('modifier', 'auteur', $id_auteur, false);

				sql_updateq('spip_organisations',array('id_auteur'=>$id_auteur),"id_organisation=".$arg[1]);
				
				//compatibilite
					$champs = array(
						'id_organisation' => $arg[1],
						'objet' => 'auteur',
						'id_objet' => $id_auteur
					);
					sql_insertq("spip_organisations_liens", $champs);
					
				break;

			default : break;
		}		
		
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_contact/$id_contact'");

	} else {
		
		spip_log("erreur creation auteur lie a l objet ".$arg[0],"contacts");

	}
}

?>
