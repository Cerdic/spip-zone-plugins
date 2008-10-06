<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| fonction outils :
| Modifier titre & descriptif document
+--------------------------------------------+
*/



function titredesc() {

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;
	

	// config
	$nbr_lignes_tableau = $GLOBALS['dw2_param']['nbr_lignes_tableau'];
	
	
	// reconstruire .. var=val des get et post
	// var : vl ; odb ; 
	// modif_ttr ; $id_document ; titre_document ; descriptif_document
	// .. Option .. utiliser : $var = _request($var);
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


	
//
// ...
//

//  recup' nombre de ligne et son retour, fixe LIMIT ...		
	$dl=($vl+0);

//Nbr Total de Doc ...(Docs actifs
$rcc_nligne=spip_query("SELECT id_document FROM spip_dw2_doc WHERE statut='actif'");
$nligne=spip_num_rows($rcc_nligne);

// prepa toutdeplier toutreplier + tableau des prem lettres
	while ($row_dep=spip_fetch_array($rcc_nligne))
		{
		$iddoc=$row_dep['id_document'];
		$les_docs[] = "bout$iddoc";
		$nom_block = "bout$iddoc";
		if (!$numero_block["$nom_block"] > 0)
			{
			$compteur_block++;
			$numero_block["$nom_block"] = $compteur_block;

			if (!$first_couche) 
				{ $first_couche = $compteur_block; }
				{ $last_couche = $compteur_block; }
			}
		}

		// Tri du tableau : 
		if ($odb=='ttr') { $orderby = 's.titre'; }
		else if ($odb=='fich') { $orderby = 'fichier'; }
		else if ($odb=='desc') { $orderby = 's.descriptif'; }
		else if ($odb=='id_typ') { $orderby = 'd.id_doctype'; }
		else { $orderby = 'd.date_crea DESC'; $odb='date'; }

$quer="SELECT d.id_document, ".
		"DATE_FORMAT(d.date_crea,'%d/%m/%Y') AS datecrea, ".
		"d.url, d.doctype, d.id_doctype, ".
		"d.categorie, SUBSTRING_INDEX(url, '/', -1) AS fichier, ".
		"s.titre, s.descriptif ".
		"FROM spip_dw2_doc AS d LEFT JOIN spip_documents AS s ON d.id_document=s.id_document ".
		"WHERE d.statut='actif' ".
		"ORDER BY $orderby LIMIT $dl,$nbr_lignes_tableau";
$result=spip_query($quer);
$nbliens=spip_num_rows($result);


//
// ...
//

debut_cadre_trait_couleur("doc-24.gif", false, "", _T('dw:modif_titre_descriptif'));

if ($nbliens==0)
	{
	echo "<br /><span class='arial2'><b>"._T('dw:txt_cat_aucun')."<br />";
	echo "<br /><br />"._T('dw:ajout_doc')."</b></span><br />";
	}
else
	{
	// valeur de tranche affichée	
	$nba1 = $dl+1;
	#$nba2 = $dl+$nbliens;
	
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
	
	// debut affichage
	debut_band_titre("#DFDFDF");
	echo "<div align='center' class='verdana2'>\n";
	tranches($nba1, $nligne, $nbr_lignes_tableau);
	echo "</div>";
	fin_bloc();
	
	$ifond = 0;
	
	// Entete tableau ..
	echo "<font size='1' face='Verdana'>
	<table cellpadding='2' cellspacing='0' width='100%'>	
	<tr><td width='20%' class='tete_colonne'>";
	if($odb!='fich') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','fich');
		echo "<a href='".$lien."'>"._T('dw:fichier')."</a>";
	} else {
		echo "<b>"._T('dw:fichier')."</b>";
	}
	echo "</td><td width='20%' class='tete_colonne'>";
	if($odb!='id_typ') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','id_typ');
		echo "<a href='".$lien."'>"._T('dw:doctype')."</a>";
	} else {
		echo "<b>"._T('dw:doctype')."</b>";
	}
	echo "</td><td width='20%' class='tete_colonne'>";
	if($odb!='ttr') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','ttr');
		echo "<a href='".$lien."'>"._T('dw:titre')."</a>";
	} else {
		echo "<b>"._T('dw:titre')."</b>";
	}
    echo "</td><td width='20%' class='tete_colonne'>";
	if($odb!='desc') {
		$lien=parametre_url(self(),'odb','');
		$lien=parametre_url(self(),'odb','desc');
		echo "<a href='".$lien."'>"._T('dw:descriptif')."</a>";
	} else {
	echo "<b>"._T('dw:descriptif')."</b>";
	}
	echo "</td><td width='20%' class='tete_colonne'>";
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
		$url = $a_row['url'];
		$fichier = $a_row['fichier'];
		$doctype = $a_row['doctype'];
		$iddoctype = $a_row['id_doctype'];
		$categorie = $a_row['categorie'];
		$datecrea = $a_row['datecrea'];
		$statut=$a_row['statut'];
		$titre_doc = $a_row['titre'];
		$desc_doc = $a_row['descriptif'];
		
		// h.20/01/07 .. cesure ' ' sur nom/nomfichier trop long + 30 caract
		$fichier = wordwrap($fichier,30,' ',1);

		
		$bouton = bouton_block_invisible('bout'.$iddoc);

		// ligne du tableau
		echo "<tr><td colspan='5' valign='top'>";
		
		debut_cadre_enfonce("", false, "", $bouton." ".$fichier." -- ".$datecrea);
		
		echo "<div class='verdana3' align='right' style='padding:2px;'><b>".aff_appart_doc($doctype, $iddoctype)."</b></div>\n";
		echo "<div class='arial2 fondl' style='min-height:12px;'><b> ".$titre_doc."</b></div>\n";
		echo "<div class='arial2 fondl' style='min-height:12px;'> ".$desc_doc."</div>\n";
			
		echo debut_block_invisible('bout'.$iddoc);
			// le formulaire
			form_titre_desc($iddoc, $titre_doc, $desc_doc, generer_url_ecrire("dw2_outils","outil=titredesc"));
		echo fin_block();
		
		fin_cadre_enfonce();

		echo "</td></tr>\n";
		}
	echo "</table>\n";
	
	}

fin_cadre_trait_couleur();


}
?>
