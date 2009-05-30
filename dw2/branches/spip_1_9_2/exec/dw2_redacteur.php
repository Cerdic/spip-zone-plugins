<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Accueil Redacteur ... 
| Liste les Docs (art. publie, dans dw2) d'un auteur
+------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_dw2_redacteur() {

	// elements spip
	global $connect_id_auteur, $couleur_claire;

	// requis
	include_spip('inc/dw2_inc_pres');


//
// prepa 
//
	$res=spip_query("SELECT saa.id_article, sa.titre 
					FROM spip_auteurs_articles saa, spip_articles sa 
					WHERE saa.id_auteur=$connect_id_auteur AND saa.id_article=sa.id_article 
					AND sa.statut='publie'");
					
	$tab=array();
	
	while($row=spip_fetch_array($res)) {
		$id_art = $row['id_article'];
		
		$rdoc=spip_query("SELECT id_document, url, total, DATE_FORMAT(dateur,'%d/%m/%Y - %H:%i') AS datetel 
							FROM spip_dw2_doc WHERE id_doctype=$id_art AND doctype='article'");
		$i=0;
		while ($ldoc=spip_fetch_array($rdoc)) {
			$nomfichier = substr(strrchr($ldoc['url'],'/'), 1);
			$dateur = $ldoc['datetel'];
			$total = $ldoc['total'];
			$iddoc = $ldoc['id_document'];
			
			$tab[$id_art]['titre']=$row['titre'];
			$tab[$id_art]['doc'][$iddoc]=array($nomfichier,$total,$dateur);
			$i++;
		}
	}
	reset($tab);


//
// affichage 
//

	debut_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");
	echo "<a name='haut_page'></a><br />";
	
	gros_titre(_T('dw:titre_page_admin'));
	
	debut_gauche();
	
	debut_droite();


	debut_cadre_trait_couleur(_DIR_IMG_PACK."doc-24.gif", false, "", _T('dw:vos_doc'));
	
	// entete table
	echo "<table align='center' cellpadding='2' cellspacing='1' border='0' width='100%'>\n";
	echo "<tr bgcolor='$couleur_claire' class='verdana1'>\n";
	echo "<td width='3%'></td>\n";
	echo "<td width='56%'>"._T('fichier')."</td>\n".
		"<td width='12%'><div align='center'>"._T('compteur')."</div></td>\n".
		"<td width='29%'><div align='center'>"._T('dw:dernier_telech')."</div></td>\n";
	echo "</tr>";
	
	// article(s)
	foreach($tab as $k => $v) {
		echo "<tr><td colspan='4'>\n";
		echo "<br /><span class='arial2'><b>
			<a href='".generer_url_ecrire("articles","id_article=".$id_art)."'>".$v['titre']."</a>
			</b></span>\n";
		echo "</td></tr>\n";

		// les docs : nom fichier, compteur, dernier telech !
		foreach($v['doc'] as $x => $y) {
			$ifond = $ifond ^ 1;
			$bgcolor = ($ifond) ? '#EFEFEF' : $couleur_claire ;
			
			echo "<tr bgcolor='$bgcolor'>\n";
			echo "<td></td>\n";
			echo "<td><div class='verdana2'>".$y[0]."</div></td>\n"; //nomfichier
			echo "<td><div align='center' class='arial2'><b>".$y[1]."</b></div></td>\n"; //total
			echo "<td><div align='center' class='verdana2'>".$y[2]."</div></td>\n"; //dateur
			echo "</tr>";
		}
	}
	echo "</table><br />\n";	

	fin_cadre_trait_couleur();



	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();

}
?>
