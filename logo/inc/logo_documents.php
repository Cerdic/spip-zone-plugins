<?php

if (!function_exists('check_upload_error')) {
    // Erreurs d'upload
    // renvoie false si pas d'erreur
    // et true si erreur = pas de fichier
    // pour les autres erreurs affiche le message d'erreur et meurt
    // http://doc.spip.org/@check_upload_error
    function check_upload_error($error, $msg='') {
	    global $spip_lang_right;

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

      	if(_request("iframe")=="iframe") {
	      echo "<div class='upload_answer upload_error'>$msg</div>";
	      exit;
	    }

	    echo minipres($msg,
		          "<div style='text-align: $spip_lang_right'><a href='"  . rawurldecode($GLOBALS['redirect']) . "'><button type='button'>" . _T('ecrire:bouton_suivant') . "</button></a></div>");
	    exit;
    }
}

if (!function_exists('deplacer_fichier_upload')) {
    /**
     * Deplacer ou copier un fichier
     *
     * http://doc.spip.org/@deplacer_fichier_upload
     *
     * @param string $source
     * @param string $dest
     * @param bool $move
     * @return bool|mixed|string
     */
    function deplacer_fichier_upload($source, $dest, $move=false) {
	    // Securite
	    if (substr($dest,0,strlen(_DIR_RACINE))==_DIR_RACINE)
		    $dest = _DIR_RACINE.preg_replace(',\.\.+,', '.', substr($dest,strlen(_DIR_RACINE)));
	    else
		    $dest = preg_replace(',\.\.+,', '.', $dest);

	    if ($move)	$ok = @rename($source, $dest);
	    else				$ok = @copy($source, $dest);
	    if (!$ok) $ok = @move_uploaded_file($source, $dest);
	    if ($ok)
		    @chmod($dest, _SPIP_CHMOD & ~0111);
	    else {
		    $f = @fopen($dest,'w');
		    if ($f) {
			    fclose ($f);
		    } else {
			    include_spip('inc/flock');
			    raler_fichier($dest);
		    }
		    spip_unlink($dest);
	    }
	    return $ok ? $dest : false;
    }
}



?>
