<?php
//
// exec/edittable.php
//

include_spip("inc/presentation");
include_spip("inc/tableau");

function exec_edittable_edit(){
	echo debut_page(_T('edittable:spip_edittable'));
		echo debut_gauche();
			echo debut_boite_info();
				echo '<div style="font-weight: bold; text-align: center;" class="verdana1 spip_xx-small">'._T('edittable:edittable_numero');
				echo '<br /><span class="spip_xx-large">'._request('uid').'</span></div>';
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=edittable_edit&uid=new">'._T('edittable:ajouter_un_edittable').'</a><br />';
				echo '<a href="?exec=edittable">'._T('edittable:mes_edittable').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			$sql_edittable = "SELECT * FROM spip_articles WHERE id_article = '"._request('id_article')."';";
			$res_edittable = spip_query($sql_edittable);
			$contenu_edittable = spip_fetch_array($res_edittable);
			
			echo debut_cadre_formulaire();
			echo '<form action="?exec=editer_edittable" method="post">';
			echo "<input name='action' type='hidden' value='edittable_edit' />
				<input name='redirect' type='hidden' value='".lire_meta('adresse_site')."'/ecrire/?exec=editer_edittable' />
				<input type='hidden' name='editer_edittable' value='oui' />";
			if (_request('new') == "oui") {
				echo '<input type="hidden" name="id_article" value="new" />';
			}else{
				echo '<input type="hidden" name="id_article" value="'.$contenu_edittable['id_titre'].'" />';
			}
			//var_dump($contenu_edittable);
			editer_tableau_div($contenu_edittable);
			echo fin_cadre_formulaire();
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
