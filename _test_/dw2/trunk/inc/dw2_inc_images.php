<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| traitement : Ajout Images
| (OU .. dw2_cata_img.php)
| Listing des Images du site.
| Forcer compteur sur fichiers img (jpg, gif, png)
+--------------------------------------------+
*/



//
// generer le tableau des fichiers imgages type : jpg(1), png(2), gif(3)
// -> forcer image dans dw2
function tableau_doc_images()
	{
	// requete doc type jpg, png, gif
	$query = "SELECT sd.id_document, sd.id_vignette, sd.id_type, sd.fichier, sd.largeur, 
			sd.hauteur, sd.distant, sd.mode, IF (dd.id_document,'oui','non') AS dw_in 
			FROM spip_documents sd LEFT JOIN spip_dw2_doc dd ON sd.id_document=dd.id_document
			WHERE sd.id_type BETWEEN 1 AND 3 ";
			
	// ... avec 'vignette' (miniature)
	$cond = "AND sd.id_vignette !='0' ";
	
	$tab_small=array(); // h2/4/06
	
	$result = spip_query($query.$cond);
	while ($row=spip_fetch_array($result))
		{
		$iddoc=$row['id_document'];
		$idvign=$row['id_vignette'];
		$urlfichier=$row['fichier'];
		$fichier=substr(strrchr($row['fichier'],'/'), 1);
		
		$tab_spipimg[$iddoc]['id_document']=$iddoc;
		$tab_spipimg[$iddoc]['fichier']=$fichier;
		$tab_spipimg[$iddoc]['idvign']=$idvign;
		$tab_spipimg[$iddoc]['urlfichier']=$urlfichier;
		$tab_spipimg[$iddoc]['id_type']=$row['id_type'];
		$tab_spipimg[$iddoc]['largeur']=$row['largeur'];
		$tab_spipimg[$iddoc]['hauteur']=$row['hauteur'];
		$tab_spipimg[$iddoc]['dw_in']=$row['dw_in'];
		$tab_spipimg[$iddoc]['mode']= $row['mode'];
		$tab_spipimg[$iddoc]['distant']= $row['distant'];
		
		// table des miniatures type '-s' et autre vignette personnalisee
		$tab_small[]=$idvign;
		}
	
	reset($tab_small);
	
	// chercher doc pas dans tab_small (sans miniature)
	$result2=spip_query($query);
	while ($row2=spip_fetch_array($result2))
		{
		$iddoc=$row2['id_document'];
		$idvign=$row2['id_vignette'];
		$urlfichier=$row2['fichier'];
		$fichier=substr(strrchr($row2['fichier'],'/'), 1);
		
		if(!in_array($iddoc,$tab_small))
			{
			$tab_spipimg[$iddoc]['fichier']=$fichier;
			$tab_spipimg[$iddoc]['id_document']=$iddoc;
			$tab_spipimg[$iddoc]['idvign']=$idvign;
			$tab_spipimg[$iddoc]['urlfichier']=$urlfichier;
			$tab_spipimg[$iddoc]['id_type']=$row2['id_type'];
			$tab_spipimg[$iddoc]['largeur']=$row2['largeur'];
			$tab_spipimg[$iddoc]['hauteur']=$row2['hauteur'];
			$tab_spipimg[$iddoc]['dw_in']=$row2['dw_in'];
			$tab_spipimg[$iddoc]['mode']= $row2['mode'];
			$tab_spipimg[$iddoc]['distant']= $row2['distant'];
			}
		}
	return $tab_spipimg;
	}


//
// element spip
global $spip_ecran;
$nom_site_spip = $GLOBALS['meta']['nom_site'];


//
// requis
include_spip("inc/documents");


// reconstruire .. var=val des get et post
// var : imgin ; tabforcimg ; amv ; 
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//config
$nbr_lignes_tableau=$GLOBALS['dw2_param']['nbr_lignes_tableau'];
$mode_affiche_images=$GLOBALS['dw2_param']['mode_affiche_images'];

# h.04/03/07 -- redef nbre affichage pour hors_dw
if($hors_dw) {
	if($spip_ecran == "large") {
		$nbr_lignes_tableau=24;
	}
	else {
		$nbr_lignes_tableau=16;
	}
}


	//
	//
	// enregistrement Docs selectionnes, dans cat. dw
	if($imgin=='oui' AND $tab_forcimg AND $connect_statut=="0minirezo" AND !$hors_dw)
		{
		debut_band_titre("#dfdfdf");
		echo "<span class='verdana2'>"._T('dw:enreg_doc')." :<br>";
		foreach($tab_forcimg as $id_in)
			{
			ajout_doc_catalogue($id_in);
			echo "<b><a href='".generer_url_ecrire("dw2_modif", "id=".$id_in)."' title='"._T('dw:voir_fiche')."'>".$id_in."</a></b>, ";
			}
		echo "</span>";
		fin_bloc();
		}


	//
	//
	// generer le tableau (function dans _inc_func)
	$tab_spipimg = tableau_doc_images();
	
	// nombre de ligne du tableau et si aucune on sort
	$nligne=count($tab_spipimg);
	if($nligne==0) {
		debut_cadre_relief(_DIR_IMG_DW2."catalogue.gif");
		echo "<br><b>"._T('dw:aucun_doc_type_img').".<br>";
		fin_cadre_relief();
		break;
	}

	// prepa données puis affichage
	
		//  recup' nombre de ligne et son retour, fixe LIMIT ...		
		$dl=($vl+0);
		
		// valeur de tranche affichée	
		$nba1 = $dl+1;
		
		// tri du tableau selon odb
		function compare($a, $b)
			{ return strcasecmp($a["fichier"], $b["fichier"]); }
		if($odb=="fichier")
			{ usort($tab_spipimg, "compare"); }
		else
			{ ksort($tab_spipimg); }
			
		// prepa toutdeplier toutreplier
		while (list($id) = each($tab_spipimg))
			{
			$id_document=$tab_spipimg[$id]['id_document'];
			$les_docs[] = "bout$id_document";
			$nom_block = "bout$id_document";
			if (!$numero_block["$nom_block"] > 0)
				{
				$compteur_block++;
				$numero_block["$nom_block"] = $compteur_block;
	
				if (!$first_couche) 
					{ $first_couche = $compteur_block; }
					{ $last_couche = $compteur_block; }
				}
			}

		// trancher le tableau
		$tab_spipimg = array_slice($tab_spipimg,$dl,$nbr_lignes_tableau);

		
		// $amv : affichage mode vignette (ou mode ligne)
		// $amv prend la valeur de config : $mode_affiche_images
		
		// mode par defaut : $mode_affiche_images
		if(!$amv) { $amv=$mode_affiche_images; }
		// lien
		if($amv=='1') {
			$onglet='aff_mode_ligne';
			$lien=parametre_url(self(),'amv','');
			$lien=parametre_url(self(),'amv','2');
			}
		elseif($amv=='2') {
			$onglet='aff_mode_vignette';
			$lien=parametre_url(self(),'amv','');
			$lien=parametre_url(self(),'amv','1');
			}


	//
	// affichage page
	//
	if($hors_dw) { $icone_cadre="../"._DIR_IMG_DW2."cata_img.gif"; }
	else { $icone_cadre="rien.gif"; }
	
	debut_cadre_relief($icone_cadre);
	
		// Titre tableau ..
		debut_band_titre($couleur_foncee);
			echo "<div align='center' class='verdana3'><b>"._T('dw:cat_images_de', array('nom_site_spip' => $nom_site_spip))."</b></div>";
		fin_bloc();
		
		//
		// onglets choix du mode		
		echo debut_onglet().
		onglet(_T('dw:mode_ligne'), $lien, 'aff_mode_ligne', $onglet, _DIR_IMG_DW2."catalogue.gif").
		onglet(_T('dw:mode_vignette'), $lien, 'aff_mode_vignette', $onglet, "vignette-24.png").
		fin_onglet();
		


		//
		// affichage tranches
		debut_band_titre("#dfdfdf");
		echo "<div align='center' class='verdana2'>\n";
		tranches($nba1, $nligne, $nbr_lignes_tableau);
		echo "</div>\n";
		fin_bloc();



		//
		// toutdeplier/toutreplier block_invi
		$javasc_ouvrir = "manipuler_couches('ouvrir','$spip_lang_rtl',$first_couche,$last_couche, '" . _DIR_IMG_PACK . "')";
		$javasc_fermer = "manipuler_couches('fermer','$spip_lang_rtl',$first_couche,$last_couche, '" . _DIR_IMG_PACK . "')";
		if ($les_docs)
			{
			$les_docs = join($les_docs,",");
			echo "<div style='float:left; padding:3px;'>";
			echo "<b class='verdana2'>";
			echo "<a href=\"javascript:$javasc_ouvrir\">";
			echo _T('lien_tout_deplier');
			echo "</a>";
			echo "</b></div>";
			echo "<div style='float:right; padding:3px;'>";
			echo "<b class='verdana2'>";
			echo "<a href=\"javascript:$javasc_fermer\">";
			echo _T('lien_tout_replier');
			echo "</a>";
			echo "</b></div>\n";
			echo "<div style='clear:both'></div>\n";
			}

					
		$ifond = 0;

		//
		// tableau .. 
		//
		
		// si ce script est appele par dw2_cata_img (hors dw2)
		// desactive le selecteur (checkbox
		if(!$hors_dw) {
			echo "<form action='".self()."' method='post'>\n";
		}
		
		
		// tete de colonne ...
		//
		echo "<table align='center' border='0' cellpadding='2' cellspacing='0' width='100%'>\n	
			<tr>
				<td width='14%' colspan='2' class='tete_colonne'>\n";
					if($odb) {
						$lien=parametre_url(self(),'odb','');
						echo "<a href='".$lien."'><b>"._T('dw:numero')."</b></a>";
					}
					else {
						echo "<b>"._T('dw:numero')."</b>";
					}
				echo "</td>\n
				<td width='68%' colspan='2' class='tete_colonne'>\n";
					if(!isset($odb)) {
						$lien=parametre_url(self(),'odb','fichier');
						echo "<a href='".$lien."'><b>"._T('dw:fichier')."</b></a>";
					}
					else {
						echo "<b>"._T('dw:fichier')."</b>";
					}
				echo "</td>\n
				<td width='14%' class='tete_colonne'>\n
					<b>DW2 +</b>
				</td>\n
			</tr>\n";
			
		
		//
		// si affichage vignettes : colonnage
		if($amv=='2') {
			echo "<tr>";
			echo "<td colspan='5'>";
			$i=0;
			$icol=3; 			// nombre de colonnes
			$widthcol='150px'; 	// largeur colonne .. choix conseille .. col:2/lrg:230 - col:3/lrg:150 
			
			// si hors_dw changer nombre colonne 
			//($page_affiche n'est pas définie dans dw2_cata_img !)
			if(!isset($page_affiche)) {
				if ($spip_ecran == "large") { $icol=6; $widthcol='149px'; }
				else { $icol=4; $widthcol='173px'; }
			}
		}

		//
		// extraire element et l'afficher
		//
		$icel='0';
		while (list($iddoc) = each($tab_spipimg)) {
			//objet : 
			foreach($tab_spipimg[$iddoc] as $item => $valitem)
				{ $$item=$tab_spipimg[$iddoc][$item]; }

			if($mode=="document") { $mode="doc"; }
			else { $mode="img"; }
			
			// cesure ' ' sur nom/nomfichier trop long
			#mode ligne :
			$fichier_lg = wordwrap($fichier,45,' ',1);
			$nomfichier_lg = wordwrap($nomfichier,45,' ',1);
			#mode vignette :
			$fichier_v = wordwrap($fichier,25,' ',1);
			$nomfichier_v = wordwrap($nomfichier,25,' ',1);
			

			// origine
				// on acceptera de selectionner des images mm si l'article(rub) est 'non-publie'
				// car pas d'automatisme pour les images. (( $origine[2] -> statut ))
			$origine=origine_doc($id_document);
			$doctype=$origine[0];
			$iddoctype=$origine[1];

			// placer checkbox SI pas dw_in, SI article ou rubrique
			// dw2 à ce stade ne gère pas les doc images des breves .. 
	// h.31/01/06 .. ??? ..voir à intégrer les breves ; test à faire et utilité
			$checkbox= ($dw_in=='oui') ? '2' : ($doctype=='article' ? '1' : ($doctype=='rubrique' ? '1' : ''));
			 
			
			// traitement vignette affichee
			//
			// prepa fonction spip, suivante ..
			$document=array('id_type'=> $id_type, 'id_vignette' => $idvign, 'fichier'=>$urlfichier);
			if($idvign>'0')
				{
				$logo_idvign = "vignette-16.png";
				$nomfichier=$tab_vignette[3];
				$aff_vignette = document_et_vignette($document, '', true);
				}
			else
				{
				$logo_idvign = "";
				$aff_vignette = document_et_vignette($document, '', true);
				}


			$bouton = bouton_block_invisible("bout$id_document");
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#ffffff' : $couleur_claire;

			//
			// ligne du tableau
			//
			
			// affichage mode ligne
			if($amv=='1') {
				echo "<tr bgcolor='".$couleur."'>";
				echo "<td width='4%'><div align='center'>".$bouton."</div></td>\n";
				echo "<td width='10%'><div align='right' class='verdana2'>".$id_document." .</div></td>\n";
				echo "<td width='68%'><div class='verdana2'>".$fichier_lg."</div></td>\n";
				echo "<td width='4%'><img src='"._DIR_IMG_DW2.$logo_idvign."' align='absmiddle'></td>\n";
				echo "<td width='14%'><div align='center'>\n";
				
				if(!$hors_dw) {
				if ($checkbox=='2')
					{ echo "<a href='".generer_url_ecrire("dw2_modif","id=".$id_document)."'>
							<img src='"._DIR_IMG_DW2."fiche_doc-15.gif' style='padding:2px;' align='absmiddle' border='0' title='"._T('dw:voir_fiche')."'></a>"; }
				else if ($checkbox=='1')
					{ echo "<input type='checkbox' name='tab_forcimg[]' value='".$id_document."' title='"._T('dw:ajouter')."'>"; }
				else
					{ echo "<img src='"._DIR_IMG_PACK."croix-rouge.gif' align='absmiddle' title=''>"; } // title !!?
				}
				
				echo "</div></td>\n";
				echo "</tr>\n";
				echo "<tr bgcolor='$couleur'><td colspan='5' class='verdana1'>\n";
				
				// deroulant
				echo debut_block_invisible("bout$id_document");
				echo "<div style='float:left; padding-right:6px;'>".$aff_vignette."</div>\n";
				echo "<span class='verdana2'>&lt;".$mode."<b>".$id_document."</b>&gt;</span><br>".
					aff_appart_doc($doctype,$iddoctype);
				if($idvign>'0')
					{ echo "<br><img src='"._DIR_IMG_DW2."vignette-16.png' align='absmiddle'>\n ".$idvign." . ".$nomfichier_lg; }
				echo fin_block();
				
				echo "</td></tr>\n";
			}
			
			// affichage mode vignette
			else {
				$icel++;
				if($hors_dw) { ($icel=='2'||$icel=='4'||$icel=='6')? $coulb36 ='gris' : $coulb36 ='blanc'; }
				else { ($icel=='2')? $coulb36 ='gris' : $coulb36 ='blanc'; }
				
				echo "<div style='float:left; width:".$widthcol."; margin:2px 2px 4px 2px; padding:2px;' class='bouton36".$coulb36."'>\n";
				
				echo "<div style='background-color:".$couleur_claire."; margin-bottom:4px; padding:2px;' class='verdana2'>".$fichier_v."</div>\n";

				echo "<div style='position:relative; height:160px;'>\n";
				echo "<div style='position:absolute; right:1px'><img src='"._DIR_IMG_DW2.$logo_idvign."'></div>\n";
				echo "<div align='center'>".$aff_vignette."<br>\n";

				echo "<span class='verdana2'>&lt;".$mode."<b>".$id_document."</b>&gt;</span>\n";
				if($distant=='oui')
					{ echo "&nbsp;<img src='"._DIR_IMG_PACK."attachment.gif' align='absmiddle' title='".$urlfichier."'>"; }
				echo "</div>";
				echo "</div>\n";
							
				echo "<div style='background-color:".$couleur_claire."; padding-bottom:2px;'>\n";

				echo "<div style='float:right;'>\n";
				if (!$hors_dw) {
				if ($checkbox=='2')
					{ echo "<a href='".generer_url_ecrire("dw2_modif", "id=".$id_document)."'>\n
							<img src='"._DIR_IMG_DW2."fiche_doc-15.gif' border='0' style='padding:2px;' title='"._T('dw:voir_fiche')."'></a>\n"; }
				else if ($checkbox=='1')
					{ echo "<input type='checkbox' name='tab_forcimg[]' value='".$id_document."' title='"._T('dw:ajouter')."'>\n"; }
				else
					{ echo "<img src='"._DIR_IMG_PACK."croix-rouge.gif' style='padding:5px;' align='absmiddle' title=''>\n"; } // title !!?
				}
				echo "</div>\n";

				echo $bouton;
				echo "</div>\n";
				
				// deroulant
				echo debut_block_invisible("bout$id_document");
				echo "<div class='verdana2'>\n";
				echo aff_appart_doc($doctype,$iddoctype);
				if($idvign>'0')
					{ echo "<br><img src='"._DIR_IMG_DW2."vignette-16.png' align='absmiddle'> . ".$idvign." .<br />".$nomfichier_v; }
				echo "</div>\n";
				echo fin_block();
				
				echo "</div>";

				if($icel==$icol) {
					echo "<div style='clear:both;'></div>\n";
					$icel='0';
				}
			}
			
		}// fin while

		if($amv=='2') {
			echo "</td></tr>\n";
			}

		echo "</table>\n";

		if(!$hors_dw) {
		echo "<div align='right'>\n
			<input type='hidden' name='imgin' value='oui'>\n
			<input type='submit' value='"._T('dw:ajouter')."' class='fondo'>\n
			</div>\n
			</form>\n";
		}

fin_cadre_relief();

?>
