<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| function :
| changer statut en masse (Outil)
+--------------------------------------------+
*/


function archives_g() {

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;
	

	//
	// Forcer : mise en archive de fiche 
	//
	debut_cadre_trait_couleur(_DIR_IMG_PACK."doc-24.gif", false, "", _T('dw:changer_statut_masse'));

	echo "<div class='verdana3'>".
		_T('dw:info_change_statut_masse').
		"</div><br />";

	// fixe changement statut par defaut
	$chg_statut = "archive";

	// formulaire
	echo "<form action='".generer_url_action("dw2actions", "arg=changerstatut-".$chg_statut)."' method='post'>";
	echo "<input type='text' name='num_arch' value='' size='50' class='fondl' />";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_outils","outil=archives_g")."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-changerstatut-".$chg_statut)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' /><br />";
	echo "<input type='checkbox' name='inverse' value='actif' />"._T('dw:info_change_statut_inverser')."\n";
	echo "<div class='bloc_bouton_r'>";
		echo "<input type='submit' value='"._T('dw:archiver_tout')."' class='fondo' />";
	echo "</div>\n";
	echo "</form>\n";

	fin_cadre_trait_couleur();

}
?>
