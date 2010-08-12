<?php
/**
* Definir les fonctions php qui manque a la 1.9.x
**/
include_spip('inc/presentation');

if (!function_exists(sql_count))
{
	function sql_count ($res)
	{	return spip_num_rows($res);
	}
}

if (!function_exists(sql_showtable))
{
	function sql_showtable ($t, $b, $r)
	{	return spip_abstract_showtable($t,$r,$b);
	}
}

if (!function_exists(sql_insert))
{	function sql_insert ($table, $noms, $valeurs)
	{	return spip_abstract_insert($table, $noms, $valeurs);
	}
}

if (!function_exists(icone_inline))
{
	function icone_inline($texte, $lien, $fond, $fonction="", $align="", $ajax=false, $javascript='')	
	{	return icone($texte, $lien, $fond, $fonction, $align, false);
	}
}
?>