<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| fichier d'appel des principales fonctionnalités
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_admin() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');

//
// function requises ...
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");

include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");
include_spip("inc/dw2_inc_ajouts");

// config
$mode_enregistre_doc = $GLOBALS['dw2_param']['mode_enregistre_doc'];
$jours_affiche_nouv = $GLOBALS['dw2_param']['jours_affiche_nouv'];
$nbr_lignes_tableau = $GLOBALS['dw2_param']['nbr_lignes_tableau'];


//
// affichage
//

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");
echo "<a name='haut_page'></a><br />";

echo gros_titre(_T('dw:titre_page_admin'),'','',true);


echo debut_gauche('',true);

	// les fonctions principales de dw2 -> pages
	menu_administration_telech();

	// atteindre fiche du doc 'n' ..
	menu_voir_fiche_telech();

	// configuration & sauvegarde 
	menu_config_sauve_telech();

	// module outils
	bloc_popup_outils();

	// module delocaliser
	bloc_ico_page(_T('dw:acc_dw2_dd'), generer_url_ecrire("dw2_deloc"), _DIR_IMG_DW2."deloc.gif");


echo creer_colonne_droite('',true);

	// controler MaJ du plugin sur serveur 
	// via action cron 1 /2 jrs
	if($GLOBALS['dw2_param']['avis_maj']=='oui' && $GLOBALS['dw2_param']['message_maj']!=0) {
		$maj = unserialize($GLOBALS['dw2_param']['message_maj']);
		echo debut_cadre_trait_couleur(_DIR_IMG_PACK."warning-24.gif", true, "", _T('dw:maj_evolution_dw'));
			echo "<ul class='avis_maj'>";
			echo "<li>".typo($maj['nom'])." "._T('dw:maj_version')." <b>".$maj['version']."</b></li>";
			echo "<li>"._T('dw:maj_etat')." <b>".$maj['etat']."</b></li>";
			echo "<li>".propre($maj['description'])."</li>";
			echo "<li>".propre($maj['lien'])."</li>";
			echo "</ul>";
		echo fin_cadre_trait_couleur(true);
		
	}
	
	// vers popup aide 
	echo "<br />";
	bloc_ico_aide_ligne();

	// signature
	echo "<br />";
	echo debut_boite_info(true);
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	echo fin_boite_info(true);
	echo "<br />";


echo debut_droite('',true);


	// Affichage : Nombre de Doc gérés par DW2 (actifs et archives)
	$result=sql_query("SELECT COUNT(*) as nb_glob FROM spip_dw2_doc");
	$row=sql_fetch($result);
	$nb_glob=$row['nb_glob'];
	
	$result=sql_query("SELECT id_document FROM spip_dw2_doc WHERE statut='actif'");
	$nb_actif=sql_count($result);
	$nb_archive=$nb_glob-$nb_actif;

echo debut_cadre_relief(_DIR_IMG_DW2."catalogue.gif",true);

	echo "<br /><div class='center verdana3'>"._T('dw:actuellement');
		if($nb_actif<=1)
			{ echo _T('dw:doc_dans_cat', array('nb_actif' => $nb_actif))."<br />\n"; }
		else 
			{ echo _T('dw:doc_dans_cat_s', array('nb_actif' => $nb_actif))."<br />\n"; }
		
	echo "( + ";
		if($nb_actif<=1)
			{ echo _T('dw:doc_dans_archive', array('nb_archive' => $nb_archive))." )</div><br />\n"; }
		else 
			{ echo _T('dw:doc_dans_archive_s', array('nb_archive' => $nb_archive))." )</div><br />\n"; }
		

echo fin_cadre_relief(true);


	//
	// Alerte pour Documents supprimés de spip_documents
	//
	$result2=sql_select("dw.id_document, dw.nom, dw.doctype, dw.id_doctype ",
						"FROM spip_dw2_doc dw LEFT JOIN spip_documents sd ON dw.id_document = sd.id_document ",
						"sd.id_document IS NULL AND dw.statut='actif'");
	
	
	if (sql_count($result2)) {
		echo debut_cadre_trait_couleur(_DIR_IMG_PACK."warning-24.gif", true, "", _T('dw:doc_pas_dans_spip'));
		
		$num_arch = array();
		while ($row2=sql_fetch($result2)) {
			$nom = $row2['nom'];
			// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 40 caract
			$nom = wordwrap($nom,40,' ',1);
			
			echo "<div class='boite_doc_suppr verdana3'>\n".
				"<b>".$nom."</b>&nbsp;\n".
				"<img src='"._DIR_IMG_DW2."fiche_doc-15.gif' style='border:0;vertical-align: middle;' title='"._T('dw:voir_fiche')."'>\n".
				"<a href='".generer_url_ecrire("dw2_modif", "id=".$row2['id_document'])."'>\n".
				"</a></div>\n";
			// faire tableau des Docs Non Spip
			$num_arch[]=$row2['id_document'];
		}
				
		reset($num_arch);
		$num_arch = implode(',',$num_arch);
		$chg_statut = "archive";
		
		// bouton tout archiver
		echo "<div class='bloc_bouton_r'>";
		echo "<form action='".generer_url_action("dw2actions", "arg=changerstatut-".$chg_statut)."' method='post'>\n";
		echo "<input type='hidden' name='num_arch' value='".$num_arch."' />";
		echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_admin")."' />\n";
		echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-changerstatut-".$chg_statut)."' />";
		echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
		echo "<input type='submit' value='"._T('dw:archiver_tout')."' class='fondo'>";
		echo "</form>";
		echo "</div>";

		echo fin_cadre_trait_couleur(true);
	}


	//
	// alerte pour Documents non inscrits au Catalogue
	// OU
	// si mode_enregistre_doc = auto : liste des docs enreg. depuis 'n' jours
	//

	$where="";
	$order="";
	if ($mode_enregistre_doc=='manuel')
		{	
			$where = " AND sd.extension NOT IN ('png', 'jpg', gif') AND dw.id_document IS NULL";
			$order = "titre";
		//$query3.="AND sd.id_type > '3' AND dw.id_document IS NULL ORDER BY titre"; 
		}
	if ($mode_enregistre_doc=='auto')
		{ 
		//$query3.="AND dw.date_crea >= DATE_SUB(NOW(),INTERVAL $jours_affiche_nouv DAY) ORDER BY date_crea DESC"; 
			$where = " AND dw.date_crea >= DATE_SUB(NOW(),INTERVAL $jours_affiche_nouv DAY)";
			$order = "date_crea DESC";
		}
	$result3=sql_select("sd.id_document, sd.titre, sd.fichier" ,
					"spip_documents sd LEFT JOIN spip_dw2_doc dw ON sd.id_document = dw.id_document ",
					"sd.mode = 'document' $where",
					"", // group by
					$order);
	
	
	// potentielement y'a des Docs
	if (sql_count($result3)) {
		$prep_dispo=array();
		
		// le doc est-il enregistrable (origine = 'publie')
		while ($row3=sql_fetch($result3)) {
			$iddoc = $row3['id_document'];
			$origine=origine_doc($iddoc);
			$nomfichier = substr(strrchr($row3['fichier'],'/'), 1);
			// si en statut 'publie' OK .. on enregistre
			if($origine[2]=='1') {
				// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 40 caract
				$nomfichier = wordwrap($nomfichier,40,' ',1);
			
				$prep_dispo[$iddoc]['nomfichier'] = $nomfichier;
				$prep_dispo[$iddoc]['doctype'] = $origine[0];
				$prep_dispo[$iddoc]['iddoctype'] = $origine[1];
			}
		}
		$nb_dispo=count($prep_dispo);
		
		// affichage des doc enregistrables
		if($nb_dispo>0) {
			if ($mode_enregistre_doc=='manuel')
				{ $ttr_bloc = _T('dw:doc_pas_dans_cat'); }
			if ($mode_enregistre_doc=='auto')
				{ $ttr_bloc = _T('dw:doc_dans_cat_depuis', array('jours_affiche_nouv' => $jours_affiche_nouv)); }	

			echo debut_cadre_trait_couleur(_DIR_IMG_DW2."ajout_doc.gif", true, "", $ttr_bloc);
			
			$i=0;
			foreach($prep_dispo as $k => $v) {
				echo "<div class='boite_doc_enreg verdana3'>";
				
				if ($mode_enregistre_doc=='auto')
					{ echo "<a href='".generer_url_ecrire("dw2_modif", "id=".$k)."' title='"._T('dw:voir_fiche')."'>"; }

				echo "<img src='"._DIR_IMG_DW2."fiche_doc-15.gif' border='0' align='absmiddle'>&nbsp;";
				echo "<span class='verdana2'>".$v['doctype']." ".$v['iddoctype']." &middot;&middot; </span> ";
				echo "<b>".$v['nomfichier']."</b>\n";
				
				if ($mode_enregistre_doc=='auto') { echo "</a>"; }
				
				echo "</div>";
				$i++;
				if($i==$nbr_lignes_tableau) break;	// affichage limite à $nbr_lignes_tableau lignes
			}
			
			// si plus de ligne, on le signale !
			if($nb_dispo>$nbr_lignes_tableau) {
				echo "<div class='bloc_bouton_r verdana3'>";
				echo "+ "._T('dw:total_doc_pour_ajout', array('total' => $nb_dispo, 'nbr_lignes_tableau' => $nbr_lignes_tableau))." +";
				echo "</div>";
			}
			
			// si en "manuel", on propose le bouton 'ajout'
			if ($mode_enregistre_doc=='manuel') {
				echo "<div class='bloc_bouton_r'>";
				echo "<form action='".generer_url_ecrire("dw2_ajouts")."' method='post'>";
				echo "<input type='submit' value='"._T('dw:ajouter')."' class='fondo'>";
				echo "</form>";
				echo "</div>";
			}
						
			echo fin_cadre_trait_couleur(true);
		}
	}


	//
	// telechargements du jour ...
	//
		//  recup' nombre de ligne et son retour, fixe debut LIMIT ...		
		$dl=($_GET['vl']+0);
		
		// Verif. : telech aujourd'hui ?	
		$rvtel=sql_query("SELECT id_doc, telech FROM spip_dw2_stats WHERE TO_DAYS(date)=TO_DAYS(NOW())");
		$nligne=sql_count($rvtel);
		
	//tableau
	$result4=sql_select("ds.id_doc, ds.date, ds.telech, dd.url, dd.nom, dd.total ",
						"spip_dw2_stats ds LEFT JOIN spip_dw2_doc dd ON ds.id_doc=dd.id_document ",
						"TO_DAYS(date)=TO_DAYS(NOW())",
						"", // group by
						"ds.telech DESC", // order by
						"$dl,$nbr_lignes_tableau"); // limit
		
	if ($nligne==0)
		{
		echo debut_cadre_relief(_DIR_IMG_PACK."statistiques-24.gif",true);
		echo "<br /><b>"._T('dw:aucun_telech_moment')."</b><br /><br />\n";
		echo fin_cadre_relief(true);
		}
	else
		{
		// total des telech du jour !
		$add_telech = array();
		while ($l_rvtel=sql_fetch($rvtel))
			{
			$telech = $l_rvtel['telech'];
			$add_telech[]=$telech;
			}
		reset($add_telech);
		
		// initialise tranche
		$nba1 = $dl+1;
		
		// nbre de telechargement
		$tt_telech_j = array_sum($add_telech);

		echo debut_cadre_relief(_DIR_IMG_PACK."statistiques-24.gif",true);
		
		echo debut_boite_filet("a", "center");
			echo "<b>"._T('dw:telech_du_jour_nombre', array('nbr_tt'=>$tt_telech_j))."</b>\n";
		echo fin_bloc();
		debut_band_titre('#dfdfdf');
			tranches($nba1, $nligne, $nbr_lignes_tableau);
		echo fin_bloc();
		
		// tableau
		echo "<table align='center' cellpadding='2' cellspacing='1' border='0' width='100%'>\n".
				"<tr bgcolor='$couleur_foncee'>\n".
				"<td><span class='arial2' style='color:#FFFFFF;'>"._T('dw:nom_fiche')."</span></td>\n".
				"<td><span class='arial2' style='color:#FFFFFF;'>"._T('dw:fichier')."</span></td>\n".
				"<td>&nbsp;</td>\n".
				"<td><div class='arial2' style='color:#FFFFFF; text-align:center;'>TT</div></td>\n".
				"</tr>";

		$ifond = 0;

		while ($row4=sql_fetch($result4))
			{
			$iddoc = $row4['id_doc'];
			$nomfichier = substr(strrchr($row4['url'],'/'), 1);
			$telech = $row4['telech'];
			$nom = $row4['nom'];
			$total = $row4['total'];
			$ifond = $ifond ^ 1;
			$bgcolor = ($ifond) ? '#FFFFFF' : $couleur_claire;
			
			// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 25 caract
			$nom = wordwrap($nom,25,' ',1);
			$nomfichier = wordwrap($nomfichier,25,' ',1);

			
			echo "<tr bgcolor='$bgcolor'>";
			echo "<td width='40%'><span class='arial2'>".
				"<a href='".generer_url_ecrire("dw2_modif", "id=".$iddoc)."' title='"._T('dw:la_fiche')."'>".$nom."</a>".
				"</span></td>";
			echo "<td width='42%'><div class='verdana2'>".$nomfichier."</div></td>";
			echo "<td width='8%'><div align='center' class='arial2'><b>".$telech."</b></div></td>";
			echo "<td width='10%'><div align='center' class='verdana2'>".$total."</div></td>";
			echo "</tr>";
			}
		echo "</table>";
		
		echo fin_cadre_relief(true);
		}

//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	echo fin_gauche().fin_page();
} // fin exec_
?>