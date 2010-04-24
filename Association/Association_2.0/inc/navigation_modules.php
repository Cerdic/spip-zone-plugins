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
		
		echo debut_onglet();
			
		$link1= generer_url_ecrire('adherents');
		$link2= generer_url_ecrire('dons');
		$link4= generer_url_ecrire('ventes');
		$link5= generer_url_ecrire('activites');
		$link6= generer_url_ecrire('comptes');
		$link7= generer_url_ecrire('ressources');
		
		echo onglet(_T('asso:menu2_titre_gestion_membres'), $link1, '', 'Membres', _DIR_PLUGIN_ASSOCIATION_ICONES.'annonce.gif','rien.gif' );  
		if (lire_config("association/dons")) {
			echo onglet(_T('asso:menu2_titre_gestion_dons'), $link2, '', 'Dons', _DIR_PLUGIN_ASSOCIATION_ICONES.'dons.gif','rien.gif' ); 
		}
		if (lire_config("association/ventes")) {
			echo onglet(_T('asso:menu2_titre_ventes_asso'), $link4, '', 'Ventes', _DIR_PLUGIN_ASSOCIATION_ICONES.'ventes.gif','rien.gif' ); 
		}
		if (lire_config("association/activites")) {
			echo onglet(_T('asso:menu2_titre_gestion_activites'), $link5, '', 'Activites', _DIR_PLUGIN_ASSOCIATION_ICONES.'activites.gif','rien.gif' ); 
		}
		if (lire_config("association/prets")) {
			echo onglet(_T('asso:menu2_titre_gestion_prets'), $link7, '', 'Prets', _DIR_PLUGIN_ASSOCIATION_ICONES.'pret1.gif','rien.gif' ); 
		}
		if (lire_config("association/comptes")) {
			echo onglet(_T('asso:menu2_titre_livres_comptes'), $link6, '', 'Comptes', _DIR_PLUGIN_ASSOCIATION_ICONES.'comptes.gif','rien.gif' ); 
		}
		
		echo fin_onglet();
	}
?>
