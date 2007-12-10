<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Lister les Documents, type, taille, origine ...
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_tabbord_documents() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;


//
// requis
//
include_spip('inc/tabbord_pres');
include_spip('inc/func_tabbord_docs');
include_spip("inc/documents");

//
// prepa
//

	// fixer le nombre de ligne du tableau (tranche)
	$fl=20;

	// recup $vl dans URL
	$dl=($_GET['vl']+0);



// generer le tableau des docs
$tbl_docs = tableau_def_docs();
$nligne=count($tbl_docs);


// compter types de docs, extension ...
$tbl_types=totaux_types_documents($tbl_docs);


// tri sur tableau docs
function compare($a, $b)
	{ return strcasecmp($a["fichier"], $b["fichier"]); }
$odb=_request('odb');
if($odb=="fichier")
	{ usort($tbl_docs, "compare"); }
else
	{ ksort($tbl_docs); }


// derniere phase :
// trancher le tableau
	$tbl_docs = array_slice($tbl_docs,$dl,$fl);


//
// affichage
//
#debut_page(_T('tabbord:titre_plugin'), "suivi", "tabbord");
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('tabbord:titre_plugin'), "suivi", "tabbord_gen", '');
	echo "<br />";


// Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques)
	{
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
	}
	

debut_gauche();

menu_gen_tabbord();


debut_droite();

echo "<div style='width:650px;'>";
debut_cadre_formulaire();

// affichage tableau
	if (count($tbl_docs)>0) {
		// valeur de tranche affichée	
		$nba1 = $dl+1;
		//	
		
		gros_titre(_T('tabbord:document_s_').$GLOBALS['meta']['nom_site']);
		
		// Présenter valeurs de la tranche de la requête
		echo "<div align='center' class='iconeoff verdana2' style='clear:both;'>\n";
		tranches_liste($nba1,$nligne,$fl);
		echo "\n</div>\n";

		// entête ...
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n
			<tr>\n";
			echo "<td width='7%'>";
			if($odb=='fichier') {
				echo "<a href='".generer_url_ecrire(_request('exec'))."' title='"._T('tabbord:tri_par_id')."'>"._T('tabbord:id_mjsc')."</a>";
			} else { echo "<b>&gt;"._T('tabbord:id_mjsc')."&lt;</b>"; }
			echo "</td>\n<td width='34%'>";
			if($odb=='') {
				echo "<a href='".generer_url_ecrire(_request('exec'), 'odb=fichier')."' title='"._T('tabbord:tri_par_nom')."'>"._T('tabbord:fichier')."</a>";
			} else { echo "<b>&gt;"._T('tabbord:fichier')."&lt;</b>"; }
			echo "</td>\n".
				"<td width=5%>"._T('tabbord:mode')."</td>\n".
				"<td width=5%>&nbsp;</td>\n".
				"<td width='6%'>"._T('tabbord:origine')."</td>\n".
				"<td width=5%>&nbsp;</td>\n".
				"<td class='center' width=13%>"._T('tabbord:larg_x_haut')."</td>\n".
				"<td class='center' width=12%>"._T('tabbord:taille')."</td>\n".
				"<td class='center' width=13%>"._T('tabbord:cree_le')."</td>\n".
				
			"</tr>\n";

		// corps du tableau
		foreach ($tbl_docs as $k => $v) {
			$id=$v['id_document'];
			$fichier=$v['fichier'];
			$id_vignette=$v['id_vignette'];
			$url_fichier=$v['urlfichier'];
			$id_type=$v['id_type'];
			$larg=$v['largeur'];
			$haut=$v['hauteur'];
			$taille=taille_en_octets($v['taille']);
			
			if($v['mode']=='vignette') {
				$mode = http_img_pack('image-24.gif','mod','',_T('tabbord:mode_vignette'));
			}
			else { $mode = http_img_pack('doc-24.gif','mod','',_T('tabbord:mode_document')); }
			
			/**/
			#$document=array('id_type'=> $id_type, 'id_vignette' => $id_vignette, 'fichier'=>$url_fichier);
			#$aff_vignette = document_et_vignette($document, '', true);
			$logo_vign = "";
			if($id_vignette > '0') {
				$logo_vign = http_img_pack('vignette-24.png','min','',_T('tabbord:vignette_associee'));
			}
			
			if($v['distant']!='non') {
				$distant=http_img_pack('attachment.gif','ico','',_T('tabbord:fichier_distant'));
			}
			
			$date=$v['date'];
			
			$orig = origine_document($id);
			if($orig[0]=='article') { $lien_orig = "articles"; $ico_orig=http_img_pack('article-24.gif','art','',''); }
			elseif($orig[0]=='rubrique') {$lien_orig = 'naviguer'; $ico_orig=http_img_pack('rubrique-24.gif','art','',''); }
			elseif($orig[0]=='breve') { $lien_orig = 'breves_voir'; $ico_orig=http_img_pack('breve-24.gif','art','','');}
			$titre_orig = propre($orig[2]);
			
			// la ligne ...
			echo "<tr class='liste'>".
				"<td class='right'>$id . </td>".
				"<td><a href='".generer_url_document($id)."'>".wordwrap($fichier,40,' ',1)."</a></td>".
				"<td class='center'>".$mode."</td>".
				"<td class='center'>".$logo_vign."</td>".
				"<td class='center'>
					<a href='".generer_url_ecrire($lien_orig,'id_'.$orig[0]."=".$orig[1])."' title='".$titre_orig."'>".
					$ico_orig."</a>
				</td>".
				"<td class='center'>$distant</td>".
				"<td class='center'>".($larg>0?"$larg x $haut":'')."</td>".
				"<td class='right'>$taille</td>".
				"<td class='right'>$date</td>".
				
				"</tr>";
		
		}

		echo "</table>\n";
	}
	else {
		echo _T('tabbord:pas_doc_sur_site');
	}

fin_cadre_formulaire();

echo "<br />";
debut_cadre_formulaire();
	gros_titre(_T('tabbord:types_de_docs_'));
	// entête ...
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='tabbord'>\n
			<tr>\n".
			"<td class='center' width='8%'>"._T('tabbord:id_mjsc')."</td>".
			"<td class='center' width='11%'>"._T('tabbord:type')."</td>".
			"<td width='23%'>"._T('tabbord:titre')."</td>".
			"<td class='center' width='10%'>"._T('tabbord:nombre')."</td>".
			"<td width='48%'>&nbsp;</td>".
		"</tr>";
		foreach($tbl_types as $t => $n) {
			
			echo "<tr class='liste'><td class='right'>".$t."&nbsp;</td>".
				"<td class='center'>(.".$n['ext'].")</td>".
				"<td>".typo($n['titre'])."</td>".
				"<td class='center'>".$n['nb']."</td>".
				"<td>&nbsp;</td>".
				"</tr>";
		}
		echo "</table>\n";

fin_cadre_formulaire();
echo "</div>";

//
//
echo fin_gauche(), fin_page();
}
?>
