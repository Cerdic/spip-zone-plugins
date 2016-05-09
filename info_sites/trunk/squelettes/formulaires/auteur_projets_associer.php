<?php
function formulaires_auteur_projets_associer_charger_dist($id_auteur) {
	include_spip('base/abstract_sql');
	$auteurs_liens_projets = sql_allfetsel('id_objet,role', 'spip_auteurs_liens', "objet='projet' AND id_auteur=" . $id_auteur);
	$projets_anciens = array();
	if (is_array($auteurs_liens_projets) and count($auteurs_liens_projets) > 0) {
		foreach ($auteurs_liens_projets as $auteur) {
			$projets_anciens[$auteur['id_objet']][] = $auteur['role'];
		}
	}
	// Contexte du formulaire.
	$contexte = array(
		'id_auteur' => $id_auteur,
		'projets_anciens' => $projets_anciens,
	);

	return $contexte;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_auteur_projets_associer_verifier_dist($id_auteur) {
	$erreurs = array();

	return $erreurs;
}

function formulaires_auteur_projets_associer_traiter_dist($id_auteur) {
	//Traitement du formulaire.
	include_spip('base/abstract_sql');
	$projets_anciens = _request('projets_anciens');
	$projets = _request('projets');
	// On enlève les projets, pour les mettre à jour plus loin.
	// On est sur une table de lien, de ce fait, il n'y a pas de procédures simples de mise à jour des tables de liens.
	sql_delete('spip_auteurs_liens', "id_auteur=$id_auteur AND objet='projet'");

	if (is_array($projets) and count($projets) > 0) {
		foreach ($projets as $id_projet => $roles_auteur) {
			// On efface toutes les infos sur le projet de l'auteur
			sql_delete('spip_auteurs_liens', "id_auteur=$id_auteur AND objet='projet' AND id_objet=$id_projet");
			// Maintenant, on insère les nouvelles valeurs du projet dans la base avec leur rôle
			foreach ($roles_auteur as $role_auteur) {
				sql_insertq('spip_auteurs_liens', array(
					'id_auteur' => $id_auteur,
					'objet' => 'projet',
					'id_objet' => $id_projet,
					'role' => $role_auteur,
				));
			}
		}
	}

	// Donnée de retour.
	return array(
		'editable' => true,
		'message_ok' => '',
		'redirect' => generer_url_entite($id_auteur, 'auteur'),
	);
}
