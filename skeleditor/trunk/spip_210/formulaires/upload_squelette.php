<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

include_spip('inc/skeleditor');

/**
 * Chargement du formulaire
 * 4 cas possibles :
 *  - un nouveau fichier ($fichier='')
 *  - un fichier a l'extension interdite
 *  - un fichier de squelettes/, modifiable
 *  - un fichier existant (d'un plugin par exe),
 *    qui doit etre copie dans squelettes/ a l'enregistrement
 *
 * @param string $path_base
 * @param string $fichier
 * @return array
 */
function formulaires_upload_squelette_charger_dist($path_base){

	$valeurs = array(
		'file'=>'',
		'path_base'=>$path_base,
		'editable'=>true
	);

	return $valeurs;
}

function formulaires_upload_squelette_verifier_dist($path_base){
	$res = skeleditor_check_upload($path_base);
	if (is_string($res))
		$erreurs['file'] = $res;
	elseif(!count($res)){
		$erreurs['file'] = _T('skeleditor:erreur_fichier_inexistant');
	}
	return $erreurs;
}



function formulaires_upload_squelette_traiter_dist($path_base){
	$files = skeleditor_check_upload($path_base);
	$ok = true;
	foreach($files as $file){
		if (!move_uploaded_file($file['tmp_name'], $path_base.$file['name']))
			$ok = false;
	}
	if ($ok){
		$res['message_ok'] = 'ok';
		$res['redirect'] = parametre_url(parametre_url(self(),'upload',''),'f',$path_base.$file['name']);
	}
	else
		$res['message_erreur'] = 'erreur';

	return $res;
}



function skeleditor_check_upload($path_base){
	$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
	$files = array();
	$erreurs = array();

	if (is_array($post)){
		foreach ($post as $file) {
			//UPLOAD_ERR_NO_FILE
			if (!($file['error'] == 4)){
				if (is_string($err = skeleditor_upload_error($file['error'])))
					return $err; // un erreur upload
				if ($e = skeleditor_verifie_nouveau_nom($path_base,$file['name'],true))
					return $e;
				$files[]=$file;
			}
		}
	}
	return $files;
}

// Erreurs d'upload
// renvoie false si pas d'erreur
// et true si erreur = pas de fichier
// pour les autres erreurs renvoie le message d'erreur
function skeleditor_upload_error($error) {

	if (!$error) return false;
	spip_log("Erreur upload $error -- cf. http://php.net/manual/fr/features.file-upload.errors.php");
	switch ($error) {

		case 4: /* UPLOAD_ERR_NO_FILE */
			return true;

		# on peut affiner les differents messages d'erreur
		case 1: /* UPLOAD_ERR_INI_SIZE */
			$msg = _T('upload_limit',
			array('max' => ini_get('upload_max_filesize')));
			break;
		case 2: /* UPLOAD_ERR_FORM_SIZE */
			$msg = _T('upload_limit',
			array('max' => ini_get('upload_max_filesize')));
			break;
		case 3: /* UPLOAD_ERR_PARTIAL  */
			$msg = _T('upload_limit',
			array('max' => ini_get('upload_max_filesize')));
			break;

		default: /* autre */
			if (!$msg)
			$msg = _T('pass_erreur').' '. $error
			. '<br />' . propre("[->http://php.net/manual/fr/features.file-upload.errors.php]");
			break;
	}

	spip_log ("erreur upload $error");
	return $msg;

}


?>