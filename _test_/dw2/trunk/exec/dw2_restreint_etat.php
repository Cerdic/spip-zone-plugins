<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Tableau sql dw2_acces_restreint
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_restreint_etat() {
// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');

//
// requis
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");

include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");

#include_spip("inc/dw2_inc_rubriquage");
include_spip("inc/dw2_inc_hierarchie");


// reconstruire .. var=val des get et post
// var : $id_rub,$id_art
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }

//
// prepa
//

// recup des id_rub racine
$q=spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent='0'");
while($r=spip_fetch_array($q)) {
	$tbl_idrub_racine[] = $r['id_rubrique'];
}

// recup la table acces_restreint
$tbl_collect_racine=array();
$tbl_idr=array();
$tbl_ida=array();

$sq=spip_query("SELECT * FROM spip_dw2_acces_restreint");

while($rsq=spip_fetch_array($sq)) {
	// lister rubriques
	if($rsq['id_rubrique']!='0') {
		$rt=spip_query("SELECT titre, id_parent FROM spip_rubriques WHERE id_rubrique='".$rsq['id_rubrique']."'");
		$rl=spip_fetch_array($rt);
		$tbl_idr[$rsq['id_rubrique']]['res']=$rsq['restreint'];
		$tbl_idr[$rsq['id_rubrique']]['titre']=$rl['titre'];
		$tbl_idr[$rsq['id_rubrique']]['parent']=$rl['id_parent'];
		if($rl['id_parent']=='0' && $rsq['restreint']=='0') {
			$tbl_collect_racine[]=$rsq['id_rubrique'];
		}
	}
	// lister articles
	if($rsq['id_article']!='0') {
		$rt=spip_query("SELECT titre FROM spip_articles WHERE id_article='".$rsq['id_article']."'");
		$rl=spip_fetch_array($rt);
		$tbl_ida[$rsq['id_article']]['res']=$rsq['restreint'];
		$tbl_ida[$rsq['id_article']]['titre']=$rl['titre'];
	}
	// NON on s'occupe pas des docs
	/*if($rsq['id_document']!='0') { $tbl_idd[$rsq['id_document']]=$rsq['restreint']; }*/
	
}

// compte nbr de ligne par tbl
$nb_idr=count($tbl_idr);
$nb_ida=count($tbl_ida);
#$nb_idd=count($tbl_idd);


//
// affichage page
//

debut_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");
echo "<a name='haut_page'></a><br />";

gros_titre(_T('dw:titre_page_admin'));


debut_gauche();

	menu_administration_telech();
	menu_voir_fiche_telech();
	menu_config_sauve_telech();
	
	// module outils
	bloc_popup_outils();

	// module delocaliser
	bloc_ico_page(_T('dw:acc_dw2_dd'), generer_url_ecrire("dw2_deloc"), _DIR_IMG_DW2."deloc.gif");


creer_colonne_droite();

	// vers popup aide 
	bloc_ico_aide_ligne();

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";

debut_droite();

	//
	// onglets 		
	echo debut_onglet().
		onglet(_T('dw:rest_page_hierarchie'), generer_url_ecrire("dw2_restreint"), 'page_res', '', "racine-site-24.gif").
		onglet(_T('dw:rest_page_table'), generer_url_ecrire("dw2_restreint_etat"), 'page_resetat', 'page_resetat', _DIR_IMG_DW2."catalogue.gif").
	fin_onglet();
	echo "<br />";


// faire menage dans table acces_restreint : 
// .. supprimer les secteurs si tous '0'

	//racine reel $tbl_idrub_racine
	// racine en restreint '0' : $tbl_collect_racine
	$nbr_secteurs = count($tbl_idrub_racine);
	if(count(array_intersect($tbl_collect_racine, $tbl_idrub_racine)) == $nbr_secteurs) {
		$exp_tbl=implode(',',$tbl_idrub_racine);

		debut_cadre_relief("");
		echo _T('dw:rest_tous_secteur_0');
		echo "<br />";
		echo "<form action='".generer_url_action("dw2actions", "arg=menageracine-".$nbr_secteurs)."' method='post' class='arial2'>\n";
		echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_restreint_etat")."' />\n";
		echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-menageracine-".$nbr_secteurs)."' />";
		echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
		echo "<input type='hidden' name='tbl_racine' value='".$exp_tbl."' />";
			
		echo "<div class='bloc_bouton_r'><input type=submit value="._T('dw:validez')." class='fondo' /></div>\n";
		echo "</form>\n";
		fin_cadre_relief();
	}


debut_cadre_relief(_DIR_IMG_DW2."restreint-24.gif");


	echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>\n";
	echo "<tr><th colspan='3' class='verdana3'>"._T('dw:rest_etat_table_restrict')."</th></tr>";
	
	// affiche ligne rubrique
	if($nb_idr>0) {
		ksort($tbl_idr);
		foreach($tbl_idr as $k => $v) {
			if($v['parent']=='0') {
				$aff_icone = "<img src='"._DIR_IMG_PACK."secteur-12.gif' border='0' valign='absmiddle'>&nbsp;";
			}
			else {
				$aff_icone = "<img src='"._DIR_IMG_PACK."rubrique-12.gif' border='0' valign='absmiddle'>&nbsp;";
			}
			echo "<tr class='tr_liste verdana2'>\n";
			echo "<td width='7%'><div align='right'>".$k."</div></td>\n";
			echo "<td><a href='".
				generer_url_ecrire("dw2_restreint", "id_rub=".$k).
				"'>".
				$aff_icone.typo($v['titre']).
				"</a></td>\n";
			echo "<td width='7%'><div align='center'>".icone_niveau_restreint($v['res'])."</div></td>\n";
			echo "</tr>\n";
		}
	}
	// affiche ligne article
	if($nb_ida>0) {
		ksort($tbl_ida);
		foreach($tbl_ida as $k => $v) {
			echo "<tr class='tr_liste verdana2'>\n";
			echo "<td width='7%'><div align='right'>".$k."</div></td>\n";
			echo "<td><a href='".
				generer_url_ecrire("dw2_restreint", "id_art=".$k).
				"'>".
				"<img src='"._DIR_IMG_PACK."article-24.gif' border='0' width='12'  height='12' valign='absmiddle'>&nbsp;".
				typo($v['titre']).
				"</a></td>\n";
			echo "<td width='7%'><div align='center'>".icone_niveau_restreint($v['res'])."</div></td>\n";
			echo "</tr>\n";
		}
	}

	echo "</table>\n";

fin_cadre_relief();



//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec
?>
