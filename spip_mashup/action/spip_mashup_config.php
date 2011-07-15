<?php
/**
* Plugin SPIP-Mashup
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
* Configuration du plugin
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/auth');
include_spip("inc/lang");
include_spip('inc/compat_192');
include_spip('inc/config');
include_spip('mashup_fonctions');

function action_spip_mashup_config_dist() 
{	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// Admin total ?
	if (autoriser('configurer', 'plugins'))
	{	if (isset($_POST['modifier']))
		{	$options = mashup_getConfig();
			$options["backLayer"] = isset($_POST['backLayer']);
			$options["zoom_pere"] = isset($_POST['zoom_pere']);
			$options["zoom_img"] = isset($_POST['zoom_img']);
			$options["no_popup"] = ($_POST['no_popup']=="1");
			if (intval($_POST['largeur'])) $options["largeur"] = intval($_POST['largeur']);
			if (intval($_POST['largeur_mot'])) $options["largeur_mot"] = intval($_POST['largeur_mot']);
			if (intval($_POST['bord'])) $options["bord"] = intval($_POST['bord']);
			$options["bord_couleur"] = ($_POST['couleur']);
			$options = serialize($options);
			ecrire_meta('spip_mashup',$options);
			ecrire_metas();
		}
	}
	
	redirige_par_entete(_DIR_RESTREINT.urldecode(_request('redirect')));
}
?>