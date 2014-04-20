<?php	

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_import_docker_charger(){
	$contexte['id_rubrique'] = _request('id_rubrique');
	$contexte['nblimite'] = _request('nblimite');
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
	// La limite du nombre à traiter
	$nblimite = _request('nblimite');

	spip_log("importer les documents de id_rubrique= $id_rubrique","docker");
	
	//On récupère la liste des documents de la rubrique sinon tous	
	//todo étendre aux articles de cette rubrique
	$id=$id_rubrique;
	$type="rubrique";

	if($id_rubrique>0)
	$res = sql_select("D.id_document,D.fichier,D.extension", "spip_documents AS D LEFT JOIN spip_documents_liens AS T ON T.id_document=D.id_document", "distant='oui' AND T.id_objet=" . intval($id) . " AND T.objet=" . sql_quote($type)." LIMIT 0 , $nblimite");
	else
	$res = sql_select("D.id_document,D.fichier,D.extension", "spip_documents AS D LEFT JOIN spip_documents_liens AS T ON T.id_document=D.id_document", "distant='oui' LIMIT 0 , $nblimite");


	include_spip('inc/distant');

	$copier_fichier= charger_fonction('copier_local','action');
	while ($row = sql_fetch($res)){
		spip_log("document renommé id_document=".$row['id_document'],"docker");
		//On traite les documents en les important
		$copier_fichier($row['id_document']);
	}
	
	return $retour;	
}


?>
