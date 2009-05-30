<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Popup principal - Outils divers
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_outils() {

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;
	
	// requis spip .. h5/9
	include_spip("inc/actions");
	
	// function requises ...
	include_spip("inc/dw2_inc_admin");
	include_spip("inc/dw2_inc_func");
	include_spip("inc/dw2_inc_pres");
	
	
	// reconstruire .. var=val des get et post
	// var : $outil
	// .. Option .. utiliser : $var = _request($var);
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }
#h.09/03 adaptation 1.9.2
##
include_spip('inc/headers');
http_no_cache();
include_spip('inc/commencer_page');
# + echo sur fonction :

	echo init_entete(_T('dw:outils'),'');
##	
	echo "<body>\n";
	
	
	echo "<a name='haut_page'></a>";
	echo "<div style='margin:5px; width:500px;'>";
	
	
	//
	// titre fenetre + icone retour
	//
	echo "<div class='boite_filet_b center'>";
	bloc_minibout_act(_T('dw:outils'), generer_url_ecrire("dw2_outils"), _DIR_IMG_PACK."administration-24.gif",'','');
	gros_titre(_T('dw:titre_page_outils'));
	echo "<div style='clear:both;'></div>";
	echo "</div>";


	if($outil) {
		
		// menu .. icones
		bloc_minibout_act(_T('dw:conten_repert_img'), generer_url_ecrire("dw2_outils","outil=dossimg"), _DIR_IMG_PACK."rubrique-24.gif",'','');
		bloc_minibout_act(_T('dw:changer_statut_masse'), generer_url_ecrire("dw2_outils", "outil=archives_g"), _DIR_IMG_DW2."catalogue.gif",'','');
		bloc_minibout_act(_T('dw:modif_titre_descriptif'), generer_url_ecrire("dw2_outils","outil=titredesc"), _DIR_IMG_PACK."doc-24.gif",'','');
		bloc_minibout_act(_T('dw:supprimer_doc_du_catalogue'), generer_url_ecrire("dw2_outils","outil=netcat"), _DIR_IMG_PACK."petition-24.gif",'','');
	
		echo "<div style='clear:both;'></div>";
		
		//
		// ... inclure l'outil ...
		//
		include_spip("inc/dw2_inc_".$outil);
		$outil();
		
	}
	else {
	
		// aff. outils disponibles
		bloc_ico_page(_T('dw:conten_repert_img'), generer_url_ecrire("dw2_outils","outil=dossimg"), _DIR_IMG_PACK."rubrique-24.gif");
		bloc_ico_page(_T('dw:changer_statut_masse'), generer_url_ecrire("dw2_outils", "outil=archives_g"), _DIR_IMG_DW2."catalogue.gif");
		bloc_ico_page(_T('dw:modif_titre_descriptif'), generer_url_ecrire("dw2_outils","outil=titredesc"), _DIR_IMG_PACK."doc-24.gif");
		bloc_ico_page(_T('dw:supprimer_doc_du_catalogue'), generer_url_ecrire("dw2_outils","outil=netcat"), _DIR_IMG_PACK."petition-24.gif");

	}



	echo "<br />";
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";


	echo "</div>\n</body>\n</html>";

}
?>
