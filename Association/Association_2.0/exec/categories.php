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
		if (autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_categories = generer_url_ecrire('categories');
		$url_ajout_categorie=generer_url_ecrire('edit_categorie','agir=ajoute');
		$url_edit_categorie=generer_url_ecrire('edit_categorie','agir=modifie');
		$url_action_categorie=generer_url_ecrire('action_categorie');
		$url_retour = $_SERVER['HTTP_REFERER'];
		
		//debut_page(_T(''), "", "");
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Cat&eacute;gories de cotisation')) ;
		 echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		
		
		$res=icone_horizontale(_L('Ajouter une cat&eacute;gorie de cotisation'), $url_ajout_categorie, _DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif","rien.gif",false);
		$res.= icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif",false);
		        echo bloc_des_raccourcis($res);	
		
			
		echo debut_droite("",true);
		
		 echo debut_cadre_relief(  "../"._DIR_PLUGIN_ASSOCIATION."/img_pack/calculatrice.gif", false, "", $titre = _T('Cat&eacute;gories de cotisation'));
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr bgcolor='#DBE1C5'>";
		echo '<td><strong>ID</strong></td>';
		echo '<td><strong>Cat&eacute;gorie</strong></td>';
		echo '<td><strong>Libell&eacute; complet</strong></td>';
		echo '<td><strong>Dur&eacute;e (mois)</strong></td>';
		echo '<td><strong>Montant</strong></td>';
		echo '<td><strong>Commentaires</strong></td>';
		echo '<td colspan=2 style="text-align:center;"><strong>Action</strong></td>';
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
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_action_categorie.'&agir=supprime&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td  class="arial11" style="border-top: 1px solid #CCCCCC;text-align:center;"><a href="'.$url_edit_categorie.'&id='.$data['id_categorie'].'"><img src="'._DIR_PLUGIN_ASSOCIATION.'/img_pack/edit-12.gif" title="Modifier"></a></td>';
			echo'  </tr>';
		}     
		echo'</table>';
		
		 echo fin_cadre_relief(true);  		
		fin_page();
	}
?>
