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

	// Ajouter un document   
	if (($files = ($_FILES ? $_FILES : $HTTP_POST_FILES)) && (is_uploaded_file($files['fichier']['tmp_name']))) {
		 
    include_spip('action/ajouter_documents');
		$ajouter_un_document = charger_fonction('ajouter_un_document','action');
    
    $extention_autorisee = explode("|",lire_config("formulaireupload/files_accepted"));
    $nb_doc_autorise = intval(lire_config("formulaireupload/files_number"));
    
    $compteur = 0;  
    $res['message_ok'] = ""; 
    
    // FIXE A VERIFIER ne prend que le dernier fichier ...

    foreach ($files as $file) {           
           // verification cote serveur 
            // DEBUG $res['message_ok'] .= " -*- ";
           // ... si le fichier est autorisee (securite)
           if (count($extention_autorisee)>0) {
                   //  FIXME : il faudrait tester l'extension est bien conforme a la configuration (pour eviter les hacks)
                   //          analyser le mime type du _FILE et trouver la correspond ds spip document
           }
           
          
           // limite aux nombres de fichiers liés à l'objet ?            
           if ($nb_doc_autorise==0) {  
                // pas de limite            
                 $id = $ajouter_un_document("new", $file, $type, $id_objet, 'document');
                 $compteur++;
           }  else {
                // oui, on cherche les objets déjà liés
                $nb_objet = 0;
                if ($res_nb_objet = sql_select('id_document', 'spip_documents_liens', array("objet = '$type'",'id_objet='.intval($id_objet))))
                           $nb_objet = sql_count($res_nb_objet);
                if ($nb_objet<=$nb_doc_autorise)  {
                     $id = $ajouter_un_document("new", $file, $type, $id_objet, 'document');
                    $compteur++;
                }  else {
                    $res['message_ok'] =  _T("formupload:msg_doc_added_max",array("max"=>$nb_doc_autorise))."<br />";
                }
                      
           }
           
    } 
   
		$invalider = true;
		$res['message_ok'] .= _T("formupload:msg_doc_added",array("compteur"=>$compteur));
	}

	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'upload');
	}

	return $res;
}

?>
