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

	debut_raccourcis();
	icone_horizontale(_T('asso:menu2_titre_gestion_membres'), generer_url_ecrire('adherents'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' ); 
	icone_horizontale(_T('asso:menu2_titre_relances_cotisations'), generer_url_ecrire('edit_relances'),  '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ico_panier.png','rien.gif' ); 
	if (lire_config("association/dons")) {
		icone_horizontale(_T('asso:menu2_titre_gestion_dons'), generer_url_ecrire('dons'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/bienfaiteur.png','rien.gif' ); 
	}
	if (lire_config("association/ventes")) {
		icone_horizontale(_T('asso:menu2_titre_ventes_asso'), generer_url_ecrire('ventes'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/journaux.png','rien.gif' ); 
	}
	if (lire_config("association/activites")) {
		icone_horizontale(_T('asso:menu2_titre_gestion_activites'), generer_url_ecrire('activites'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	}
	if (lire_config("association/prets")) {
		icone_horizontale(_T('asso:menu2_titre_prets'), enerer_url_ecrire('ressources'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	}
	if (lire_config("association/comptes")) {
		icone_horizontale(_T('asso:menu2_titre_livres_comptes'), generer_url_ecrire('comptes'), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
	}
	
	fin_raccourcis();

?>