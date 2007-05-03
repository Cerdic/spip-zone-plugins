<?php
/*
 * Plugin cfg : ecrire/?exec=cfg&cfg=xxxx
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */
// la fonction appelee par le core, une simple "factory" de la classe cfg
function exec_cfg_dist($class = null)
{
	$cfg = charger_fonction("cfg","inc");
	$res = $cfg();
	
	if ($res['erreur']){
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_refuse') .
			$res['erreur']);
		exit;
	}
	else echo $res['html'];
	return;
}

?>