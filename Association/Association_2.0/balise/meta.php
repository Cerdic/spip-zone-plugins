<?php

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
