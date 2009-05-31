<?php
//
// exec/edittable.php
//
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/presentation");
include_spip("inc/tableau");

function exec_edittable_edit(){
	echo debut_page(_T('edittable:spip_edittable'));
		echo debut_gauche();
			echo debut_boite_info();
				echo '<center><font face="Verdana,Arial,Sans,sans-serif" size="1"><b>'._request('table').'</b></font><br><font face="Verdana,Arial,Sans,sans-serif" size="2"><b>'._request('colonne_cle').'&nbsp;=&nbsp;'._request('valeur_cle').'</b></font></center>';

			echo fin_boite_info();
			echo debut_raccourcis();
				echo '<a href="?exec=edittable&table='._request('table').'">'._T('edittable:retour_a_la_table').'</a><hr />';
				echo '<a href="?exec=listetable">'._T('edittable:les_table').'</a>';
			echo fin_raccourcis();
		echo debut_droite();
		if ($GLOBALS['connect_statut'] == "0minirezo")
		{
			$sql_edittable = "SELECT * FROM "._request('table')." WHERE ".utf8_decode(_request('colonne_cle'))." = '"._request('valeur_cle')."';";
			$res_edittable = spip_query($sql_edittable);
			$contenu_edittable = spip_fetch_array($res_edittable);
			
			echo debut_cadre_formulaire();
			echo '<form action="'.lire_meta('adresse_site').'/spip.php" method="post" >';
			if (_request('new') == "oui") {
				echo '<input type="hidden" name="id_article" value="new" />';
			}else{
				echo '<input type="hidden" name="id_article" value="'.$contenu_edittable['id_titre'].'" />';
			}
			//var_dump($contenu_edittable);
			afficher_tableau_complex_div($contenu_edittable,_request('table'));
			echo '<input type="hidden" name="action" value="edittable_save" />
				<input type="hidden" name="valeur_cle" value="'._request('valeur_cle').'" />
				<input type="hidden" name="table" value="'._request('table').'" />
				<input type="hidden" name="colonne_cle" value="'._request('colonne_cle').'" />
				<input type="hidden" value="'.lire_meta('adresse_site').'/ecrire/?exec=edittable_voir&amp;valeur_cle='._request('valeur_cle').'&amp;table='._request('table').'&amp;colonne_cle='._request('colonne_cle').'" name="redirect" />
				<div align="right"><input type="submit" value="'._T('edittable:enregistrer').'" class="fondo"/></div>
				</form>';
			echo fin_cadre_formulaire();
		}			
	if ($GLOBALS['spip_version_code']>=1.92) { echo fin_gauche(); }
	echo fin_page();
}
?>
