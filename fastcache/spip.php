<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

# compatibilite mutualisation (modifier la definition de $site au besoin)
$site = str_replace('www.', '', $_SERVER['HTTP_HOST']);
list($site) = explode(':', $site); // supprimer le :80 (flash)
define('_FC_LANCEUR', 'tmp/fcconfig_' . $site . '.inc');

if (@file_exists(_FC_LANCEUR)) include _FC_LANCEUR;
else {

# ou est l'espace prive ?
@define('_DIR_RESTREINT_ABS', 'ecrire/');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

# rediriger les anciens URLs de la forme page.php3fond=xxx
if (isset($_GET['fond'])) {
	include_spip('inc/headers');
	redirige_par_entete(generer_url_public($_GET['fond']));
 }

# au travail...
include _DIR_RESTREINT_ABS.'public.php';
}

?>
