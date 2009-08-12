<?php
	/**
	* Plugin favoris
	*
	* Copyright (c) 2009
	* Bernard Blazin 
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	
	include_spip('inc/navigation_modules');
	
	function exec_favoris() {
		
		global $connect_statut, $connect_toutes_rubriques, $table_prefix;
		
		include_spip ('inc/acces_page');
		
		$url_adherents = generer_url_ecrire('favoris');
		
		
		
		 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('Favoris Texte')) ;
		
		
		echo debut_gauche("",true);
		
			
		echo debut_boite_info(true);
		//echo favoris_date_du_jour();	
		
		
		// TOTAUX	
		
		echo '<div><strong>'._T('favoris:Nombre d\'articles mis en favoris').'</strong></div>';
		$nombre=0;
			$query=spip_query("SELECT * FROM spip_favtextes ");
			$nombre=sql_count($query);
			echo '<div style="float:right;text_align:right">'.$nombre.'</div>'; 	
		echo '<div style="float:right;text_align:right">'.$nombre_total.'</div>';
		echo '<div>'._T('nombre total').'</div>';
		echo fin_boite_info(true);	
		
		
		
		
		echo debut_droite("",true);
		
		echo debut_cadre_relief(  "", true, "", $titre = _T('Les favoris textes'));		
		
		
		
		//Affichage de la liste
		
		echo "<table border=0 cellpadding=2 cellspacing=0 width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo '<tr bgcolor="#DBE1C5">';
		echo '<td><strong>';
		 echo _T('favoris:id_auteur');
		
		echo '</strong></td>';
		echo '<td><strong>'._T('favoris:photo').'</strong></td>';
		echo '<td><strong>'._T('favoris:nom').'</strong></td>';
		echo '<td><strong>'._T('favoris:id_rubrique').'</strong></td>';
		echo '<td><strong>'._T('').'</strong></td>';
		echo '<td><strong>'._T('favoris:id_article').'</strong></td>';
		echo '<td colspan="4" style="text-align:center;"><strong>'._T('favoris:Titre').'</strong></td>';
		echo '</tr>';
	
		$sql=spip_query("SELECT * FROM spip_favtextes, spip_articles WHERE spip_favtextes.id_texte= spip_articles.id_article ORDER BY id_auth ASC");
while($data = sql_fetch($sql)){
			$id_auteur=$data['id_auth'];  $id_texte= $data['id_texte']; $titreart=$data['titre']; $id_rub=$data['id_rubrique'];
		$query = spip_query ( "SELECT nom,id_auteur FROM spip_auteurs  WHERE id_auteur='$id_auteur' ");
		
		while ($data = spip_fetch_array($query)) {
		$nom= $data['nom'];
		
		//$query = spip_query ( "SELECT * FROM spip_articles  WHERE id_article=$id_texte");
		
		//while ($p = spip_fetch_array($query)) {
			echo '<tr> ';
			echo '<td style="border-top: 1px solid #CCCCCC;text-align:right;" class ='.$class.'>';
			 
			 echo $data["id_auteur"];
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ="'.$class.'">';
			$logo="../IMG/auton".$data['id_auteur'];
			$id_auteur=$data['id_auteur'];
			if(@file('../IMG/auton'.$id_auteur.'.jpg')!=""){
             echo '<img src="../IMG/auton'.$data['id_auteur'].'.jpg" alt="&nbsp;" width="60"  title="'.$data["nom"].' ">';
            }else{
            echo '<img src="'._DIR_PLUGIN_FAVORIS.'/img_pack/ajout.gif" alt="&nbsp;" width="10"  title="'.$data["nom"].' ">';
            }
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			
			 echo $nom.'</td>'; 
			
			//echo '<a href="mailto:'.$data["email"].'">'.$data["nom"].'</a></td>';
			
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>'.$id_rub.'</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
						
			echo '</td>'; 
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			echo $id_texte;
			echo '</td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'>';
			
		
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'></td>';
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'></td>'; 
		
			echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><a href="?exec=articles&id_article='.$id_texte.'">'.$titreart.'</a></td>';//}
			//echo '<td style="border-top: 1px solid #CCCCCC;" class ='.$class.'><input name="delete[]" type="checkbox" value='.$data['id_auteur'].'></td>';
			echo '</tr>';
		
		}}
		echo '</table>';
		
		
		
		echo fin_cadre_relief(true);  
		 echo fin_gauche(), fin_page();
	}
?>
