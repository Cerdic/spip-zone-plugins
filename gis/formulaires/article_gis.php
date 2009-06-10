<?php


include_spip('inc/session');
include_spip('inc/securiser_action');
include_spip('public/assembler');

function formulaires_article_gis_charger_dist(){

	$valeurs = array(
		'titre'=>'',
		'texte'=>'',
		'lat'=>'',
		'lonx'=>'',
		'editable'=>true
	);

	// si on a pas de rubrique, on n'edite pas le formulaire !
	if (!$id_rubrique = lire_config('gis/rubrique_cible')){
		$valeurs['editable']=false;
	}
	
	// si l'on vien juste de poster le formlaire et qu'il a ete valide
	// on veut pouvoir recommencer a poster un nouvelle photo
	// on ne prend du coup pas les anciennes valeurs dans l'environnement
	// pour ne pas polluer le nouveau formulaire
	if (_request('soumission_gis_enregistre')){
		unset ($valeurs['titre']);
		unset ($valeurs['texte']);
		unset ($valeurs['lat']);
		unset ($valeurs['lonx']);
	}
	return $valeurs;
}


function formulaires_article_gis_verifier_dist(){

	$erreurs = array();
	$id_article = _request('id_article');
	
	if ($id_article AND !_request('document'))
		return $erreurs;
	
	if (!$titre = _request('titre')){
		$erreurs['titre'] = _T('gis:erreur_titre');
	}
	
	if (!$texte = _request('texte')){
		$erreurs['texte'] = _T('gis:erreur_texte');
	}
	
	if (!count($erreurs)){
		if ($afficher_texte != 'non') {
			// ajout de l'article et de ses coordonnées
			if(!$id_article){
				include_spip('base/abstract_sql');
				include_spip('inc/texte');
				// pour le traitement :
				// 1) on demande un nouvel article
				// 2) on lui donne un titre et un statut et on y colle le texte
				//3) on insère les coordonnées de l'article

				// 1
				include_spip('action/editer_article');
				$id_rubrique = lire_config('gis/rubrique_cible');
				if (!$id_article = insert_article($id_rubrique)){
					return array(1,_T('gis:erreur_ajout_article'));
				}

				// 2
				$titre = _request('titre');
				$statut = lire_config('gis/statut','prop');
				$texte = sql_quote(_request('texte'));
				$c = array(
					'titre'=> $titre,
					'statut'=> $statut
				);
				include_spip('inc/modifier');
				revision_article($id_article, $c);
				instituer_article($id_article, $c);
				sql_update('spip_articles', array('texte' => $texte), 'id_article=' . sql_quote($id_article));
				
				
				//3
				$lat = _request('lat');
				$lonx = _request('lonx');
				sql_insertq("spip_gis",  array("id_article" => $id_article , "lat" => $lat, "lonx" => $lonx));
			}
			// ajout des documents
			if(_request('document')) {
				// compatibilite php < 4.1
				if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
					
				// recuperation des variables
				$fichier = $_FILES['doc']['name'];
				$size = $_FILES['doc']['size'];
				$tmp = $_FILES['doc']['tmp_name'];
				$type = $_FILES['doc']['type'];
				$error = $_FILES['doc']['error'];
				$doc = &$_FILES['doc'];
				
				// verification si upload OK
				if( !is_uploaded_file($tmp) ) {
					$erreurs['document'] = _T('gis:erreur_upload');
				}
				else {
					// on récupère l'extension du document envoyé
					include_spip('base/abstract_sql');
					include_spip('inc/ajouter_documents');
					list($extension,$fichier) = fixer_extension_document($doc);
					$acceptes = array_map('trim', explode(',',lire_config('gis/formats_documents','jpg,png,gif')));
					// on vérifie que l'extension du document est autorisée
					if (!in_array($extension, $acceptes)) {
						if (!$formats = join(', ',$acceptes))
							$formats = _L('aucun');
						$erreurs['document'] = _T('gis:erreur_formats_acceptes',array('formats' => $formats));
					}
					else {
					// ajout du document
						$ajouter_documents = charger_fonction('ajouter_documents','inc');
						if(!$ajouter_documents($tmp, $fichier, "article", $id_article, 'document', $id_document=0, $docs_actifs=array()))
							$erreurs['document'] = _T('gis:erreur_copie_impossible');
					}
				}
				// supprimer des documents ?
				if (is_array(_request('supprimer')))
				foreach (_request('supprimer') as $supprimer) {
					if ($supprimer = intval($supprimer)) {
						include_spip('inc/autoriser');
						$supprimer_document = charger_fonction('supprimer_document','action');
						$supprimer_document($supprimer);
						$erreurs['document'] = _T('gis:document_supprime');
					}
				}
			}
			$form_doc = inclure_formulaire_doc_gis($id_article);
			$erreurs['form_doc'] = $form_doc;
		}
	}
	
	return $erreurs;
}


function formulaires_article_gis_traiter_dist(){
	
	// signaler que l'on vient de soumettre le formulaire
	// pour que charger ne remette pas les anciennes valeurs
	// puisqu'on propose sa reedition dans la foulee

	set_request('soumission_gis_enregistre',true);

	return array('editable'=>true,'message_ok'=>_T('gis:ok_formulaire_soumis'));

}

function inclure_formulaire_doc_gis($id_article)
{
	$contexte = array();

	$contexte = array_merge(
		$contexte,
		array(
			'id_article' => $id_article
			)
		);
	return inclure_balise_dynamique(array('formulaires/documents_gis',0,$contexte),false);
}

?>