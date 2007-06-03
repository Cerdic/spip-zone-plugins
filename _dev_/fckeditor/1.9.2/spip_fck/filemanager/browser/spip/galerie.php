<?php

	$chemin = "../../../../../../";
	$cheminEcrire = $chemin."config/";
	$cheminImages = $chemin."dist/images/";
	if (defined("_ECRIRE_INC_VERSION")) return;
	define("_ECRIRE_INC_VERSION", "1");
	function spip_connect_db($host, $port, $login, $pass, $db) {
		global $fck_mysql_link;	// pour connexions multiples
		$fck_mysql_link = @mysql_connect($host, $login, $pass);
		mysql_select_db($db);
	}
	include ($cheminEcrire.'connect.php');
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="<?php echo $chemin; ?>spip.php?page=style_prive&amp;couleur_claire=C0CAD4&amp;couleur_foncee=85909A&amp;ltr=left" >
		<script type='text/javascript'><!--
			var admin = 0;
			var stat = 1;
			var largeur_icone = 110;
			var  bug_offsetwidth = 0 ;
		
			var confirm_changer_statut = 'confirm_changer_statut';
		//--></script>
		<script type="text/javascript" language="JavaScript">
		
		function addDoc( fileUrl )
		{
			window.top.opener.GetE('cmbLinkProtocol').value = '' ;
			window.top.opener.SetUrl( fileUrl ) ;
			window.top.close() ;
			window.top.opener.focus() ;
		}
		function show(id) {
			layer = document.getElementById(id);
			if(layer.style.display == 'none' || layer.style.display == '') layer.style.display = 'block';
			else layer.style.display = 'none';
		}
				
		</script>
		<style type="text/css">
		body {
			font-size: 11px;
			margin-left: 15px;
			margin-top: 15px;
		}
		td {
			font-size: 11px;
		}
		.fleche {
			padding-top: 8px;	
		}
		a:link, a:visited {
			text-decoration: none;
			color: #000000;
		}
		a:hover, a:active {
			text-decoration: underline;
		}
		.indent {
			padding-left: 30px;	
		}
		.invisible {
			display: none;	
		}
		.puce {
			margin-right: 5px;
			border: 1px solid #000;
		}
		</style>
</head>

<body>
<?php


list($data, $nbDocsTotal) = sous_arborescence(0);

echo $data;

function sous_arborescence($id_rubrique) {
	global $cheminEcrire;
	global $cheminImages;

	$image_article = array('prepa'=>'blanche', 'prop'=>'orange', 'publie'=>'verte', 'poubelle'=>'poubelle', 'refuse'=>'rouge');
	$nbDocsTotal = 0;
	
	$sousRubriques = mysql_query("SELECT id_rubrique, titre FROM spip_rubriques WHERE id_parent = $id_rubrique ORDER BY titre");
	$nbSousRubriques = mysql_num_rows($sousRubriques);

	$documentsRubrique = mysql_query("SELECT id_document FROM spip_documents_rubriques WHERE id_rubrique = $id_rubrique");
	$nbDocumentsRubrique = mysql_num_rows($documentsRubrique);
	$nbDocsTotal += $nbDocumentsRubrique;

	$articles = mysql_query("SELECT id_article AS nb, titre, id_article, statut FROM spip_articles WHERE id_rubrique = ".$id_rubrique." GROUP BY id_article ORDER BY titre");
	$nbArticles = mysql_num_rows($articles);
	
	$sites = mysql_query("SELECT id_syndic, nom_site, statut FROM spip_syndic WHERE id_rubrique = ".$id_rubrique."");
	$nbSites = mysql_num_rows($sites);
	
	$listeSites = array();
	if ($nbSites > 0) {
		while ($row = mysql_fetch_array($sites)) {
			$listeSites[] = array('id' => $row['id_syndic'], 'titre' => $row['nom_site'], 'statut' => $row['statut']);
		}
	}

	$nbDocumentsArticles = 0;
	$listeArticles = array();
	if ($nbArticles > 0) {
		while ($row = mysql_fetch_array($articles)) {
			$listeArticles[] = array('id' => $row['id_article'], 'titre' => $row['titre'], 'statut' => $row['statut'], 'nb' => $row['nb']);
			$nbDocumentsArticles += $row['nb'];
			$nbDocsTotal += $row['nb'];
		}
	}

	$retour = '';
	if (($nbSousRubriques + $nbDocumentsRubrique + $nbDocumentsArticles + $nbSites) > 0) {
		$retour .= '<div>';

		while ($row = mysql_fetch_array($sousRubriques)) {
			
			list($content, $nbDocs) = sous_arborescence($row['id_rubrique']);

			$nbDocsTotal += $nbDocs;
			$retour .= '<div class="indent">';
			if ($content != '') {
				$retour .= '<a href="#" onclick="show(\'rub'.$row['id_rubrique'].'\');"><img src="'.$cheminImages.'triangle-droite.gif" width="8" height="8" class="puce"></a>';
			} else {
				$retour .= '<img src="'.$cheminImages.'rien.gif" width="16" height="14" />';
			}
			$retour .= '<img src="'.$cheminImages.'rubrique-24.gif" align="absbottom" /> ';
			$retour .= '<a href="javascript:addDoc(\'spip.php?rubrique'.$row['id_rubrique'].'\');" title="Lier la rubrique">' . $row['titre'].' <img src="'.$cheminImages.'plus.gif" align="bottom" border="0" /> <img src="'.$cheminImages.'rubrique-12.gif" align="absbottom" border="0" /></a>';
			if ($content != '') {
				$retour .= '<div id="rub'.$row['id_rubrique'].'" class="invisible">';
				$retour .= $content;
				$retour .= '</div>';
			}
			$retour .= '</div>';
		}
		if ($nbArticles > 0) {
			reset($listeArticles);
			while (list(, $article) = each($listeArticles)) {
				
				$retour .= '<div class="indent">';
				$retour .= '<img src="'.$cheminImages.'article-24.gif" align="absbottom" /> ';
				$retour .= '<a href="javascript:addDoc(\'spip.php?article'.$article['id'].'\');" title="Lier l\'article">' . $article['titre'] . ' <img src="'.$cheminImages.'plus.gif" align="bottom" border="0" /> <img src="'.$cheminImages.'puce-'.$image_article[$article['statut']].'.gif" align="bottom" border="0" /></a>';
				$retour .= '</div>';
			}
		}
		if($nbSites > 0) {
			reset($listeSites);
			while (list(, $site) = each($listeSites)) {
				$retour .= '<div class="indent">';
				$retour .= '<img src="'.$cheminImages.'site-24.gif" align="absbottom" /><a href="javascript:addDoc(\'spip.php?site'.$site['id'].'\');" title="Lier l\'outil">';
				$retour .= $site['titre'].' ';
				$retour .= '<img src="'.$cheminImages.'plus.gif" align="bottom" border="0" /> <img src="'.$cheminImages.'puce-'.$image_article[$site['statut']].'.gif" align="bottom" border="0" /></a>';
				$retour .= '</div>';
			}
		}
		$retour .= '</div>';
	}
	mysql_free_result($sousRubriques);
	mysql_free_result($documentsRubrique);
	
	return array($retour, $nbDocsTotal);
}
?>


</body>
</html>