<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_upload_charger_dist($objet, $id_objet, $fond_documents){
	// definition des valeurs de base du formulaire
	$valeurs = array(
		'objet'=>$objet,
		'id_objet'=>$id_objet,
		'fond_documents'=>$fond_documents,
		'editable'=>true
	);
	
	$id_type_objet = id_table_objet($objet);
	$valeurs[$id_type_objet] = $id_objet;
	
	if (!intval($GLOBALS['auteur_session']['id_auteur']))
		$valeurs['editable'] = false;

	include_spip('inc/autoriser');
	if (!autoriser('joindredocument', $objet, $id_objet))
		$valeurs['editable'] = false;

	return $valeurs;
}

function formulaires_upload_verifier_dist($objet, $id_objet, $fond_documents){
	$erreurs = array();

	return $erreurs;
}

function formulaires_upload_traiter_dist($objet, $id_objet, $fond_documents){
	$res = array('editable'=>' ', 'message_ok'=>'');
	
	$invalider = false;
	$type = objet_type($objet);
	$res['message_ok'] = _T("formupload:msg_nothing_to_do");

	// supprimer des documents ?
  $compteur=0;
	if (is_array(_request('supprimer'))) {
  	foreach (_request('supprimer') as $supprimer) {
  		if ($supprimer = intval($supprimer)) {
  			include_spip('inc/autoriser');
  			sql_delete('spip_documents_liens', 'id_document='.$supprimer);
  			$supprimer_document = charger_fonction('supprimer_document','action');
  			$supprimer_document($supprimer);
  			$invalider = true;
        $compteur++; 			
  			spip_log("supprimer document ($type)".$supprimer, 'upload');
  		}      
  	}
    $res['message_ok'] = _T("formupload:msg_doc_deleted",array("compteur"=>$compteur));
  }

	// Ajouter un document (cf plugins-dist/medias)
  include_spip('inc/joindre_document');
	$files = joindre_trouver_fichier_envoye();
   
  if (is_array($files)) {
    
    $ajouter_documents = charger_fonction('ajouter_documents', 'action');
    $nouveaux_doc = $ajouter_documents($id_document,$files,$objet,$id_objet,'document');
    
    $compteur = count($nouveaux_doc);
    
    /* A verifier:
       - securite : verifier les extentions (si forcing)
       - ajouter un quota image pour client ?
       

    $quota_client = intval(lire_config("formulaireupload/files_number"));
    

                // quota
                $nb_objet = 0;
                if ($res_nb_objet = sql_select('id_document', 'spip_documents_liens', array("objet = '$type'",'id_objet='.intval($id_objet))))
                           $nb_objet = sql_count($res_nb_objet);
                if ($nb_objet<=$quota_client )  
                  
                    $res['message_ok'] =  _T("formupload:msg_doc_added_max",array("max"=>$nb_doc_autorise))."<br />";
               
            ... a finir ...
           
    } 
       */
		$invalider = true;
		$res['message_ok'] = _T("formupload:msg_doc_added",array("compteur"=>$compteur));
	}

	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'upload');
	}

	return $res;
}

?>