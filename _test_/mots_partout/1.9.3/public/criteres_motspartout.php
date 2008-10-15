<?

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

//ajout d'un critere {branchemot ?}

function critere_branchemot($idb, &$boucles, $crit)
{
	$boucle = &$boucles[$idb];
	$arg = calculer_argument_precedent($idb, 'id_groupe', $boucles);

	$c = "sql_in('" . $boucle->id_table . ".id_groupe', calcul_branchemot($arg)"
	  . ($crit->not ? ", 'NOT'" : '')
	  . ")";

	$boucle->where[]= $crit->cond ? "($arg ? $c : 1=1)" : $c;
}	
 
?>
