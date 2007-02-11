<?php
//
// exec/editer_digg.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");

function exec_editer_digg(){
	echo debut_page(_T('spipdigg:spip_diggs'));
		echo debut_gauche();
			echo debut_boite_info();
				echo "Hello World !";
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=editer_digg&id_digg=new">'._T('spipdigg:ajouter_un_digg').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
			$oncontinue = true;
			if (_request('id_digg') != "new"){
				$sql_cherche_digg = "SELECT diggs.* FROM spip_diggs diggs, spip_diggs_auteurs diggs_auteurs WHERE diggs.id_digg = '"._request('id_digg')."' AND diggs.id_digg = diggs_auteurs.id_digg AND diggs_auteurs.id_auteur ='".$GLOBALS['auteur_session']['id_auteur']."';";
				//echo $sql_cherche_digg.'<br />';
				$res_cherche_digg = spip_query($sql_cherche_digg);
				if(spip_num_rows($res_cherche_digg) == 0) {
					$oncontinue = false;
				}else{
					$contenu = spip_fetch_array($res_cherche_digg);
				}
				
			}
			
			if ($oncontinue){
				echo debut_cadre_formulaire();
					echo '<form action="'.lire_meta('adresse_site').'/spip.php" method="post"><input type="hidden" name="redirect" value="'.lire_meta('adresse_site').'/spip.php" />';
					echo "
						<input name='action' type='hidden' value='editer_digg' />
						<input name='redirect' type='hidden' value='".lire_meta('adresse_site').'/ecrire/?exec=diggs'."' />
						<input type='hidden' name='editer_digg' value='oui' />
					";
					
					echo '<b>'._T('spipdigg:titre_digg').'</b>';
					echo '<br /><input type="text" name="titre" value="'.$contenu[titre].'" class="formo spip_small" />';
					echo '<br />';
					echo '<b>'._T('spipdigg:url_digg').'</b>';
					echo '<br /><input type="text" name="url_digg" value="'.$contenu[url_digg].'" class="forml" />';
					echo '<br />';
					echo '<b>'._T('spipdigg:descriptif_digg').'</b>';
					echo '<br />'.barre_textarea ( $contenu['descriptif'], '10', $cols, $lang='' );
					echo '<input type="hidden" name="id_digg" value="'._request('id_digg').'" />';
					echo '<br />';
					echo '<div align="right"><input type="submit" name="new" value="'._T('spipdigg:enregistrer').'" class="fondo"/></div>';
					echo '</form>';
				echo fin_cadre_formulaire();
			}else{
				echo _T('spipdigg:pas_de_digg_ou_pas_les_droits_pour_editer');
			}
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
