<?php	

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_import_docker_charger(){
	$contexte['id_objet'] = _request('id_objet');
	$contexte['objet'] = _request('objet');
	$contexte['nblimite'] = _request('nblimite');
	return $contexte;
}

function formulaires_import_docker_verifier(){
	$erreurs = array();
	
	/*if (_request('id_objet')) {
		if (!is_numeric(_request('id_objet'))){
			$erreurs['id_objet']=_T('docker:valeur_incorrecte');
		}
	}*/
	return $erreurs;
}



function formulaires_import_docker_traiter(){
	
	$id_objet = _request('id_objet');
	$objet = _request('objet');
	
	// La limite du nombre à traiter
	$nblimite = _request('nblimite');

	spip_log("importation des documents de objet=$objet id_objet= $id_objet","docker");
	
	//On récupère la liste des documents de l'objet (article rubrique ou autre) sinon tous
	//todo tous les documents d'une branche rubrique ou secteur	

	
	//On exclue certaines extensions via base spip, sans vérifier la réalité
	include_spip('inc/config');	
	$config = lire_config('docker');
	$extensions_exclues=$config['extensions'];

	if($id_objet>0)
		$res = sql_select("D.id_document,D.fichier,D.extension", "spip_documents AS D LEFT JOIN spip_documents_liens AS T ON T.id_document=D.id_document", "distant='oui' AND FIND_IN_SET(D.extension,REPLACE('$extensions_exclues',' ','')) = 0 AND T.id_objet=" . intval($id_objet) . " AND T.objet=" . sql_quote($objet)." LIMIT 0 , $nblimite");
		else
		$res = sql_select("D.id_document,D.fichier,D.extension", "spip_documents AS D LEFT JOIN spip_documents_liens AS T ON T.id_document=D.id_document", "distant='oui' AND FIND_IN_SET(D.extension,'$extensions_exclues') = 0 LIMIT 0 , $nblimite");

	
	include_spip('inc/distant');

	//fonction de plugins-dist medias
	$copier_fichier= charger_fonction('copier_local','action');
	while ($row = sql_fetch($res)){
		spip_log("document à renommer id_document=".$row['id_document']." avec ".$row['extension'],"docker_extension");
		//On traite les documents en les important
		$copier_fichier($row['id_document']);
		//On ajoute le titre après
		docker_titrer($row['id_document']);
	}
	
	return;	
}

/**
 * Traitements après l'édition d'un document
 * reprend un document distant importé
 *
**/
function docker_titrer($id_document){
	// options à définir depuis ecrire/?exec=configurer_docker
	$titrer = _TITRER_DOCUMENTS;
	if(!isset($titrer)) return;

	//on reprend le champ credit pour en extraire le titre
	$row = sql_fetsel('titre,credits,fichier','spip_documents','id_document='.sql_quote($id_document));

		if($row['titre']==''){
			$source=$row['credits'];
			$path_parts = pathinfo($source);
			$extension = $path_parts ? $path_parts['extension'] : '';
			if (isset($row['credits'])) $nom_envoye = basename($row['credits']);
			// retourne le nom du fichier
			$nom_envoye = preg_replace('#(?:.*)[^:]/(.*)#Umis','$1',$source);
			$fichier = "$extension/$nom_envoye";
			
			$insert['titre'] = '';
			if ($titrer){
				$titre = substr($nom_envoye,0, strrpos($nom_envoye, ".")); // Enlever l'extension du nom du fichier
				$titre = preg_replace(',%20+,u', ' ', $titre); // Enlever les espaces html du nom du fichier
				$titre = preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre);
				$insert['titre'] = preg_replace(',\.([^.]+)$,', '', $titre);
			}
			spip_log("credits=".$row['credits']."pour id_document=$id_document et nom_envoye=$nom_envoye","titrer_document");
			include_spip('inc/modifier');
			document_modifier($id_document,$insert);
		}
		
}


?>
