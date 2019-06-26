<?php
/**
   Fichier #FORMULAIRE_UPLOAD_IMAGE
  * formulaire d'upload d'images.
  * Largement pompe de Skeleditor
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL
*/

// securite
if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

/**
 * Chargement du formulaire
 * @param string $path_base
 * @param string $bloc
 * @param string $fichier
 * @return array
 */
function formulaires_upload_image_charger_dist($path_base,$bloc){
	$valeurs = array(
		'file'=>'',
		'path_base'=>$path_base,
        'bloc' =>$bloc,
		'editable'=>true
	);

	return $valeurs;
}

function formulaires_upload_image_verifier_dist($path_base,$bloc) {
    if (!is_dir($path_base)) {
        if (!mkdir(_DIR_SITE."squelettes/images/", 0755, true)) {
            $erreurs['file'] = _T('sdc:erreur_creer_dir_images', array('dir'=>_DIR_SITE));
        }
    }
    $files = sdc_check_upload($path_base);
    foreach ($files as $file) {
        if ( !preg_match("#.(png|jpg|jpeg|gif)$#i",$file['name'])) {
            $erreurs["file"] = _T('sdc:erreur_format');
        }
    }
    
	return $erreurs;
}

function formulaires_upload_image_traiter_dist($path_base,$bloc){
	if (!_request('img_delete')) {
		$files = sdc_check_upload($path_base);
		$ok = true;
		foreach ($files as $file) {
            $fileName = sdc_suppr_accents($file['name']);
			if (!move_uploaded_file($file['tmp_name'], $path_base.$fileName)) {
                $ok = false;
            }
		}
		if ($ok){
			$res['message_ok'] = "L'image à bien été téléversée";
		}
		else {
            $res['message_erreur'] = 'Erreur de téleversement';
        }
	}
	else {
		// supprimer des images 
		if (is_array(_request('todelete'))) {
			$compteur=0;
			$img_deleted="";	
			foreach (_request('todelete') as $delete) {
				unlink(_DIR_SITE."squelettes/images/".$delete);
				$img_deleted.= $compteur==0 ? $delete : ", ".$delete;
				$compteur++;	
			}
			$res['message_ok'] .= _T("sdc:msg_image_supprimee",array("compteur"=>$compteur))." : ".$img_deleted;
		}
	}
    
	return $res;
}

function sdc_check_upload($path_base){
	$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
	$files = array();
	$erreurs = array();

	if (is_array($post)) {
		foreach ($post as $file) {
			//UPLOAD_ERR_NO_FILE
			if (!($file['error'] == 4)) {
				$files[]=$file;
			}
		}
	}
	return $files;
}

/**
 * Supprimer les accents
 * 
 * @param string $str chaîne de caractères avec caractères accentués
 * @param string $encoding encodage du texte (exemple : utf-8, ISO-8859-1 ...)
 */
function sdc_suppr_accents($str, $encoding='utf-8')
{
    // transformer les caractères accentués en entités HTML
    $str = htmlentities($str, ENT_NOQUOTES, $encoding);
 
    // remplacer les entités HTML pour avoir juste le premier caractères non accentués
    // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "à" => "a" ...
    $str = preg_replace( '#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str );
 
    // Remplacer les ligatures tel que : œ,Æ ...
    // Exemple "œ" => "oe"
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    // Remplacer tout le reste par _
    $str = preg_replace('#&[^;]+;#', '_', $str);
    $str = preg_replace('# #', '_', $str);

 
    return $str;
}
