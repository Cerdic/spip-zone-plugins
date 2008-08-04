<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');
	
	function exec_categories(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$url_categories = generer_url_ecrire('categories');
		$url_ajout_categorie=generer_url_ecrire('edit_categorie','faire=ajoute');
		$url_edit_categorie=generer_url_ecrire('edit_categorie','faire=modifie');
		$url_faire_categorie=generer_url_ecrire('faire_categorie');
		$url_retour = $_SERVER['HTTP_REFERER'];
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Cat&eacute;gories de cotisation'), "", "");
		
		debut_gauche();
		
		debut_boite_info();
		echo association_date_du_jour();	
		fin_boite_info();
		
		
		$res = icone_horizontale(_T('asso:Ajouter une cat&eacute;gorie de cotisation'), $url_ajout_categorie, _DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif","cree.gif", false);	
		$res .= icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif", false);	
		echo bloc_des_raccourcis($res);
		
		creer_colonne_droite();	
		debut_droite();
		
		debut_cadre_relief(  "../"._DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif", false, "", $titre = _T('Cat&eacute;gories de cotisation'));
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr bgcolor='#DBE1C5'>";
		echo '<td><strong>ID</strong></td>';
		echo '<td><strong>Cat&eacute;gorie</strong></td>';
		echo '<td><strong>Libell&eacute; complet</strong></td>';
		echo '<td><strong>Dur&eacute;e (mois)</strong></td>';
		echo '<td><strong>Montant</strong></td>';
		echo '<td><strong>Commentaires</strong></td>';
		echo '<td colspan=2 style="text-align:center;"><strong>faire</strong></td>';
		echo'  </tr>';
		$query = spip_query ( "SELECT * FROM spip_asso_categories ORDER by id_categorie" ) ;
		while ($data = spip_fetch_array($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right">'.$data['id_categorie'].'</td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['valeur'].'</td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['libelle'].'</td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right">'.$data['duree'].'</td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:right">'.$data['cotisation'].'</td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;">'.$data['commentaires'].'</td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_faire_categorie.'&faire=supprime&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_edit_categorie.'&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Modifier"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  		
		echo fin_gauche(),fin_page();
	}
?>
