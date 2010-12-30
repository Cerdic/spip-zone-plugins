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
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:categories_de_cotisations')) ;
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		
		$res=association_icone(_T('asso:ajouter_une_categorie_de_cotisation'),  generer_url_ecrire('edit_categorie','agir=ajoute'), "calculatrice.gif");
		$res.= association_icone(_T('asso:bouton_retour'), str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']), "retour-24.png");
		echo bloc_des_raccourcis($res);	
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES."calculatrice.gif", false, "", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ._T('asso:toutes_categories_de_cotisations'));
		
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>' . _T('asso:id') . "</th>\n";
		echo '<th>' . _T('asso:categorie') . "</th>\n";
		echo '<th>' . _T('asso:libelle_complet') . "</th>\n";
		echo '<th>' . _T('asso:duree_mois') . "</th>\n";
		echo '<th>' . _T('asso:montant') . "</th>\n";
		echo '<th>' . _T('asso:commentaires') . "</th>\n";
		echo '<th colspan="2" style="text-align: center;">' . _T('asso:action') . "</th>\n";
		echo'  </tr>';
		$query = sql_select('*', 'spip_asso_categories', '', "id_categorie" ) ;
		while ($data = sql_fetch($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11 border1" style="text-align: right;">'.$data['id_categorie']."</td>\n";
			echo '<td class="arial11 border1">'.$data['valeur']."</td>\n";
			echo '<td class="arial11 border1">'.$data['libelle']."</td>\n";
			echo '<td class="arial11 border1" style="text-align: right;">'.$data['duree']."</td>\n";
			echo '<td class="arial11 border1" style="text-align: right;">'.$data['cotisation']."</td>\n";
			echo '<td class="arial11 border1">'.$data['commentaires']."</td>\n";
			echo '<td class="arial11 border1" style="text-align: center;">' . association_bouton(_T('asso:bouton_supprimer'), 'poubelle-12.gif', 'action_categorie','id='.$data['id_categorie']). "</td>\n";
			echo '<td class="arial11 border1" style="text-align: center;">' . association_bouton(_T('asso:bouton_modifier'), 'edit-12.gif', 'edit_categorie','id='.$data['id_categorie']). "</td>\n";
			echo'  </tr>';
		}     
		echo'</table>';
		
		echo fin_cadre_relief(true);  		
		echo fin_page_association();
	}
}
?>
