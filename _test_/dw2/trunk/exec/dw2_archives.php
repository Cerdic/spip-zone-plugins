<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Catalogue des doc mis en archives
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_dw2_archives() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');

//
// requis dw
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");

// config dw
$nbr_lignes_tableau=$GLOBALS['dw2_param']['nbr_lignes_tableau'];



//
// prepa
//

// reconstruire .. var=val des get et post
// var : vl ; odb ; wltt
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//  recup' nombre de ligne passe en url, fixe debut LIMIT ...		
$dl=($vl+0);


//Nbr Total de Doc ...(Docs actifs
$rcc_nligne=spip_query("SELECT nom, id_document FROM spip_dw2_doc WHERE statut='archive' ORDER BY nom");
$nligne=spip_num_rows($rcc_nligne);
	
	
// prepa toutdeplier toutreplier + tableau des prem lettres
$gen_ltt = array();
while ($row_dep=spip_fetch_array($rcc_nligne)) {
	$iddoc=$row_dep['id_document'];
	$les_docs[] = "bout$iddoc";
	$nom_block = "bout$iddoc";
	if (!$numero_block["$nom_block"] > 0) {
		$compteur_block++;
		$numero_block["$nom_block"] = $compteur_block;
			if (!$first_couche) 
			{ $first_couche = $compteur_block; }
			{ $last_couche = $compteur_block; }
	}
	// tableau de toutes premieres lettres
	$gen_ltt[] = strtoupper(substr($row_dep['nom'],0,1));
}
	
// gen_ltt => elimine doublons => tbl_ltt
reset ($gen_ltt);
$nbr_ltt=0;
while (list(,$ltt)=each($gen_ltt)) {
	if($ltt != $ltt_prec)
		{ $tbl_ltt[$ltt] = 1; }
	else
		{ $tbl_ltt[$ltt]++; }
	$ltt_prec = $ltt;
}

		
// Tri tableau : par Catégorie, heberge, date_crea ou par nom fiche
if ($odb=='cat'){ $orderby = 'categorie';}
else if ($odb=='heb') { $orderby = 'heberge'; }
else if ($odb=='date') { $orderby = 'd.date_crea DESC'; }
else { $orderby = 'd.nom'; $odb='nom'; }


// si tri alphabet'
if (isset($wltt)) {
	$where_ltt = "AND UPPER(d.nom) LIKE '$wltt%'";
	// on redéfinis $nligne pour la function tranche
	reset($tbl_ltt);
	$nligne = $tbl_ltt[$wltt];
}


// requete principale du catalogue
$quer="SELECT d.id_document, DATE_FORMAT(d.dateur,'%d/%m/%Y - %H:%i') AS datetel, 
		DATE_FORMAT(d.date_crea,'%d/%m/%Y') AS datecrea, 
		d.nom, d.url, d.total, d.doctype, d.id_doctype, d.categorie, d.heberge, d.id_serveur, 
		TO_DAYS(d.dateur) - TO_DAYS(d.date_crea) AS nbr_jour, 
		ROUND(d.total/(TO_DAYS(d.dateur) - TO_DAYS(d.date_crea)),2) AS moyj, 
		s.taille, s.id_type, s.distant 
		FROM spip_dw2_doc AS d 
		LEFT JOIN spip_documents AS s ON d.id_document=s.id_document 
		WHERE d.statut='archive' $where_ltt 
		ORDER BY $orderby LIMIT $dl,$nbr_lignes_tableau";
		
$result=spip_query($quer);
$nbliens=spip_num_rows($result);



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


debut_cadre_relief(_DIR_IMG_DW2."catalogue.gif");

if ($nbliens==0) {

		echo "<br /><b>"._T('dw:aucun_doc_archive')."<br /><br />";

} else {
	// valeur de tranche affichee	
	$nba1 = $dl+1;
	
	// affichage titre
	$coul_fond = "#E8C8C8";

	
	debut_band_titre($coul_fond, "verdana3", "bold");
	if(isset($wltt))
		{ echo "[ ".$wltt."... ]"; }
		
	echo _T('dw:doc_pas_dans_spip')."<br />\n";
	if($nligne <=1)
		{ _T('dw:doc_dans_archive', array('nb_archive' => $nligne))."\n"; }
	else 
		{ _T('dw:doc_dans_archive_s', array('nb_archive' => $nligne))."\n"; }

	fin_bloc();
	

	// toutdeplier/toutreplier block_invi
	$javasc_ouvrir = "manipuler_couches('ouvrir','$spip_lang_rtl',$first_couche,$last_couche, '" . _DIR_IMG_PACK . "')";
	$javasc_fermer = "manipuler_couches('fermer','$spip_lang_rtl',$first_couche,$last_couche, '" . _DIR_IMG_PACK . "')";
	if ($les_docs)
		{
		$les_docs = join($les_docs,",");
		echo "<div style='float:left; padding:2px;'>";
		echo "<b class='verdana2'>";
		echo "<a href=\"javascript:$javasc_ouvrir\">";
		echo _T('lien_tout_deplier');
		echo "</a>";
		echo "</b></div>";
		echo "<div style='float:right; padding:2px;'>";
		echo "<b class='verdana2'>";
		echo "<a href=\"javascript:$javasc_fermer\">";
		echo _T('lien_tout_replier');
		echo "</a>";
		echo "</b></div>\n";
		echo "<div style='clear:both'></div>\n";
		}


	// affichage tranches
	debut_band_titre("#dfdfdf");
	tranches($nba1, $nligne, $nbr_lignes_tableau);
	fin_bloc();


	// affichage lettres pour tri-alphabetique
	bouton_tout_catalogue($page_affiche);
	reset ($tbl_ltt);
	while (list($k,$v) = each($tbl_ltt))
		{
		echo "<a href='".generer_url_ecrire("dw2_archives","wltt=".$k)."' title='"._T('dw:document_s')." : $v'>\n";
		echo bouton_alpha($k);
		echo "</a>\n";
		}
	echo "<div style='clear:both;'></div>\n";	
	// 
	
	$ifond = 0;
	
	// Entete tableau ..
	echo "<table border='0' cellpadding='2' cellspacing='0' width='100%'>\n	
		<tr><td width='8%' class='tete_colonne'>\n";
	if($odb!='heb') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','heb');
		echo "<a href='".$lien."'><img src='"._DIR_IMG_DW2."dot_serveur.gif' border='0' align='absmiddle'></a>";

	} else {
		echo "<img src='"._DIR_IMG_DW2."dot_serveur.gif' border='0' align='absmiddle'>";
	}	
	echo "</td><td width='50%' class='tete_colonne'>\n";
	if($odb!='nom') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','nom');
		echo "<a href='".$lien."'>"._T('dw:nom_fiche')."</a>";
	} else {
		echo "<b>"._T('dw:nom_fiche')."</b>";
	}
	echo "</td><td width='26%' class='tete_colonne'>\n";
	if($odb!='cat') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','cat');
		echo "<a href='".$lien."'>"._T('dw:categorie')."</a>";
	} else {
		echo "<b>"._T('dw:categorie')."</b>";
	}
	echo "</td><td width='16%' class='tete_colonne'>\n";
	if($odb!='date') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','date');
		echo "<a href='".$lien."'>"._T('dw:entree_cat')."</a>";
	} else {
		echo "<b>"._T('dw:entree_cat')."</b>";
	}
	echo "</td></tr>";


	while ($a_row=spip_fetch_array($result))
		{
		$ifond = $ifond ^ 1;
		$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
		
		$iddoc = $a_row['id_document'];
		$nom = $a_row['nom'];
		$url = $a_row['url'];
		$total = $a_row['total'];
		$nomfichier = substr(strrchr($url,'/'), 1);
		$cheminfichier = str_replace($nomfichier, '', $url); // extrait repertoires de url
		$datetel = $a_row['datetel'];
		$doctype = $a_row['doctype'];
		$iddoctype = $a_row['id_doctype'];
		$categorie = $a_row['categorie'];
		$datecrea = $a_row['datecrea'];
		$heberge = $a_row['heberge'];
		$id_serveur = $a_row['id_serveur'];
		$nbrjour=$a_row['nbr_jour'];
		$moyj=$a_row['moyj'];
		$statut=$a_row['statut'];
		$idtype=$a_row['id_type'];
		$t_s=$a_row['taille'];
		$distant=$a_row['distant'];
		
		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 30 caract
		$nom = wordwrap($nom,30,' ',1);
		$nomfichier = wordwrap($nomfichier,30,' ',1);
		
		if (!$t_s)
			{ $taille = "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif'>&nbsp;"._T('dw:pas_dans_spip'); }
		else
			{ $taille = taille_en_octets($t_s); }


		// ligne du tableau
		//
		$bouton = bouton_block_invisible("bout$iddoc");
		echo "<tr bgcolor='$couleur'>";
		echo "<td width=8%>$bouton ".origine_heberge($heberge)."</td>\n";
		echo "<td width=50%><div class='verdana2'>";
		if (!$t_s)
			{ echo "<img src='"._DIR_IMG_DW2."puce-rouge-breve.gif'>"; }
		echo "&nbsp;".$nom."</div></td>\n";
		echo "<td width=26%><div align='center' class='verdana2'>".$categorie."</div></td>\n";
		echo "<td width=16%><div align='center' class='arial2'>".$datecrea."</div></td>\n";
    	echo "</tr>\n";
		
		// Déroulant : fiche du Lien
		echo "<tr bgcolor='$couleur'><td colspan='4'><span class='verdana1'>";
		echo debut_block_invisible("bout$iddoc");
		
		conten_bloc_bout();
			//
			// bouton "modifier"
			bloc_minibout_act(_T('dw:modifier'), generer_url_ecrire("dw2_modif", "id=".$iddoc), _DIR_IMG_DW2."fiche_doc.gif","","");
			//
			// bouton exporter .. abandonne v.2.11// h.22/8		
			#if ($t_s && $id_serveur=='0' && $distant!='oui')
			#	{ bloc_minibout_act(_T('dw:exporter'), generer_url_ecrire("dw2_deloc", "id_document=".$iddoc), _DIR_IMG_DW2."export-24.gif","",""); }
			//
			// bouton telechargement non incrementé !
			if($t_s) {
				if($heberge=='local')
					{ $chem_telech = "..".$url; }
				else if ($heberge=='distant')
					{ $chem_telech = $url; }
				else
					{ $chem_telech = $heberge.$url; }
				bloc_minibout_act(_T('dw:telech_fichier'), "$chem_telech", "", $idtype,"0");
			}
			//
		fin_bloc();

		//details Document
		echo _T('dw:fichier')." : <b>".$nomfichier."</b><br />\n";
		echo _T('dw:taille')." : <b>".$taille."</b><br />\n";
		echo _T('dw:doc_spip_n')." ".$iddoc."<br />\n";
		echo aff_appart_doc($doctype, $iddoctype);
		echo _T('dw:enreg_dans_cat_et_nbr_jours', array('datecrea' => $datecrea,'nbrjour' => $nbrjour))."<br />\n";
		echo _T('dw:dernier_telech')." : ".$datetel." <b>::</b> "._T('dw:moyenne_jour')." : <span class='verdana2'>".$moyj."</span><br />\n";
		echo _T('dw:compteur')." : <span class='verdana2'><b>".$total."</b></span><br />\n";
		echo _T('dw:chemin')." : ";
		if($id_serveur>='1') { echo "<b>".$heberge."</b>"; }
		echo $cheminfichier."<br />\n";
		echo fin_block();		
		echo "</span></td></tr>\n";
		}
	echo "</table>\n";
}
fin_cadre_relief();


//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
