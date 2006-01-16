<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if ($_GET['exec'] AND
preg_match(',^[0-9a-z_]*$,i', $_GET['exec']))
	$exec = $_GET['exec'];
 else $exec = "accueil";

if (!defined('_ECRIRE_INC_VERSION')) include ("ecrire/inc_version.php");

$var_f = include_fonction($exec);
$var_f();

?>
