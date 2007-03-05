<?php
//
// exec/edittable.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");
include_spip("inc/tableau");
function exec_edittable_voir(){
	echo debut_page(_T('edittable:spip_edittable'));
		echo debut_gauche();
			echo debut_boite_info();
				echo '<center><font face="Verdana,Arial,Sans,sans-serif" size="1"><b>'._request('table').'</b></font><br><font face="Verdana,Arial,Sans,sans-serif" size="2"><b>'._request('colonne_cle').'&nbsp;=&nbsp;'._request('valeur_cle').'</b></font></center>';
			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=listetable">'._T('edittable:mes_edittable').'</a>';
			echo fin_raccourcis();
			if(_request('sql_command')){
				echo debut_boite_info();
				echo '<center><font face="Verdana,Arial,Sans,sans-serif" size="3"><b>'._T('edittable:command_sql_executee').'</b></font><br /><br /><code>'._request('sql_command').'</code><br />';
				echo fin_boite_info();
			}
		echo debut_droite();
			$sql_edittable = "SELECT * FROM "._request('table')." WHERE "._request('colonne_cle')." = '"._request('valeur_cle')."';";
			$res_edittable = spip_query($sql_edittable);
			$contenu_edittable = spip_fetch_array($res_edittable);
			
			echo debut_cadre_trait_couleur();
				echo '<form action="?exec=edittable_edit&valeur_cle='._request('valeur_cle').'&table='._request('table').'&colonne_cle='._request('colonne_cle').'" method="post">
				<div align="right"><input type=submit value="'._T('adittable:edite_enregistrement').'" class="fondo"/></div>
				</form>';
			afficher_tableau_div($contenu_edittable);
			echo fin_cadre_trait_couleur();
			
			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
