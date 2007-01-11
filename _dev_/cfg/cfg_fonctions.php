<?php
include_spip('balise/config');
/* etend la balise #CONFIG 
 *
 *  cfg plugin for spip (c) toggg 2007 -- licence LGPL
 */

//
// #CONFIG etendue dynamique interpretant les /
//
// Par exemple #CONFIG{xxx/yyy/zzz} fait comme #CONFIG{xxx}['yyy']['zzz']
// xxx etant un tableau serialise dans spip_meta comme avec exec=cfg&cfg=montruc
// Le 2eme argument de la balise est la valeur defaut comme pour la dist
//
// La balise appelle celle de la dist si pas de /
//

// lire_cfg() permet de recuperer une config depuis le php
// $cfg: la config, lire_cfg('montruc') est un tableau
// lire_cfg('montruc/sub') est l'element "sub" de cette config
// $def: un defaut optionnel

function lire_cfg($cfg = '', $def = NULL)
{
	return cfg_meta($cfg . '/', $def);
}
?>
