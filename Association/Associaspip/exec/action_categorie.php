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
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_action_categorie(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_action_categorie=generer_url_ecrire('action_categorie');
		
		$id_categorie=intval(_request('id'));
		
		$libelle=$_POST['libelle'];
		$valeur=$_POST['valeur'];
		$duree=$_POST['duree'];
		$montant=$_POST['montant'];
		$commentaires=$_POST['commentaires'];
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:categories_de_cotisations')) ;
		echo debut_gauche("",true);
			
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		
		echo bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  $_POST['url_retour'], "retour-24.png"));

		echo debut_droite("",true);
			
		echo debut_cadre_relief(  "", false, "",  _T('asso:categories_de_cotisations'));
			
		echo '<p><strong>' . _T('asso:vous_vous_appretez_a_effacer_le_categorie') .$id_categorie.'</strong></p>';
		$res = '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></p>';
		echo redirige_action_post('supprimer_categories', $id_categorie, 'categories', '', $res);

		fin_cadre_relief();  
		echo fin_page_association(); 
	}
}
?>
