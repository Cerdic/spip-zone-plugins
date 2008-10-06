<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Ajout Images
| Listing des Images du site.
| Forcer compteur sur fichiers img (jpg, gif, png)
+--------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_ajouts_det() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee,
		$browser_name;

// page prim en cours
#$page_affiche=_request('exec');

//
// requis
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");
include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");
include_spip("inc/dw2_inc_ajouts");

include_spip("inc/documents");

//config
$nbr_lignes_tableau=$GLOBALS['dw2_param']['nbr_lignes_tableau'];

// reconstruire .. var=val des get et post
// var :  $objet ; $vl ; $md ; $odb ; $tp ; $sel
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$v; }
foreach($_POST as $k => $v) { $$k=$v; }


//
// prepa
//

	//
	// retour : faire ajout catalogue
	//
	if($tout_select=='oui') {
		foreach($toutdoc as $item)
			{ ajout_doc_catalogue($item); }
	}
	if($select=='oui') {
		foreach($docselect as $item)
			{ ajout_doc_catalogue($item); }
	}
	//
	//
	//
	
	
	// recup' nombre de ligne passe en url, fixe debut LIMIT ...		
	$dl=($vl+0);
	
	// premiere val de tranche en cours
	$nba1 = $dl+1;
	
	//
	// methode de selection des articles/rubriques

	// objet article ou rubrique
	if(!$obj) { $objet='article'; $obj="art"; }
	else {
		switch ($obj) {
			case "art": $objet='article'; break;
			case "rub": $objet='rubrique'; break;
		}
	}
	
	// mode : document, vignette ou les deux
	if(!$md) { $mode="AND sd.mode='document'"; $md = "doc"; }
	else {
		switch ($md) {
			case "doc": $mode="AND sd.mode='document'"; break;
			case "vig": $mode="AND sd.mode='vignette'"; break;
			case "vido": $mode=""; break;
		}
	}
	
	// type : jpg-png-gif, autres types ou tous les types
	if(!$tp) { $type="AND sd.id_type <=3"; $tp="img"; }
	else {
		switch ($tp) {
			case "img": $type="AND sd.id_type <= 3"; break;
			case "aut": $type="AND sd.id_type > 3"; break;
			case "tous": $type=""; break;
		}
	}
	/**/
	// dw2 : avec ou sans catalogue DW2
	if(!$cdw) { $catdw="AND dw.id_document IS NULL"; $cdw="non"; }
	else {
		switch ($cdw) {
			case "non": $catdw="AND dw.id_document IS NULL"; break;
			case "oui": $catdw=""; break;
		}
	}

	
	// articles du site ayant des docs (choix selon mode, type)
	$q=spip_query("SELECT SQL_CALC_FOUND_ROWS sda.id_".$objet.", sa.titre, sa.statut, sd.mode, 
			COUNT(sda.id_document) as nb_doc 
			FROM spip_documents_".$objet."s sda 
			LEFT JOIN spip_".$objet."s sa ON sda.id_".$objet."=sa.id_".$objet." 
			LEFT JOIN spip_documents sd ON sda.id_document=sd.id_document 
			LEFT JOIN spip_dw2_doc dw ON sda.id_document = dw.id_document 
			WHERE 1 $type $mode $catdw 
			GROUP BY sda.id_".$objet." 
			ORDER BY sa.titre LIMIT $dl,$nbr_lignes_tableau 
			");

	// récup nombre total d'entrée
	$nl= spip_query("SELECT FOUND_ROWS()");
	$r_found = @spip_fetch_array($nl);
	$nligne=$r_found['FOUND_ROWS()'];
	
	
	// reconstruire/transmettre post url detail
	// pour retour : prepar tbl arg
	$arr_arg =array('vl','obj','md','tp', 'cdw');
	
	
	while($r=spip_fetch_array($q)) {
		$id_objet=$r['id_'.$objet];
		$titre=$r['titre'];
		$statut=$r['statut'];
		$nb_doc_objet =$r['nb_doc'];
		
		if($objet=='article') {
			$lien_exec = 'articles'; $arg_id='id_article';
		} else {
			$lien_exec='naviguer'; $arg_id='id_rubrique';
		}
		
		switch ($statut) {
			case 'publie': $puce = 'verte'; break;
			case 'prepa': $puce = 'blanche'; break;
			case 'prop': $puce = 'orange'; break;
			case 'prive': $puce = 'orange'; break;
			case 'refuse': $puce = 'rouge'; break;
			case 'poubelle': $puce = 'poubelle'; break;
		}

			
		$ret.=
			"<tr class='tr_liste verdana2'".
			(eregi("msie", $browser_name) ? " onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"" :'').
			">\n<td width='85%'>".
			"<a href='".generer_url_ecrire($lien_exec, $arg_id."=".$id_objet)."'>".
			"<img src='"._DIR_PLUGINS."dw2/img_pack/puce-".$puce."-breve.gif' border='0' valign='absmiddle'></a>&nbsp;".
			typo($titre).
			"</td>\n".
			"<td width='5%'><div align='right'>\n".
				$nb_doc_objet.
			"</div></td>\n".
			"<td width='10%'>".
			"<form action='".generer_url_ecrire("dw2_ajouts_det","sel=".$id_objet)."' method='POST'>";
			
			foreach ($arr_arg as $gk ){
				if(isset($$gk)) {
					$ret.="<input type='hidden' name='".$gk."' value='".$$gk."' />";
				}
			}
			
		$ret.=
			"<input type='submit' value='"._T('dw:voir')."' class='fondo'/>\n".
			"</form></td>\n".
			"</tr>\n";
	}

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
	// onglets page ajouts global/catalogue images		
	echo debut_onglet().
		onglet(_T('dw:ajout_manuel'), generer_url_ecrire("dw2_ajouts"), 'page_gen', '', _DIR_IMG_DW2."ajout_doc.gif").
		onglet(_T('dw:ajout_images_1_1'), generer_url_ecrire("dw2_images"), 'page_img', '', _DIR_IMG_DW2."cata_img.gif").
		onglet(_T('dw:ajout_par_article'), generer_url_ecrire("dw2_ajouts_det"), 'page_det', 'page_det', _DIR_IMG_DW2."catalogue.gif").
	fin_onglet();
	
	
	//
	// selection d'affichage :
	//
	
	$affchek = "checked='checked'";
	
	debut_cadre_relief("rien.gif");
		debut_band_titre("#dfdfdf");
			echo "<div align='center' class='verdana3'>\n";
				echo _T('dw:criteres_recherche_art_doc');
			echo "</div>";
		fin_bloc();
		echo "<form action='".generer_url_ecrire("dw2_ajouts_det")."' method='POST'>";
		
		echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'><tr>
				<td width='25%' valign='top'>";
				echo debut_boite_filet('a');
				echo "<br />
					<input type='radio' name='obj' value='art' ";
					echo ($obj=='art')? $affchek : '';
					echo " />&nbsp;"._T('dw:article')."<br />
					<input type='radio' name='obj' value='rub' ";
					echo ($obj=='rub')? $affchek : '';
					echo " />&nbsp;"._T('dw:rubrique');
				echo fin_bloc();
				echo "</td><td width='25%' valign='top'>";
				echo debut_boite_filet('a');
				echo _T('dw:mode_doc_spip')."<br />
					<input type='radio' name='md' value='doc' ";
					echo ($md=='doc')? $affchek : '';
					echo " />&nbsp;"._T('dw:document')."<br />
					<input type='radio' name='md' value='vig' ";
					echo ($md=='vig')? $affchek : '';
					echo " />&nbsp;"._T('dw:image')."<br />
					<input type='radio' name='md' value='vido' ";
					echo ($md=='vido')? $affchek : '';
					echo " />&nbsp;"._T('dw:les_deux');
				echo fin_bloc();
				echo "</td><td width='25%' valign='top'>";
				echo debut_boite_filet('a');
				echo _T('dw:type_spip_extension')."<br />
					<input type='radio' name='tp' value='img' ";
					echo ($tp=='img')? $affchek : '';
					echo " />&nbsp;jpg-png-gif<br />
					<input type='radio' name='tp' value='aut' ";
					echo ($tp=='aut')? $affchek : '';
					echo " />&nbsp;"._T('dw:autres')."<br />
					<input type='radio' name='tp' value='tous' ";
					echo ($tp=='Tous')? $affchek : '';
					echo " />&nbsp;"._t('dw:tous');
				echo fin_bloc();
				echo "</td><td width='25%' valign='top'>";
				echo debut_boite_filet('a');
				echo _T('dw:catalogue_dw2')."<br />
					<input type='radio' name='cdw' value='non' ";
					echo ($cdw=='non')? $affchek : '';
					echo " />&nbsp;"._T('dw:rejete_doc_dw2')."<br />
					<input type='radio' name='cdw' value='oui' ";
					echo ($cdw=='oui')? $affchek : '';
					echo " />&nbsp;"._T('dw:integre_doc_dw');
				fin_bloc();
				echo "</td>
			</tr><tr>
				<td colspan='4'><div align='right'>
					<input type='submit' value='"._T('dw:selection')."' class='fondo' />
				</div></td>
			</tr>
			</table>
			";
		echo "</form>";

	fin_cadre_relief();
	
	
	//
	// tableau art - rub
	//

	if(spip_num_rows($q)) {
		
		debut_cadre_relief(_DIR_IMG_DW2."catalogue.gif");
		
		// tranches selection
		if($nligne>$nbr_lignes_tableau) {
			debut_band_titre("#dfdfdf");
				echo "<div align='center' class='verdana2'>\n";
				tranches($nba1, $nligne, $nbr_lignes_tableau);
				echo "</div>\n";
			fin_bloc();
		}	
		echo "\n<br /><table cellpadding='2' cellspacing='1' width='100%' border='0'>\n";
		echo "<tr>
			<td width='85%'>"._T('dw:titre')." "._T('dw:'.$objet)."</td>
			<td colspan='2' width='15%'><div align='center'>"._T('dw:nbre_docs')."</div></td>
			</tr>";
			
		// lignes art/rub
		echo $ret;
		
		echo "</table>\n";
		
		fin_cadre_relief();
		
		
		//
		// tableau document de l'objet (art/rub)
		//
		if($sel) {
			$rqob=spip_query("SELECT titre, statut FROM spip_".$objet."s WHERE id_".$objet."=".$sel);
			$lgob=spip_fetch_array($rqob);
			$ttr_obj=supprimer_numero(typo($lgob['titre']));
			$statut_obj=$lgob['statut'];
			
			debut_cadre_relief($objet."-24.gif");
			
			debut_band_titre("#efefef", "verdana3", "bold");
				echo "<span class='verdana3'>".$ttr_obj."</span><br />";
			fin_bloc();
			
			echo "<form action='".generer_url_ecrire("dw2_ajouts_det","sel=".$sel)."' method='post'>";
			
			echo "\n<br /><table cellpadding='2' cellspacing='1' width='100%' border='0'>\n";
			echo "<tr class='verdana2'>
				<td width='6%'>"._T('dw:mode_doc_spip')."</td>
				<td width='14%'>"._T('dw:type_spip_extension')."</td>
				<td>"._T('dw:presente_detail_doc')."</td>
				<td width='5%'>"._T('dw:ajouter')."</td>
				</tr>";
			
			// genere tableau des docs
			$lesdocs = liste_documents_art_rub($sel,$objet,$mode,$type);
			$nb_doc_art = count($lesdocs);
			$compte_dw=0;
			
			foreach($lesdocs as $id_doc => $args) {
			/**/
				list($fichier, $largeur, $hauteur) = vignette_par_defaut($args['extension']);
				$image = "<img src='$fichier'\n\t height='$hauteur' width='$largeur' />";
				
				if($args['mode']=='document')
					{ $aff_md="<img src='"._DIR_IMG_PACK."doc-24.gif' border='0' align='absmiddle' title='"._T('dw:mode_doc_spip_document')."'>"; }
				else 
					{ $aff_md="<img src='"._DIR_IMG_PACK."vignette-24.png' border='0' align='absmiddle' title='"._T('dw:mode_doc_spip_image')."'>"; }
				
				echo "<tr class='tr_liste verdana2'";
				echo (eregi("msie", $browser_name) ? " onmouseover=\"changeclass(this,'tr_liste_over');\" onmouseout=\"changeclass(this,'tr_liste');\"" :'');
				echo ">\n".
					"<td>".$aff_md."</td>\n".
					"<td><div align='center'>\n".$image."</div></td>\n".
					"<td>".wordwrap($args['fichier'],30,' ',1)."<br />".taille_octets($args['taille'])."<br />".
					"<b>".typo($args['titre'])."</b><br />".typo($args['descriptif']).
					"</td>\n<td><div align='center'>";
				
				// enregistrable ?
				if($args['dw']){
					echo "<a href='".generer_url_ecrire("dw2_modif", "id=".$id_doc)."'>";
					echo "<img src='"._DIR_IMG_DW2."fiche_doc-15.gif' border='0' align='absmiddle' title='"._T('dw:voir_fiche')."' />";
					echo "</a>";
					$compte_dw++;
				}
				elseif($statut_obj!='publie') {
					echo "<img src='"._DIR_IMG_DW2."puce-orange-breve.gif' border='0' valign='absmiddle' title='"._T('dw:'.$objet)."&nbsp;"._T('dw:statut_non_publie')."'/>";
				}
				else {
					echo "<input type='checkbox' name='docselect[]' value='".$id_doc."' />";
				}
				echo "</div></td>\n</tr>\n";
			}
			echo "</table>\n";
			
			
			// si au moins un doc pas deja enreg
			//
			if($compte_dw!=$nb_doc_art) {
				foreach ($arr_arg as $gk ){
					if(isset($$gk)) {
						echo "<input type='hidden' name='".$gk."' value='".$$gk."' />";
					}
				}
				echo "<div align='right'>\n
					<input type='hidden' name='select' value='oui'>\n
					<input type='submit' value='"._T('dw:ajouter_select')."' class='fondo'>\n
					</div>\n";
			}
			echo "</form>";
			
			
			//
			// bouton tout ajouter
			//
			// si au moins un doc pas deja enreg
			if($compte_dw!=$nb_doc_art) {
				echo "<form action='".generer_url_ecrire("dw2_ajouts_det","sel=".$sel)."' method='post'>";
				foreach($lesdocs as $id => $elem) {
					if(!$elem['dw']) {
						echo "<input type='hidden' name='toutdoc[]' value='".$id."' />";
					}
				}
				//
				foreach ($arr_arg as $gk ){
					if(isset($$gk)) {
						echo "<input type='hidden' name='".$gk."' value='".$$gk."' />";
					}
				}
				
				echo "<br /><div align='right'>\n
					<input type='hidden' name='tout_select' value='oui'>\n
					<input type='submit' value='"._T('dw:ajout_tout')."' class='fondo'>\n
					</div>\n";
				
				echo "</form>";
			}
			fin_cadre_relief();
		}
		
	// aucun art/rub ayant docs 
	}
	else {
		debut_cadre_relief($objet."-24.gif");
			echo _L('...');
		fin_cadre_relief();
	}



//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_

?>
