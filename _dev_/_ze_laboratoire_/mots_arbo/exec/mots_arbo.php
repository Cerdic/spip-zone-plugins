<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');
include_spip('base/abstract_sql');

function exec_mots_arbo_dist() {
	global $parentId;
	if($parentId) {
		afficher_sous_arbo($parentId);
		return;
	}

global $acces_comite, $acces_forum, $acces_minirezo, $ancien_type, $articles, $breves, $change_type, $conf_mot, $connect_statut, $connect_toutes_rubriques, $descriptif, $id_groupe, $modifier_groupe, $obligatoire, $rubriques, $spip_lang, $spip_lang_right, $supp_group, $syndic, $texte, $unseul;

$id_groupe = intval($id_groupe);

if ($conf_mot = intval($conf_mot)) {
	$result = spip_query("SELECT * FROM spip_mots WHERE id_mot=$conf_mot");
	if ($row = spip_fetch_array($result)) {
		$id_mot = $row['id_mot'];
		$titre_mot = typo($row['titre']);
		$type_mot = typo($row['type']);

		if ($connect_statut=="0minirezo") $aff_articles="prepa,prop,publie,refuse";
		else $aff_articles="prop,publie";

		list($nb_articles) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_mots_articles AS lien, spip_articles AS article WHERE lien.id_mot=$conf_mot AND article.id_article=lien.id_article AND FIND_IN_SET(article.statut,'$aff_articles')>0 AND article.statut!='refuse'"),SPIP_NUM);

		list($nb_rubriques) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_mots_rubriques AS lien, spip_rubriques AS rubrique WHERE lien.id_mot=$conf_mot AND rubrique.id_rubrique=lien.id_rubrique"),SPIP_NUM);
		list($nb_breves) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_mots_breves AS lien, spip_breves AS breve WHERE lien.id_mot=$conf_mot AND breve.id_breve=lien.id_breve AND FIND_IN_SET(breve.statut,'$aff_articles')>0 AND breve.statut!='refuse'"),SPIP_NUM);

		list($nb_sites) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_mots_syndic AS lien, spip_syndic AS syndic WHERE lien.id_mot=$conf_mot AND syndic.id_syndic=lien.id_syndic	AND FIND_IN_SET(syndic.statut,'$aff_articles')>0 AND syndic.statut!='refuse'"),SPIP_NUM);

		list($nb_forum) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_mots_forum AS lien, spip_forum AS forum WHERE lien.id_mot=$conf_mot AND forum.id_forum=lien.id_forum AND forum.statut='publie'"),SPIP_NUM);

		// si le mot n'est pas lie, on demande sa suppression
		if ($nb_articles + $nb_breves + $nb_sites + $nb_forum == 0) {
		  redirige_par_entete(generer_url_ecrire("mots_edit","supp_mot=$id_mot&redirect_ok=oui&redirect=" . rawurlencode(generer_url_ecrire('mots_tous')), true));
		} // else traite plus loin (confirmation de suppression)
	}
}


if ($connect_statut == '0minirezo'  AND $connect_toutes_rubriques) {
	if ($modifier_groupe == "oui") {
		$change_type = addslashes(corriger_caracteres($change_type));
		$ancien_type = addslashes(corriger_caracteres($ancien_type));
		$texte = addslashes(corriger_caracteres($texte));
		$descriptif = addslashes(corriger_caracteres($descriptif));
		$unseul = addslashes($unseul);
		$obligatoire = addslashes($obligatoire);
		$articles = addslashes($articles);
		$breves = addslashes($breves);
		$rubriques = addslashes($rubriques);
		$syndic = addslashes($syndic);
		$acces_minirezo = addslashes($acces_minirezo);
		$acces_comite = addslashes($acces_comite);
		$acces_forum = addslashes($acces_forum);
		if ($ancien_type) {	// modif groupe
			spip_query("UPDATE spip_mots SET type='$change_type' WHERE id_groupe='$id_groupe'");


			spip_query("UPDATE spip_groupes_mots SET titre='$change_type', texte='$texte', descriptif='$descriptif', unseul='$unseul', obligatoire='$obligatoire',
				articles='$articles', breves='$breves', rubriques='$rubriques', syndic='$syndic',
				minirezo='$acces_minirezo', comite='$acces_comite', forum='$acces_forum'
				WHERE id_groupe='$id_groupe'");

		} else {	// creation groupe
		  spip_abstract_insert('spip_groupes_mots',
				       "(titre, texte, descriptif, unseul,  obligatoire, articles, breves, rubriques, syndic, minirezo, comite, forum)",
				       "('$change_type', '$texte', '$descriptif', '$unseul', '$obligatoire', '$articles','$breves', '$rubriques', '$syndic', '$acces_minirezo',  '$acces_comite', '$acces_forum')");
		}
	}
	if ($supp_group){
		spip_query("DELETE FROM spip_groupes_mots WHERE id_groupe='" . addslashes($supp_group) ."'");
	}
 }


debut_page(_T('titre_page_mots_tous'), "documents", "mots");
debut_gauche();

debut_droite();

gros_titre(_T('titre_mots_tous'));
if ($connect_statut == '0minirezo'  AND $connect_toutes_rubriques) {
  echo typo(_T('info_creation_mots_cles')) . aide ("mots") ;
  }
echo "<br><br>";

/////

if ($conf_mot>0) {
	if ($nb_articles == 1) {
		$texte_lie = _T('info_un_article')." ";
	} else if ($nb_articles > 1) {
		$texte_lie = _T('info_nombre_articles', array('nb_articles' => $nb_articles)) ." ";
	}
	if ($nb_breves == 1) {
		$texte_lie .= _T('info_une_breve')." ";
	} else if ($nb_breves > 1) {
		$texte_lie .= _T('info_nombre_breves', array('nb_breves' => $nb_breves))." ";
	}
	if ($nb_sites == 1) {
		$texte_lie .= _T('info_un_site')." ";
	} else if ($nb_sites > 1) {
		$texte_lie .= _T('info_nombre_sites', array('nb_sites' => $nb_sites))." ";
	}
	if ($nb_rubriques == 1) {
		$texte_lie .= _T('info_une_rubrique')." ";
	} else if ($nb_rubriques > 1) {
		$texte_lie .= _T('info_nombre_rubriques', array('nb_rubriques' => $nb_rubriques))." ";
	}

	debut_boite_info();
	echo "<div class='serif'>";
	echo _T('info_delet_mots_cles', array('titre_mot' => $titre_mot, 'type_mot' => $type_mot, 'texte_lie' => $texte_lie));

	echo "<UL>";
	echo "<LI><B><A href='", generer_url_ecrire('mots_edit', "supp_mot=$id_mot&redirect_ok=oui&redirect=" . rawurlencode(generer_url_ecrire('mots_tous'))),
	  "'>",
	  _T('item_oui'),
	  "</A>,</B> ",
	  _T('info_oui_suppression_mot_cle');
	echo "<LI><B><A href='" . generer_url_ecrire("mots_tous","") . "'>"._T('item_non')."</A>,</B> "._T('info_non_suppression_mot_cle');
	echo "</UL>";
	echo "</div>";
	fin_boite_info();
	echo "<P>";
}

// A PARTIR D'ICI CA CHANGE PAR RAPPORT A L'ADMIN STANDARD

	$result_groupes = spip_query("SELECT *, ".creer_objet_multi ("titre", "$spip_lang")." FROM spip_groupes_mots ORDER BY multi");

	echo "\n<script type='text/javascript'>
	var idOfFolderTrees = ['arrangableNodes'];\n
	var imageFolder = '".dirname(find_in_path('images/dhtmlgoodies_plus.gif'))."/';	// Path to images
	var ajaxRequestFile = '?exec=mots_arbo&';

	var onloads = '';

	function doOnloads() {
//alert('onloads='+onloads);
		eval(onloads);
	}

	window.onload= doOnloads;
</script>";

	echo "\n<link rel='stylesheet' href='"
		.find_in_path('css/manip-tree.css')."' type='text/css'>";
	echo "\n<script type='text/javascript' src='"
		.find_in_path('js/manip-tree.js')."'></script>";

	echo "\n<link rel='stylesheet' href='"
		.find_in_path('css/folder-tree-static.css')."' type='text/css'>";
	echo "\n<script type='text/javascript' src='"
		.find_in_path('js/ajax.js')."'></script>";
	echo "\n<script type='text/javascript' src='"
		.find_in_path('js/folder-tree-static.js')."'></script>";

	echo "\n<div id='whereami'></div>";	
	echo "\n<div id='movableNode'><ul></ul></div>";	
	echo "\n<div id='arrDestIndicator'><img src='img_pack/deplierhaut.gif'></div>";
	echo "\n<form action='../spip.php?action=bouger_mots' method='post'>";
	echo "\n<input type='hidden' name='redirect' value='ecrire?exec=mots_arbo'/>";
	echo "\n<ul id='arrangableNodes' class='dhtmlgoodies_tree'>Groupes de mots clés :";

	while ($row_groupes = spip_fetch_array($result_groupes)) {
		$id_groupe = $row_groupes['id_groupe'];
		$titre_groupe = typo($row_groupes['titre']);
		$descriptif = $row_groupes['descriptif'];
		$texte = $row_groupes['texte'];
		$unseul = $row_groupes['unseul'];
		$obligatoire = $row_groupes['obligatoire'];
		$articles = $row_groupes['articles'];
		$breves = $row_groupes['breves'];
		$rubriques = $row_groupes['rubriques'];
		$syndic = $row_groupes['syndic'];
		$acces_minirezo = $row_groupes['minirezo'];
		$acces_comite = $row_groupes['comite'];
		$acces_forum = $row_groupes['forum'];

		$options=array(array(), array(), array());
		if ($articles == "oui") $options[0][]= _T('info_articles_2');
		if ($breves == "oui") $options[0][]= _T('info_breves_2');
		if ($rubriques == "oui") $options[0][]= _T('info_rubriques');
		if ($syndic == "oui") $options[0][]= _T('info_sites_references');
	
		if ($unseul == "oui") $options[1][]= _T('info_un_mot');
		if ($obligatoire == "oui") $options[1][]=_T('info_groupe_important');

		if ($acces_minirezo == "oui") $options[2][]= _T('info_administrateurs');
		if ($acces_comite == "oui") $options[2][]= _T('info_redacteurs');
		if ($acces_forum == "oui") $options[2][]= _T('info_visiteurs_02');

		$options[0]= join(',&nbsp;', $options[0]);
		$options[1]= join(',&nbsp;', $options[1]);
		$options[2]= join(',&nbsp;', $options[2]);
		$options= join('&nbsp;/&nbsp;', $options);

		if($options) {
			$options= " <font face='Verdana,Arial,Sans,sans-serif' size=1>($options)</font>\n";
		}

		echo "\n\t<li><a href='#'>$titre_groupe $options</a>\n";
		echo "\n\t<ul>\n\t\t<li parentId='li_G$id_groupe'><a href='#'>Loading ...</a></li>\n\t</ul>";

		echo "\n\t</li>";
	}
	echo "\n</ul>";

	echo "\n<input type='submit'>\n</form>";

	if ($connect_statut =="0minirezo"  AND $connect_toutes_rubriques  AND !$conf_mot) {
		echo "<p>&nbsp;</p><div align='right'>";
		icone(_T('icone_creation_groupe_mots'), generer_url_ecrire("mots_type","new=oui"), "groupe-mot-24.gif", "creer.gif");
		echo "</div>";
	}

	fin_page();
}

function afficher_sous_arbo($id) {
	if(!preg_match('/li_([GM])(\d*)/', $id, $re)) {
		echo "<li><a href='#'>Can't find node $id</a></li>";
		return;
	}
	$type= $re[1]; $id= $re[2];
	if($type=='G') {
		$query = "SELECT id_mot, titre, debut, fin FROM spip_mots
		 WHERE id_groupe=$id AND niveau=1
		 ORDER BY debut";
	} else {
		$query = "SELECT m2.id_mot, m2.titre, m2.debut, m2.fin
		  FROM spip_mots m1, spip_mots m2
		 WHERE m1.id_mot=$id
		   AND m2.niveau=m1.niveau+1
		   AND m2.debut>m1.debut AND m2.fin<m1.fin ORDER BY debut";
	}
	error_log($query);
	$mots= spip_query($query);

	while($row= spip_fetch_array($mots)) {
		error_log(var_export($row, 1));
		$id= $row['id_mot'];
		$titre= $row['titre'];

		$actions= "<input type='radio' name='from' value='$id'>";
		$actions.="<input type='radio' name='after' value='$id'>";
		$actions.="<input type='radio' name='into' value='$id'>";
		echo "\n\t<li><a href='#'>$titre</a> $actions";
		if($row['debut']+1!=$row['fin']) {
			echo "\n\t<ul>\n\t\t<li parentId='li_M$id'><a href='#'>Loading ...</a></li>\n\t</ul>";
		}
		echo "\n\t</li>";
	}
}



function afficher_arbo_groupe($id_groupe) {
	$query = "SELECT id_mot, titre, niveau FROM spip_mots WHERE id_groupe = '$id_groupe' AND niveau!=0 ORDER BY debut";

	$mots= spip_query($query);
	$vide=true;
	$niveau=0;
	while($row= spip_fetch_array($mots)) {
		$vide=false;

		$id= $row['id_mot'];
		$titre= $row['titre'];
		$niv= $row['niveau'];

		while($niv<$niveau) {
			$indent=str_repeat("\t", $niveau);
			echo "\n$indent</div>";
			$niveau--;
		}
		if($niv==$niveau) {
			$indent=str_repeat("\t", $niveau);
			echo "\n$indent</div>";
		} else {
			$niveau=$niv;
			$indent=str_repeat("\t", $niveau);
		}

		$actions= "<input type='radio' name='from' value='$id'>";
		$actions.="<input type='radio' name='after' value='$id'>";
		$actions.="<input type='radio' name='into' value='$id'>";
		echo "\n$indent<div id='li_M$id'>";
		echo "$id / $titre";
		echo "<span style='align:right;'>$actions</span>";
	}

	while($niveau>0) {
		$indent=str_repeat("\t", $niveau);
		echo "\n$indent</div>";
		$niveau--;
	}
	return $vide;
}

?>
