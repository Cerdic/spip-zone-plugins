<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 **/

 if (!defined("_ECRIRE_INC_VERSION")) return;
 
/**
 * La balise #FORM_TYPE
 * Elle récupère dans l'environnement le 'em_type'
 *
 * @return array La liste des extensions correspondantes au type passé en paramètre
 * @param object $p
 */
function balise_FORM_TYPE_dist($p) {
	$v = '@$Pile[0][\'em_type\']';
	$test = interprete_argument_balise(1,$p);
	$p->code = "emballe_medias_generer_extensions(".sinon($test,$v).")";
	//$p->interdire_scripts = false;
	return $p;
}


?>