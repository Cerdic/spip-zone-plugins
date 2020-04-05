<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter un favori
 *
 * @param Request $requete
 * @param Response $reponse
 * @return void
 */
function http_collectionjson_favoris_post_collection_dist($requete, $reponse){
	include_spip('inc/session');
	include_spip('inc/autoriser');
	$fonction_erreur = charger_fonction('erreur', 'http/collectionjson/');
	
	// Il faut être connecté pour avoir le droit et avoir un item avec les data "objet" et "id_objet"
	if (
		$id_auteur = session_get('id_auteur')
		and $id_auteur > 0
	) {
		if (
			$contenu = $requete->getContent()
			and $json = json_decode($contenu, true)
			and is_array($json)
			and isset($json['collection']['items'][0]['data'])
			and $data = $json['collection']['items'][0]['data']
			and is_array($data)
		) {
			// Pour chaque champ envoyé, on va faire un set_request() de SPIP
			foreach ($data as $champ) {
				// Seulement pour les 3 champs acceptés pour l'inscription
				if (
					isset($champ['name'])
					and isset($champ['value'])
				) {
					// Les arguments à envoyer à la fonction
					if (in_array($champ['name'], array('objet', 'id_objet', 'categorie'))) {
						${$champ['name']} = $champ['value'];
					}
					// Les choses à avoir dans le POST
					elseif (in_array($champ['name'], array('ajouter', 'retirer'))) {
						set_request($champ['name'], $champ['value']);
					}
				}
			}
			
			// On vérifie les erreurs (n'existe pas dans le formulaire par défaut)
			$erreurs = array();
			if (!$objet) {
				$erreurs['objet'] = _T('info_obligatoire');
			}
			if (!$id_objet) {
				$erreurs['id_objet'] = _T('info_obligatoire');
			}
			
			// On passe les erreurs dans le pipeline "verifier" (par exemple pour Saisies)
			$erreurs = pipeline('formulaire_verifier', array(
				'args' => array(
					'form' => 'favori',
					'args' => array($objet, $id_objet, $categorie),
				),
				'data' => $erreurs,
			));
			
			// S'il y a des erreurs, on va générer un JSON les listant
			if ($erreurs) {
				$reponse->setStatusCode(400);
				$reponse->headers->set('Content-Type', 'application/json');
				$reponse->setCharset('utf-8');
				
				$json_reponse = array(
					'collection' => array(
						'version' => '1.0',
						'href' => url_absolue(self('&')),
						'error' => array(
							'title' => _T('erreur'),
							'code' => 400,
						),
						'errors' => array(),
					),
				);
				
				foreach ($erreurs as $nom => $erreur) {
					$json_reponse['collection']['errors'][$nom] = array(
						'title' => $erreur,
						'code' => 400,
					);
				}
				$reponse->setContent(json_encode($json_reponse));
			}
			// Sinon on continue le traitement
			else {
				$favori_traiter = charger_fonction('traiter', 'formulaires/favori', true);
				$retours = $favori_traiter($objet, $id_objet, $categorie);
				
				// Si on a bien ajouté un nouvel auteur et qu'on le récupère
				if ($id_favori = intval($retours['id_favori']) and $id_favori > 0) {
					// On va cherche la fonction qui génère la vue d'une ressource
					if ($fonction_ressource = charger_fonction('get_ressource', 'http/collectionjson/', true)) {
						// On ajoute à la requête, l'identifiant de la nouvelle ressource
						$requete->attributes->set('ressource', $id_favori);
						$reponse = $fonction_ressource($requete, $reponse);
						
						// C'est une création, on renvoie 201
						$reponse->setStatusCode(201);
					}
				}
			}
		}
		// Sinon le format n'est pas reconnu
		else {
			$reponse = $fonction_erreur(415, $requete, $reponse);
		}
	}
	// Sinon erreur d'authentification
	else {
		$reponse = $fonction_erreur(401, $requete, $reponse);
	}
	
	return $reponse;
}
