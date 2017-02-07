<?php
/**
 * Définit les autorisations du plugin Crop Image
 *
 * @plugin     Crop Image
 * @copyright  2017
 * @author     tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Jcrop\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function jcrop_autoriser() {
}

function autoriser_logo_recadrer($faire, $type, $id, $qui, $opt) {
	if ($opt == "article") {
		return autoriser('modifier','article',$id);
	}
	if ($opt == "rubrique") {
		return autoriser('modifier','rubrique',$id);
	}
	return false;
}
