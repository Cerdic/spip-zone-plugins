<?php
//
// exec/diggs.php
//

include_spip("inc/presentation");

function exec_diggs(){
	echo debut_page(_T('spipdigg:spip_diggs'));
		echo debut_gauche();
			echo debut_boite_info();
				echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'._T('spipdigg:digg_numero');
				echo '<br /><span class="spip_xx-large">'._request('id_digg').'</span></div>';
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=editer_digg&id_digg=new">'._T('spipdigg:ajouter_un_digg').'</a><br />';
				echo '<a href="?exec=spipdigg">'._T('spipdigg:mes_diggs').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			$sql_digg = "SELECT * FROM spip_diggs WHERE id_digg='"._request('id_digg')."';";
			$res_digg = spip_query($sql_digg);
			$contenu_digg = spip_fetch_array($res_digg);
			
			echo debut_cadre_trait_couleur();
			echo '<form action="?exec=editer_digg" method="post">';
			echo "<input name='action' type='hidden' value='editer_digg' />
				<input name='redirect' type='hidden' value='".lire_meta('adresse_site')."'/ecrire/?exec=editer_digg' />
				<input type='hidden' name='editer_digg' value='oui' />";
			if (_request('new') == "oui") {
				echo '<input type="hidden" name="id_digg" value="new" />';
			}else{
				echo '<input type="hidden" name="id_digg" value="'.$contenu_digg['id_digg'].'" />';
			}
			echo '<div align="right"><input type="submit" name="new" value="'._T('spipdigg:editer_digg').'" class="fondo"/></div>';
			echo '</form>';
			
			
			
			echo '<span class="spip_large"><b>'.$contenu_digg['titre'].'</b></span>';
			echo '<br />';
			
			
			
			echo '<br /><b><a href="'.$contenu_digg['url_digg'].'">'.$contenu_digg['url_digg'].'</a></b>';
			echo '<br /><br />';
			
			
			
			
			echo '<b>'._T('spipdigg:descriptif_digg').'</b><br />';
			echo propre($contenu_digg['descriptif']);
			echo '<br />';
			echo fin_cadre_trait_couleur();
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
