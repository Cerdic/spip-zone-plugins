<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/formifusion');
include_spip('inc/config');

function formulaires_fusionner_formulaire_charger($id_formulaire){
	
	$contexte = array();
	
	// On va chercher les fusions existantes
	$types_echange = fusions_formulaire_lister_disponibles();
	$types_fusion = array();
	foreach ($types_echange['fusionner'] as $type=>$fonction){
		$types_fusion[$type] = _T("formifusion:fusionner_formulaire_${type}_importer");
	}
	
	$contexte['_types_fusion'] = $types_fusion;
	
	return $contexte;
}

function formulaires_fusionner_formulaire_verifier($id_formulaire){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_fusionner_formulaire_traiter($id_formulaire){
	$retours = array();
	
	if (!$_FILES['fichier']['error']){
		$type_import = _request('type_import');
		$fichier = $_FILES['fichier']['tmp_name'];
	
		$fusionner = charger_fonction('fusionner', "fusionner/formulaire/$type_import", true);

		try {
			$erreur_ou_id = $fusionner($fichier,$id_formulaire);
		}
		catch (Exception $e){
			$erreur_ou_id = $e->getMessage();
		}

		if (!is_numeric($erreur_ou_id)){
			$retours['message_erreur'] = $erreur_ou_id;
			$retours['editable'] = true;
		}
		else{
			$id_formulaire = intval($erreur_ou_id);
			// Tout a fonctionné. En fonction de la config, on attribue l'auteur courant
			$auteurs = lire_config('formidable/analyse/auteur');
			if ($auteurs == 'on') {
				if ($id_auteur = session_get('id_auteur')) {
					// association (par défaut) du formulaire et de l'auteur courant
					objet_associer(array('formulaire'=>$id_formulaire), array('auteur'=>$id_auteur));
				}
			}
			$retours['redirect'] = generer_url_ecrire('formulaire_edit', "id_formulaire=$id_formulaire&configurer=champs" );
		}
	}
	
	return $retours;
}

?>
