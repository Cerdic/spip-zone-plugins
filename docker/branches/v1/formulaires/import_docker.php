<?php	

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_import_docker_charger(){
	$contexte['id_rubrique'] = _request('id_rubrique');
	return $contexte;
}

function formulaires_import_docker_verifier(){
	$erreurs = array();
	
	/*if (_request('id_rubrique')) {
		if (!is_numeric(_request('id_rubrique'))){
			$erreurs['id_rubrique']=_T('docker:valeur_incorrecte');
		}
	}*/
	return $erreurs;
}

function formulaires_import_docker_traiter(){
	
	// On commence par chercher la rubrique a traiter 
	$id_rubrique = _request('id_rubrique');

	spip_log("documents de id_rubrique= $id_rubrique","docker");
	
	//On récupère la liste des documents de la rubrique sinon tous	
	//todo étendre aux articles de cette rubrique
	$id=$id_rubrique;
	$type="rubrique";
	
	if($id_rubrique>0)
	$res = sql_select("D.id_document", "spip_documents AS D LEFT JOIN spip_documents_liens AS T ON T.id_document=D.id_document", "distant='oui' AND T.id_objet=" . intval($id) . " AND T.objet=" . sql_quote($type));
	else
	$res = sql_select("D.id_document", "spip_documents AS D LEFT JOIN spip_documents_liens AS T ON T.id_document=D.id_document", "distant='oui'");

	$copier_local = charger_fonction('copier_local','action');
	while ($row = sql_fetch($res)){
		spip_log("document=".$row['id_document'],"docker");
		//On traite les documents en les important
		$copier_local($row['id_document']);
	}
	
	return $retour;	
}

	/*
	$ajouter_un_document = charger_fonction('ajouter_un_document','action');
	$file = array('tmp_name' => $fichier_tmp, 'name' => basename($fichier_tmp));
	$id_document = $ajouter_un_document('new', $file, 'patate', $id_patate, 'auto');
	*/

?>
