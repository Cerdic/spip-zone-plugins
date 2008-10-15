<?php


/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin Mots-Partout                                                    *
 *                                                                         *
 *  Copyright (c) 2006-2008                                                *
 *  Pierre ANDREWS, Yoann Nogues, Emmanuel Saint-James                     *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 *    This program is free software; you can redistribute it and/or modify *
 *    it under the terms of the GNU General Public License as published by * 
 *    the Free Software Foundation.                                        *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

//YOANN
// http://doc.spip.org/@calcul_generation
function calcul_generationmot ($generation) {
	include_spip('base/abstract_sql');
	$lesfils = array();
	$result = sql_select(array('id_groupe'),
				array('spip_groupes_mots AS groupes_mots'),
				array(calcul_mysql_in('id_parent', 
					$generation,
						      '')));
	while ($row = sql_fetch($result))
		$lesfils[] = $row['id_groupe'];
	return join(",",$lesfils);
}

// http://doc.spip.org/@calcul_branche
function calcul_branchemot ($generation) {
	if (!$generation) 
		return '0';
	else {
		$branche[] = $generation;
		while ($generation = calcul_generationmot ($generation))
			$branche[] = $generation;
		return join(",",$branche);
	}
}

?>
