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
	
	function association_onglets(){
		global $id_auteur, $connect_id_auteur, $connect_statut, $statut_auteur, $options;
		
		echo debut_onglet();
			
		//echo onglet(_T('spiplistes:Historique_des_envois'), generer_url_ecrire("spip_listes"), "messagerie", $onglet, _DIR_PLUGIN_SPIPLISTES."img_pack/stock_hyperlink-mail-and-news-24.gif");
		//echo onglet(_T('spiplistes:Listes_de_diffusion'), generer_url_ecrire("listes_toutes"), "messagerie", $onglet, _DIR_PLUGIN_SPIPLISTES."img_pack/reply-to-all-24.gif");
		//echo onglet(_T('spiplistes:Suivi_des_abonnements'), generer_url_ecrire("abonnes_tous"), "messagerie", $onglet, _DIR_PLUGIN_SPIPLISTES."img_pack/addressbook-24.gif");
		
		$link1= generer_url_ecrire('adherents');
		$link2= generer_url_ecrire('dons');
		$link4= generer_url_ecrire('ventes');
		$link5= generer_url_ecrire('activites');
		$link6= generer_url_ecrire('comptes');
		$link7= generer_url_ecrire('ressources');
		
		echo onglet(_T('asso:menu2_titre_gestion_membres'), $link1, '', 'Membres', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/actif.png','rien.gif' );  
		if (lire_config("association/dons")) {
			echo onglet(_T('asso:menu2_titre_gestion_dons'), $link2, '', 'Dons', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/bienfaiteur.png','rien.gif' ); 
		}
		if (lire_config("association/ventes")) {
			echo onglet(_T('asso:menu2_titre_ventes_asso'), $link4, '', 'Ventes', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/journaux.png','rien.gif' ); 
		}
		if (lire_config("association/activites")) {
			echo onglet(_T('asso:menu2_titre_gestion_activites'), $link5, '', 'Activites', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
		}
		if (lire_config("association/prets")) {
			echo onglet(_T('asso:menu2_titre_gestion_prets'), $link7, '', 'Prets', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
		}
		if (lire_config("association/comptes")) {
			echo onglet(_T('asso:menu2_titre_livres_comptes'), $link6, '', 'Comptes', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/livredor.png','rien.gif' ); 
		}
		
		echo fin_onglet();
	}
?>