<?php
function debut_raccourcis(){
	global $spip_display;
		
	if ($spip_display != 4) echo "</font>";
	else echo "</ul>";
		
	echo fin_cadre_enfonce(true);
};
function fin_raccourcis(){
	global $spip_display;
	
	if ($spip_display != 4) echo "</font>";
	else echo "</ul>";
	
	echo fin_cadre_enfonce(true);
}

	debut_raccourcis();	icone_horizontale(_T('asso:Profil de l\'association'), '?exec=cfg&cfg=association', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif', 'rien.gif');	icone_horizontale(_T('asso:Cat&eacute;gories de cotisations'), generer_url_ecrire("categories"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif', '');	icone_horizontale(_T('asso:plan_comptable'), generer_url_ecrire("plan"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/EuroOff.gif', '');		fin_raccourcis();
?>