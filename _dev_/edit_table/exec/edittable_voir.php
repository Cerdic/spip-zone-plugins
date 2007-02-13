<?php
//
// exec/edittable.php
//

include_spip("inc/presentation");

function exec_edittable_voir(){
	echo debut_page(_T('edittable:spip_edittable'));
		echo debut_gauche();
			echo debut_boite_info();
				echo '<center><font face="Verdana,Arial,Sans,sans-serif" size="1"><b>'._T('edittable:edittable_numero').'</b></font><br><font face="Verdana,Arial,Sans,sans-serif" size="6"><b>'._request('uid').'</b></font></center>';
				//echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'._T('edittable:edittable_numero');
				//echo '<br /><span class="spip_xx-large">'._request('uid').'</span></div>';
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=edittable_edit&uid=new">'._T('edittable:ajouter_un_edittable').'</a><br />';
				echo '<a href="?exec=edittable">'._T('edittable:mes_edittable').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			$sql_edittable = "SELECT * FROM spip_articles WHERE id_article = '"._request('id_article')."';";
			$res_edittable = spip_query($sql_edittable);
			$contenu_edittable = spip_fetch_array($res_edittable);
			
			echo debut_cadre_trait_couleur();
			echo '<form action="?exec=edittable_edit&id_article='.$contenu_edittable['id_article'].'" method="post">';
			echo "<input name='action' type='hidden' value='editer_edittable_edit' />
				<input name='redirect' type='hidden' value='".lire_meta('adresse_site')."'/ecrire/?exec=edittable_edit&uid="._request('id_article')."' />
				<input type='hidden' name='editer_edittable' value='oui' />";
			if (_request('new') == "oui") {
				echo '<input type="hidden" name="id_article" value="new" />';
			}else{
				echo '<input type="hidden" name="id_article" value="'.$contenu_edittable['id_article'].'" />';
			}
			echo '<div align="right"><input type="submit" name="new" value="'._T('edittable:editer_maisons_medicales').'" class="fondo"/></div>';
			echo '</form>';
			
			
			
			echo '<span class="spip_large"><b>'.$contenu_edittable['titre'].'</b></span>';
			echo '<br />';
			
			
			
			echo '<br /><b><a href="'.$contenu_edittable['url_site'].'">'.$contenu_edittable['nom_site'].'</a></b>';
			echo '<br /><br />';
			
			
			
			
			echo '<b>'._T('edittable:info_pratique').'</b><br />';
			echo propre($contenu_edittable['info_pratique']);
			echo '<br />';
			echo fin_cadre_trait_couleur();
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
