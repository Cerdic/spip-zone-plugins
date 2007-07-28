<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

	$link1= generer_url_ecrire('adherents');
	$link2= generer_url_ecrire('dons');
	$link3= generer_url_ecrire('edit_relances');
	$link4= generer_url_ecrire('ventes');
	$link5= generer_url_ecrire('activites');
	$link6= generer_url_ecrire('comptes');
	$link7= generer_url_ecrire('ressources');

	debut_raccourcis();
	icone_horizontale(_T('asso:menu2_titre_gestion_membres'), $link1, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' ); 
	icone_horizontale(_T('asso:menu2_titre_relances_cotisations'),$link3,  '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ico_panier.png','rien.gif' ); 
	if (lire_config("association/dons")) {
		icone_horizontale(_T('asso:menu2_titre_gestion_dons'), $link2, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/bienfaiteur.png','rien.gif' ); 
	}
	if (lire_config("association/ventes")) {
		icone_horizontale(_T('asso:menu2_titre_ventes_asso'), $link4, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/journaux.png','rien.gif' ); 
	}
	if (lire_config("association/activites")) {
		icone_horizontale(_T('asso:menu2_titre_gestion_activites'), $link5, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	}
	if (lire_config("association/comptes")) {
		icone_horizontale(_T('asso:menu2_titre_livres_comptes'), $link6, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	}
	if (lire_config("association/prets")) {
		icone_horizontale(_T('asso:menu2_titre_prets'), $link7, '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	}
	fin_raccourcis();

?>