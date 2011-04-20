<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James & Jeannot Lapin     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_META($p)
{
        if (!$arg = interprete_argument_balise(1,$p))
          $arg = "''";
        $p->code = 'choisir_meta(' . $arg . ')';
        return $p;
}

function choisir_meta($nom)
{
	if ($nom[0]!=='/')
	  $table = 'meta';
	else {
	  list(,$table, $nom) = explode('/', $nom);
	  $table .= '_metas';
	  if (!isset($GLOBALS[$table])) $table = 'meta';
	}
	return $nom ? $GLOBALS[$table][$nom] : $GLOBALS[$table];
}

?>
