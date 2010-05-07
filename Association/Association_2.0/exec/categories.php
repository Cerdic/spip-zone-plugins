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
	
function exec_categories(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_categories = generer_url_ecrire('categories');
		$url_ajout_categorie=generer_url_ecrire('edit_categorie','agir=ajoute');
		$url_edit_categorie=generer_url_ecrire('edit_categorie','agir=modifie');
		$url_action_categorie=generer_url_ecrire('action_categorie');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		//debut_page(_T(''), "", "");
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:categories_de_cotisations')) ;
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		
		$res=association_icone(_T('asso:ajouter_une_categorie_de_cotisation'),  $url_ajout_categorie, "calculatrice.gif");
		$res.= association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");
		echo bloc_des_raccourcis($res);	
		
			
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES."calculatrice.gif", false, "", _T('asso:toutes_categories_de_cotisations'));
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr bgcolor='#DBE1C5'>";
		echo '<td><strong>' . _T('asso:id') . '</strong></td>';
		echo '<td><strong>' . _T('asso:categorie') . '</strong></td>';
		echo '<td><strong>' . _T('asso:libelle_complet') . '</strong></td>';
		echo '<td><strong>' . _T('asso:duree_mois') . '</strong></td>';
		echo '<td><strong>' . _T('asso:montant') . '</strong></td>';
		echo '<td><strong>' . _T('asso:commentaires') . '</strong></td>';
		echo '<td colspan=2 style="text-align:center;"><strong>' . _T('asso:action') . '</strong></td>';
		echo'  </tr>';
		$query = sql_select('*', 'spip_asso_categories', '', "id_categorie" ) ;
		while ($data = sql_fetch($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td  class="arial11 border1" style="text-align:right">'.$data['id_categorie'].'</td>';
			echo '<td  class="arial11 border1">'.$data['valeur'].'</td>';
			echo '<td  class="arial11 border1">'.$data['libelle'].'</td>';
			echo '<td  class="arial11 border1" style="text-align:right">'.$data['duree'].'</td>';
			echo '<td  class="arial11 border1" style="text-align:right">'.$data['cotisation'].'</td>';
			echo '<td  class="arial11 border1">'.$data['commentaires'].'</td>';
			echo '<td  class="arial11 border1" style="text-align:center;"><a href="'.$url_action_categorie.'&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td  class="arial11 border1" style="text-align:center;"><a href="'.$url_edit_categorie.'&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="Modifier"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		echo fin_cadre_relief(true);  		
		echo fin_gauche(), fin_page();
	}
}
?>
