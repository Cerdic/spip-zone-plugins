<?php
/**
 * Medias identifier
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2016 - Distribué sous licence GNU/GPL
 *
 * @package SPIP\Medias_identifier\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline renseigner_document (SPIP)
 *
 * Vérification du format d'une image si elle correspond à son extension
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline ($flux) modifié
 */
function medias_identifier_renseigner_document($flux) {
	if (in_array($flux['args']['extension'], array('jpg','png','gif'))) {
		include_spip('inc/documents');
		$format = false;
		$commande_test = _IDENTIFY_COMMAND.' -version';
		exec($commande_test, $retour_complet, $retour);
		if (intval($retour) == 0) {
			$format = trim(exec('identify -format %m '.$flux['args']['fichier']));
		} elseif (extension_loaded('imagick')) {
			$Imagick = new Imagick($flux['args']['fichier']);
			$format = $Imagick->getImageFormat();
		}
		$new_extension = false;
		if ($format) {
			switch ($format) {
				case 'PNG':
					$new_extension = 'png';
					break;
				case 'JPEG':
					$new_extension = 'jpg';
					break;
				case 'GIF':
					$new_extension = 'gif';
					break;
			}
			if ($new_extension && $new_extension != $flux['args']['extension']) {
				$new_fichier = basename(substr($flux['args']['fichier'], 0, strrpos($flux['args']['fichier'], '.'))).
					'.'.$new_extension;
				$dir = creer_repertoire_documents($new_extension);
				$ok_new_fichier = deplacer_fichier_upload($flux['args']['fichier'], $dir.$new_fichier, true);
				if ($ok_new_fichier) {
					$flux['data']['fichier'] = $new_extension.'/'.$new_fichier;
				}
				$flux['data']['extension'] = $new_extension;
				$flux['data']['type_image'] = $new_extension;
			}
		}
	}
	return $flux;
}


/**
 * Insertion dans le pipeline renseigner_document_distan (SPIP)
 *
 * Vérification du format d'une image distante si elle correspond à son extension
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline ($flux) modifié
 */
function medias_identifier_renseigner_document_distant($flux) {
	$a = recuperer_infos_distantes($flux['source']);
	if (isset($a['extension']) and in_array($a['extension'], array('jpg','png','gif'))
		and preg_match(','._DIR_IMG.',', $a['fichier'])) {
		include_spip('inc/documents');
		$format = false;
		$commande_test = _IDENTIFY_COMMAND.' -version';
		exec($commande_test, $retour_complet, $retour);
		if (intval($retour) == 0) {
			$format = trim(exec('identify -format %m '.$a['fichier']));
		} elseif (extension_loaded('imagick')) {
			$Imagick = new Imagick($a['fichier']);
			$format = $Imagick->getImageFormat();
		}
		$new_extension = false;
		if ($format) {
			switch ($format) {
				case 'PNG':
					$new_extension = 'png';
					$mime = 'image/png';
					break;
				case 'JPEG':
					$new_extension = 'jpg';
					$mime = 'image/jpeg';
					break;
				case 'GIF':
					$new_extension = 'gif';
					$mime = 'image/gif';
					break;
			}
			if ($new_extension && $new_extension != $a['extension']) {
				$new_fichier = basename(substr($a['fichier'], 0, strrpos($a['fichier'], '.'))).
					'.'.$new_extension;
				$dir = creer_repertoire_documents('distant'); # IMG/distant/
				$dir = sous_repertoire($dir, $new_extension);
				$ok_new_fichier = deplacer_fichier_upload($a['fichier'], $dir.$new_fichier, true);
				if ($ok_new_fichier) {
					$a['fichier'] = $dir.$new_fichier;
					$a['mime_type'] = $mime;
				}
				$a['extension'] = $new_extension;
				$a['type_image'] = $new_extension;
			}
			unset($a['body']);
			$a['distant'] = 'oui';
			$a['mode'] = 'document';
			$flux = $a;
		}
	}
	return $flux;
}


function medias_identifier_image_preparer_filtre($flux) {
	if (preg_match('/distant/', $flux['args']['img'])
		or (!preg_match(','._DIR_IMG.',', $flux['args']['img'])
			and !preg_match(','._DIR_VAR.',', $flux['args']['img']))) {
		if (in_array($flux['data']['format_source'], array('jpg', 'png', 'gif'))) {
			$format = false;
			$new_extension = false;
			$fichier = extraire_attribut($flux['data']['tag'], 'src');
			if (!defined('_IDENTIFY_COMMAND')) {
				define('_IDENTIFY_COMMAND', 'identify');
			}
			$commande_test = _IDENTIFY_COMMAND.' -version';
			exec($commande_test, $retour_complet, $retour);
			if (intval($retour) == 0) {
				$format = trim(exec('identify -format %m '.$fichier));
			} elseif (extension_loaded('imagick')) {
				$Imagick = new Imagick($fichier);
				$format = $Imagick->getImageFormat();
			}
			if ($format) {
				switch ($format) {
					case 'PNG':
						$new_extension = 'png';
						$fonction_imagecreatefrom = '_imagecreatefrompng';
						$fonction_image = '_image_imagepng';
						break;
					case 'JPEG':
						$new_extension = 'jpg';
						$fonction_imagecreatefrom = '_imagecreatefromjpeg';
						$fonction_image = '_image_imagejpg';
						break;
					case 'GIF':
						$new_extension = 'gif';
						$fonction_imagecreatefrom = '_imagecreatefromgif';
						$fonction_image = '_image_imagegif';
						break;
				}
				if ($new_extension and $new_extension != $flux['data']['format_source']) {
					$flux['data']['fonction_imagecreatefrom'] = $fonction_imagecreatefrom;
					$flux['data']['fonction_image'] = $fonction_image;
					$flux['data']['format_source'] = $new_extension;
					$flux['data']['format_dest'] = $new_extension;
					$flux['data']['creer'] = true;
					$flux['data']['fichier_dest'] = substr($flux['data']['fichier_dest'], 0, strrpos($flux['data']['fichier_dest'], '.')).
						'.'.$new_extension;
				}
			}
		}
	}
	return $flux;
}
