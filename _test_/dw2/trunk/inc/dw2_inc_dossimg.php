<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Fonction Outils : répertoire IMG/
+--------------------------------------------+
| Lister le Repert. IMG/ et les sous-repert
| contenant des docs. (typiquement : /zip ; /pdf ...)
| horsmis : 'icones', 'icones-barre', 'cache-50...' etc ..
+--------------------------------------------+
*/

function dossimg() {

	// elements spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee;

	
	
	// reconstruire .. var=val des get et post
	// var :  b_repert
	// .. Option .. utiliser : $var = _request($var);
	foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
	foreach($_POST as $k => $v) { $$k=$_POST[$k]; }
	


debut_cadre_trait_couleur(_DIR_IMG_PACK."doc-24.gif", false, "", _T('dw:conten_repert_img'));


if ($b_repert=='')
	{ $b_repert="../IMG/"; }

$rep=opendir($b_repert);

// lister les extensions connues de spip (pour limiter les dossiers affichés !)
	$tab_ext=array();
	$r_extens = spip_query("SELECT extension FROM spip_types_documents");
	while ($s_ext = spip_fetch_array($r_extens))
		{
		$extens=$s_ext['extension'];
		$tab_ext[]=$extens;
		}
	
	// ajouter le repert. "distant" // h.19/03
	$tab_ext[]="distant";
	
	reset($tab_ext);	
//

chdir($b_repert);

if ($b_repert !="../IMG/")
	{
	// prepare chemin du bouton de retour //h.19/03
	$t_chem = explode("/", $b_repert);
	$nc = count($t_chem)-2;
	for($i=0; $i<$nc; $i++) {
		$chem .= $t_chem[$i]."/";
	}
	// bouton de retour
	debut_band_titre($couleur_claire);
	echo "<a href='".generer_url_ecrire("dw2_outils","outil=dossimg&b_repert=".$chem)."'>";
	$contenu ="<img src='"._DIR_IMG_PACK."rubrique-24.gif' align='absmiddle' border='0'>";
	echo bouton_alpha($contenu);
	echo "</a>&nbsp;&nbsp;<img src='"._DIR_IMG_DW2."rubrique-12.gif' align='absmiddle' border='0'>";
	echo "<span class='verdana2'> $b_repert</span><br>";
	echo "<div style='clear:both;'> </div>";
	fin_bloc();
	}

// lister les doc des répertoires ...
while ($file = readdir($rep))
	{
 	if($file == in_array($file,$tab_ext))// && $b_repert=="../IMG/"
		{
		if (is_dir($file))
			{
			$lien_file = generer_url_ecrire("dw2_outils", "outil=dossimg&b_repert=".$b_repert.$file."/");
			$f_t[$file] = $lien_file;
			$chaine_titre = "repertoire_nombre";
			$icone_item = "rubrique-12.gif";
			$info_supp = "href";
			}
		}
	else if ($b_repert!="../IMG/" && isset($file))
		{
		$voldos +=0;
		if ($file!='..' && $file !='.' && $file !='.test' && $file !='' )
			{
			$taille = filesize($file);
			$lien_file = taille_octets($taille);
			$f_t[$file] = $lien_file;
			$chaine_titre = "fichier_nombre";
			$icone_item = "puce-verte-breve.gif";
			$info_supp = "info";
			$voldos += $taille;
			}
		}
	}
closedir($rep);


	// affichage tableau (2 colonnes)
	debut_band_titre('');
		echo "<div align='center'>";
		
		if ($f_t=='')
			{ echo "<div align='center' class='verdana3'><b>"._T('dw:repert_vide')."</b></div></div>"; }
		else
			{ double_colonne($f_t, $chaine_titre, $icone_item, $info_supp); }
			
		if ($voldos > 0)
			{ echo "<div class='verdana2' align='right'>"._T('dw:repert_taille').taille_octets($voldos)."</div>"; }
			
		echo "</div>";
	fin_bloc();



		
fin_cadre_trait_couleur();


}// fin fonction 

?>
