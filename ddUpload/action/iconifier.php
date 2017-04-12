<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://code.spip.net/@action_iconifier_dist
function action_iconifier_dist()
{
	include_spip('inc/actions');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$iframe_redirect = _request('iframe_redirect');

	$arg = rawurldecode($arg);

	if (!preg_match(',^-?\d*(\D)(.*)$,',$arg, $r))
		spip_log("action iconifier: $arg pas compris");
	elseif ($r[1] == '+')
		action_spip_image_ajouter_dist($r[2], _request('sousaction2'), _request('source'));
	else	action_spip_image_effacer_dist($r[2]);
	
	if(_request("iframe") == 'iframe') {
		$redirect = urldecode($iframe_redirect)."&iframe=iframe&var_noajax=1";
		redirige_par_entete(urldecode($redirect));
	}
}

// http://code.spip.net/@action_spip_image_effacer_dist
function action_spip_image_effacer_dist($arg) {

	if (!strstr($arg, ".."))
		spip_unlink(_DIR_LOGOS . $arg);
}

//
// Ajouter un logo
//

// $source = $_FILES[0]
// $dest = arton12.xxx
// http://code.spip.net/@action_spip_image_ajouter_dist
function action_spip_image_ajouter_dist($arg,$sousaction2,$source) {
	global $formats_logos;

	include_spip('inc/getdocument');
	if (!$sousaction2) {
    if (isset($_SERVER['HTTP_X_FILE_NAME']) && isset($_SERVER['CONTENT_LENGTH'])) {  
      if($_SERVER['CONTENT_LENGTH']>0) {
        $handle = fopen("php://input","rb");
        $block = fread($handle,4096);
        $blocklen = strlen($block);
        $base64 = false;
        $dest = tempnam(_DIR_TMP, 'tmp_upload');
        $handle_dest = fopen($dest,"ab"); 
        if (preg_match("/^data:[^;]+(;charset=\"[^\"]+\")?(;base64)?,/",$block,$m)) {
          //data uri
          $base64 = $m[2];
          $block = substr($block,strlen($m[0]));
          if($base64) {
            $blocklen -= $m[0];
            $blockleft = $blocklen % 4;
            if($blockleft) {
              $block .= fread($handle,$blockleft);
            }
            $block = base64_decode($block);
          }  
        }
        fwrite($handle_dest,$block);
        while($block = fread($handle,4096)) {
          if($base64)
            $block = base64_decode($block);
          fwrite($handle_dest,$block);
        }
        fclose($handle);
        fclose($handle_dest);
        $source = array("name" => $_SERVER['HTTP_X_FILE_NAME'], "tmp_name" => $dest, "error" => 0);
      } else {
        spip_log("file upload error");
        $source = array("error" => 4);    
      }
    } else {		
      if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
      $source = (is_array($_FILES) ? array_pop($_FILES) : "");
  	}
  	
	}
	if ($source) {
		$f =_DIR_LOGOS . $arg . '.tmp';

		if (!is_array($source)) 
		// fichier dans upload/
	  		$source = @copy(determine_upload() . $source, $f);
		else {
		// Intercepter une erreur a l'envoi
			if (check_upload_error($source['error']))
				$source ="";
			else
		// analyse le type de l'image (on ne fait pas confiance au nom de
		// fichier envoye par le browser : pour les Macs c'est plus sur)

				$source = deplacer_fichier_upload($source['tmp_name'], $f);
		}
	}
	if (!$source)
		spip_log("pb de copie pour $f");
	else {
		$size = @getimagesize($f);
		$type = !$size ? '': ($size[2] > 3 ? '' : $formats_logos[$size[2]-1]);
		if ($type) {
			$poids = filesize($f);

			if (_LOGO_MAX_SIZE > 0
			AND $poids > _LOGO_MAX_SIZE*1024) {
				spip_unlink ($f);
				check_upload_error(6,
				_T('info_logo_max_poids',
					array('maxi' => taille_en_octets(_LOGO_MAX_SIZE*1024),
					'actuel' => taille_en_octets($poids))));
			}

			if (_LOGO_MAX_WIDTH * _LOGO_MAX_HEIGHT
			AND ($size[0] > _LOGO_MAX_WIDTH
			OR $size[1] > _LOGO_MAX_HEIGHT)) {
				spip_unlink ($f);
				check_upload_error(6, 
				_T('info_logo_max_taille',
					array(
					'maxi' =>
						_T('info_largeur_vignette',
							array('largeur_vignette' => _LOGO_MAX_WIDTH,
							'hauteur_vignette' => _LOGO_MAX_HEIGHT)),
					'actuel' =>
						_T('info_largeur_vignette',
							array('largeur_vignette' => $size[0],
							'hauteur_vignette' => $size[1]))
				)));
			}
			@rename ($f, _DIR_LOGOS . $arg . ".$type");
		}
		else {
			spip_unlink ($f);
			check_upload_error(6,_T('info_logo_format_interdit',
						array('formats' => join(', ', $formats_logos))));
		}
	
	}

  if($dest)
    @unlink(@dest);	
}
?>
