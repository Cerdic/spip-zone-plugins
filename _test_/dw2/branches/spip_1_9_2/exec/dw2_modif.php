<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Corps de page : modification fiche
| Détails fiche du Document
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_modif() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');

//
//  requis
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");

include_spip('inc/session'); // cas suppression fiche
include_spip('inc/dw2_inc_hierarchie'); // hierarchie_doc()/dependance_restriction()


// reconstruire .. var=val des get et post
// var : 
// new_iddoctype ; new_doctype ; anc_doctype ; id ; archtout ; modif_ttr ; maj_size
// modif_fiche ; n_categorie ; n_nom ; n_total
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }



//
// prepa fiche
//
$query="SELECT *, dd.id_document AS id_dw, DATE_FORMAT(dd.date_crea,'%d/%m/%Y') AS datecrea, 
		sd.taille, sd.titre, sd.descriptif, sd.id_type, sd.id_vignette, sd.distant, 
		TO_DAYS(NOW()) - TO_DAYS(dd.date_crea) AS nbr_jour 
		FROM spip_dw2_doc dd LEFT JOIN spip_documents sd ON dd.id_document=sd.id_document 
		WHERE dd.id_document = $id";
$result=spip_query($query);
$okres=spip_num_rows($result);
if($okres==0)
	{
	// j'ai pas trouve mieux !
	echo "<script language=javascript>window.alert('"._T('dw:aucun_doc_num')."')</script>";
	echo "<script language=javascript>history.back()</script>";
	}
	
while ($row=spip_fetch_array($result))
	{
	$iddoc = $row['id_dw'];
	$doctype = $row['doctype'];
	$iddoctype = $row['id_doctype'];
	$nom = $row['nom'];
	$url = $row['url'];
	$total = $row['total'];
	$nomfichier = substr(strrchr($url,'/'), 1);
	$cheminfichier = str_replace($nomfichier, '', $url); // extrait repertoires de url
	$categorie = $row['categorie'];
	$datecrea = $row['datecrea'];
	$nbrjour = $row['nbr_jour'];
	$heberge = $row['heberge'];
	$id_serveur = $row['id_serveur'];
	$statut = $row['statut'];
	$tail_fich = $row['taille'];
	$titre_doc = $row['titre'];
	$desc_doc = $row['descriptif'];
	$idtype = $row['id_type'];
	$id_vignette = $row['id_vignette'];
	$distant = $row['distant'];

	// cesure ' ' sur nom/nomfichier trop long
	$nom_ces = wordwrap($nom,40,' ',1);
	$nomfichier_ces = wordwrap($nomfichier,40,' ',1);

	//
	// Fiche
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

	// colonne des catégories en affichage si case"modif"
	debut_boite_filet("b");
		echo "<span class='verdana2 bold'>"._T('dw:categories_exist')." :</span>";
			$ifond = 0;
		$qcat="SELECT categorie FROM spip_dw2_doc GROUP BY categorie";
		$rcat=spip_query($qcat);
		while ($licat=spip_fetch_array($rcat)) {
			$cat=$licat['categorie'];
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
			echo "<div class='arial2' style='background:$couleur; padding:3px;'>$cat</div>";
			}
	fin_bloc();
	echo "<br />";


	// vers popup aide 
	bloc_ico_aide_ligne();

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";


debut_droite();
	
	debut_cadre_relief(_DIR_IMG_DW2."fiche_doc.gif");

	// bouton stats_graph
	if($tail_fich) {
		bloc_bout_act('right');
		popup_stats_graph($iddoc,"<img src='"._DIR_IMG_PACK."statistiques-24.gif' border='0' align='absmiddle' title='"._T('dw:evolution_telech')."'>");
		fin_bloc();
	}
		
	// nom fiche
	echo "<div class='bloc_nom_fiche' style='color:".$couleur_foncee."; width:420px;'>".$nom_ces."</div>\n";
	echo "<div style='clear:both;'></div>\n";
	
	// Si archive	
	if($statut=='archive') {
		echo "<div class='verdana3'><b>"._T('dw:doc_dans_archive', array('nb_archive' => ''))."</b></div><br />\n";
	}
	
	
	//
	// Bouton changer le statut ...
	// fichier est plus dans SPIP
	if (!$tail_fich && $statut=='actif') {
		$chg_statut ="archive";
		$div_class = "suppr";
		$txt_bloc = "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif' valign='absmiddle' />&nbsp;\n".
					_T('dw:pas_dans_spip')." - "._T('dw:archiver_fiche')."\n";
	}
	// ou proposer forcer archive
	elseif($tail_fich && $statut=='actif') {
		$chg_statut = "archive";
		$div_class = "suppr";
		$txt_bloc = _T('dw:archiver_fiche')."&nbsp;";
	}
	// ou doc archive, retablir actif !
	elseif($tail_fich && $statut=='archive') {
		$chg_statut ="actif";
		$div_class = "enreg";
		$txt_bloc = _T('dw:retour_fiche_catalogue')."&nbsp;";
	}
	// ...
	if($chg_statut!='') {
		echo "<div class='boite_doc_".$div_class."'>";
		
		echo "<form action='".generer_url_action("dw2actions", "arg=changerstatut-".$chg_statut)."' method='post'>\n";
		echo "<input type='hidden' name='chg_statut_doc' value='".$iddoc."' />\n";
		echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_modif", "id=".$iddoc)."' />\n";
		echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-changerstatut-".$chg_statut)."' />\n";
		echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
		conten_bloc_bout('right','25');
			echo "<input type='image' src='"._DIR_IMG_DW2."ok_fich.gif' title='"._T('dw:valider')."' />\n";
		fin_bloc();
		echo $txt_bloc;		
		echo "</form>\n";
		
		echo "<div style='clear:both;'></div></div>\n";
	}
	
	
	//
	// formulaire de modif : nonm, catégorie, total
	//
	debut_cadre_enfonce("", false, "", _T('dw:modif_fich_trt'));
	if($statut=='archive') { echo "<div class='boite_doc_suppr'>"; }

	echo "<form action='".generer_url_action("dw2actions", "arg=modifierfiche-".$iddoc)."' method='post' class='arial2'>\n";
	echo "<table width='100%' cellspacing='0' cellpadding='2' border='0' align='center'>\n";
	echo "<tr><td colspan='3'><b>"._T('dw:nom_fiche')."</b></td></tr>\n";
	echo "<tr><td colspan='3'><input type='text' name='n_nom' value='".$nom."' size='40' maxlength='150' class='fondl' /></td></tr>\n";
	echo "<tr><td><b>"._T('dw:categorie')."</b></td>\n";
	echo "<td><b>"._T('dw:compteur')."</b></td>\n<td></td></tr>\n";
	echo "<tr><td><input type='text' name='n_categorie' value='".$categorie."' size='40' maxlength='150' class='fondl' /></td>\n";
	echo "<td><input type='text' name='n_total' value='".$total."' size='4' maxlength='21' class='fondl' /></td>\n";
	echo "<td width='80'><input type=submit value="._T('dw:modifier')." class='fondo' /></td>\n";
	echo "<tr class='verdana2'><td><i>".$categorie."</i></td><td><i>".$total."</i></td>\n";
	echo "<td></td>\n";
	echo "</tr></table>";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_modif", "id=".$iddoc)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-modifierfiche-".$iddoc)."' />";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
	echo "</form>\n";

	if($statut=='archive') { echo "</div>"; }
	fin_cadre_enfonce();
	
	//
	// formulaire telechargement-restreint #h.01/01
	//
	#h.09/03 si pas $tail_fich pas besoin de restriction
	if($GLOBALS['dw2_param']['mode_restreint']=='oui' && $tail_fich) {
	
		$hierarchie = hierarchie_doc($iddoc);
		$type='document';

		// releve la dependance du parent directement superieur
		$restrict = dependance_restriction($iddoc, $type, $hierarchie);
		$niveau_p = $restrict[0];
		$maitre_p = $restrict[1];
		$id_maitre_p = $restrict[2];
		$titre_maitre_p = titre_maitre_dependance($maitre_p,$id_maitre_p);
		
		// releve le niveau de restriction de l'objet / ou dependance directe
		$restrict_objet = dependance_restriction($iddoc, $type, $hierarchie, true);
		$niveau_objet = $restrict_objet[0];
		
	
		// formulaire "restriction"
		debut_cadre_enfonce(_DIR_IMG_DW2."restreint-24.gif", false, "", _T('dw:rest_titre_formulaire'));
		
			//commentaire dependance
			debut_cadre_relief("", true, "","");
			 echo "<b>"._T('dw:rest_dependance_direct_sup')."</b><br />";
			 if($maitre_p) {
				echo _T('dw:rest_dependance_detail', array('maitre_p' => $maitre_p, 'titre_maitre_p'=>$titre_maitre_p)).
						_T('dw:restreint_val_'.$niveau_p);
			} else {
				echo _T('dw:rest_dependance_aucune');
			}
			fin_cadre_relief();		
			
			
			echo "<form action='".generer_url_action("dw2actions", "arg=restrictgen-".$iddoc)."' method='post' class='arial2'>\n";
			echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_modif", "id=".$iddoc)."' />\n";
			echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-restrictgen-".$iddoc)."' />";
			echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
			echo "<input type='hidden' name='type' value='document' />";
		
			// selecteur
			selecteur_restreindre($niveau_objet);
			
			echo "</form>\n";
		
		fin_cadre_enfonce();
	
	}// fin restriction
	
	fin_cadre_relief();
	
	
	
	//
	// info document
	//
	debut_cadre_relief("doc-24.gif");

	if($statut=='archive') { echo "<div class='boite_doc_suppr'>"; }
	
	conten_bloc_bout();
	// bouton téléchargement, icone par defaut ou vignette
	$id_image = ($id_vignette=='0') ? '0' : $id_vignette;

	if($tail_fich) { 
		if($heberge=='distant') { $chem_telech = $url; }
		else if ($heberge=='local') { $chem_telech = "..".$url; }
		else { $chem_telech = $heberge.$url; }
		bloc_minibout_act(_T('dw:telech_fichier'), "$chem_telech", "", $idtype, $id_image);
	}
	fin_bloc();


	// ligne nom fichier
	echo "<span class='verdana3'>"._T('dw:fichier')." : ".origine_heberge($heberge)." <b>".$nomfichier_ces."</b></span><br />";

	
	// lignes aff. taille du fichier / controle presence et nouv. taille 
	echo "<p class='verdana3'>";
	if ($tail_fich) { 	echo _T('dw:taille')." : <b>".taille_octets($tail_fich)."</b><br />"; }
	else { 	echo _T('dw:pas_dans_spip')."<br />"; }
	
	if($majsize=='oui') {
		$sizedoc = controle_size_doc($iddoc,$url,$id_serveur,$heberge,$tail_fich);

		echo "<span style='color:#DF0000;'><i>";
		if($sizedoc[0]=='0') {
			echo "&nbsp;<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif' valign='absmiddle'>&nbsp;";
			echo "<b>"._T('dw:echec_acces_fichier')."</b>";
		}
		elseif($sizedoc[0]==$sizedoc[1]) {
			echo "<br />"._T('dw:taille')." OK !";
		}
		else {
			echo "<br />"._T('dw:taille_fichier_avant').taille_en_octets($sizedoc[1]);
		}
		echo "</i></span>";
	}
	echo "</p>";
	
	
	echo "<div style='clear:both;'> </div>\n";


	conten_bloc_bout();
	// bouton verif taille ... // h.15/3 : ?? -> AND $distant!='oui'
		if($tail_fich) {
			$lien_bout = generer_url_ecrire("dw2_modif", "id=".$iddoc."&majsize=oui");
			bloc_minibout_act(_T('dw:maj_taille_fichier'), $lien_bout, _DIR_IMG_DW2."taille_fichier.gif","","");
		}
	fin_bloc();


	// def. doc spip, origine		
	echo "<div class='verdana3'>";
	echo _T('dw:enreg_dans_cat_et_nbr_jours', array('datecrea' => $datecrea,'nbrjour' => $nbrjour))."<br />\n";
	echo _T('dw:chemin')." : ";
	if($id_serveur>='1')
		{ echo " <b>".$heberge."</b>"; }
	echo $cheminfichier."<br /><br />\n";
	echo _T('dw:doc_spip_n')." ".$iddoc.",<br />\n";
	echo aff_appart_doc($doctype, $iddoctype);	
	echo "</div>\n\n";
	echo "<div style='clear:both;'> </div>\n";
	
	
	if($statut=='archive') { echo "</div>"; }


	// formulaire modif : Titre et Desc' du doc
	if ($tail_fich && $statut=='actif')
		{
		debut_cadre_enfonce("rien.gif", false, "", _T('dw:champs_modif')._T('dw:doc_lie_trt_descrip'));
		form_titre_desc($id, $titre_doc, $desc_doc, generer_url_ecrire("dw2_modif", "id=".$id));
		fin_cadre_enfonce();
		}


	//
	// réaffecter un Doc vers autre article/rubrique
	//
	$invisible = $iddoc;
	if($tail_fich && $statut=='actif')
		{		
		debut_cadre_enfonce("article-24.gif", false, "", _T('dw:deplace_doc'));
		
		// avertissement
		debut_boite_filet("a");
		if ($invisible)
			echo bouton_block_invisible('alert');
		else 
			echo bouton_block_visible('mess_alert');
		echo "&nbsp;<span class='verdana2'><b>[ "._T('dw:attention_info')." ]</b></span>";
		
		if ($invisible)
			echo debut_block_invisible('alert');
		else
			echo debut_block_visible('mess_alert');
		
		echo "<span class='verdana2'>"._T('dw:txt_deplace_doc')."</span><br />";
		echo fin_block();
		fin_bloc();
		
		// formulaire	
		echo "<div class='boite_filet_c center'>";

		echo "<form action='".generer_url_action("dw2actions", "arg=deplacerdocument-".$iddoc)."' method='post'>\n";
		echo _T('dw:destination_doc')."&nbsp;";
		echo "<input type='radio' name='new_doctype' value='article' checked='checked'> "._T('dw:article')."&nbsp;&nbsp;&nbsp;".
			"<input type='radio' name='new_doctype' value='rubrique' > "._T('dw:rubrique')." - "._T('dw:num_dblpt');
		echo "<input type='text' name='new_iddoctype' size='4' maxlength='21' onClick=\"setvisibility('valider_iddoctype','visible');\" class='fondf'>";
		echo "<input type='hidden' name='anc_doctype' value='".$doctype."'>\n";
		echo "<span  class='visible_au_chargement' id='valider_iddoctype'>";
		echo "<input type='submit' value="._T('dw:modifier')." class='fondo'>\n";
		echo "</span>";
		echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("dw2_modif", "id=".$iddoc)."' />\n";
		echo "<input type='hidden' name='hash' value='".calculer_action_auteur("dw2actions-deplacerdocument-".$iddoc)."' />";
		echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />";
		echo "</form>";

		echo "</div>";

		fin_cadre_enfonce();
		}

	fin_cadre_relief();	

	
	//
	// Effacer fiche du catalogue
	//
	if(!$tail_fich || $idtype<=3)
		{
		debut_cadre_relief("");		
		if ($invisible)
			echo bouton_block_invisible('efface', "warning-24.gif");
		else 
			echo bouton_block_visible('efface');
		echo "<span class='verdana3'><b>&nbsp;&nbsp;"._T('dw:efface_fiche')." </b></span>";
		
		if ($invisible)
			echo debut_block_invisible('efface');
		else
			echo debut_block_visible('efface');

		echo "<div class='boite_filet_c center verdana3'>";
		echo "<a href='".redirige_action_auteur('dw2actions', "supprimefiche-$iddoc", 'dw2_catalogue')
			."' title='"._T('dw:efface_fiche')."'>";
		echo "<img src='"._DIR_IMG_PACK."poubelle.gif' border='0' align='absmiddle'>&nbsp; "._T('dw:effacer')." $nom_ces "._T('dw:du_catalogue')." &nbsp;<img src='"._DIR_IMG_PACK."poubelle.gif' border='0' align='absmiddle'>";
		echo "</a>";

		echo "</div>";
		
		echo fin_block();
		fin_cadre_relief();
		}
	}


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
