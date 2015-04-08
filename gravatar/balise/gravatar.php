<?php
/**
 *
 * Gravatar : Globally Recognized AVATAR
 *
 * @package     plugins
 * @subpackage  gravatar
 *
 * @author      Thomas Beaumanoir, Clever Age <http://www.clever-age.com>
 * @copyright   Copyright (c) 2006
 * @license     GNU/GPL
 *
 * Revisee 2010 C.Morin pour passage en balise statique qui permet l'application de filtrer
 * et la mise en cache
 *
 * @version     $Id$
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * balise #GRAVATAR{email[,size[,defaut]]}
 *   size : taille en px
 *   defaut : image par defaut en l'absence de gravatar
 *
 * @param  Object $p  Arbre syntaxique utilise par le compilo
 * @return Object     Arbre retourne apres traitement
 */
function balise_GRAVATAR($p) {
	$_email = interprete_argument_balise(1,$p);
	if (!$_email) $_email = "''";
	$_size = interprete_argument_balise(2,$p);
	if (!$_size) $_size = "''";
	$_default = interprete_argument_balise(3,$p);
	if (!$_default) $_default = "''";

	$p->code = "inserer_attribut(filtrer('image_reduire',sinon(gravatar($_email),$_default), (\$s=$_size) ?\$s: 80), 'alt', '')";
	return $p;

}
	
?>