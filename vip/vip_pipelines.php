<?
if (!defined("_ECRIRE_INC_VERSION")) return;
function vip_ajouterBoutons($boutons_admin) {
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "naviguer"
	  $boutons_admin['configuration']->sousmenu['vip_cfg']= new Bouton(
		"../"._DIR_PLUGIN_VIP."/img_pack/vip-24.png",  // icone
		_L('Autorisations VIP'),// titre
		'cfg','cfg=vip'	
		);
	}
	return $boutons_admin;
}
?>