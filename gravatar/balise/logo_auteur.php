<?php

/**
 *
 * Gravatar : Globally Recognized AVATAR
 *
 * @package     plugins
 * @subpackage  gravatar
 *
 * @author      Fil
 * @license     GNU/GPL
 *
 * @version     $Id$
 **/

 if (!defined("_ECRIRE_INC_VERSION")) return;
 
/**
 * On regarde s'il y a un logo, sinon un gravatar, et on renvoie le tout.
 *
 * Pour ca il faut modifier un peu le code produit par #LOGO_*, pour introduire
 * notre fonction de recherche de logo.
 *
 * @param  Object $p   Arbre syntaxique utilise par le compilo
 * @return Object      Arbre retourne apres traitement
 */
function balise_LOGO_AUTEUR($p) {
	$balise_logo_ = charger_fonction('logo_', 'balise');
	$_email1 = champ_sql('email', $p);
	$_email2 = champ_sql('email_ad', $p);
	$_email3 = champ_sql('email_auteur', $p);
	$_email4 = champ_sql('address', $p);

	$_id = champ_sql('id_auteur', $p);
	$_emailsql = "sql_getfetsel('email','spip_auteurs','id_auteur='.intval($_id))";

	$p = $balise_logo_($p);

	$p->code = 'gravatar_img(sinon((is_null('.$_email1.')?'.$_emailsql.':'.$_email1.'),sinon('.$_email2.', sinon('.$_email3.', '.$_email4.'))), '. $p->code. ')';

	return $p;
}

?>