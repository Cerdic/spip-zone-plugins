<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/*
SCRIPT ORIGINAL POUR SPIP 1.7.2
http://www.gasteroprod.com/la-galerie-spip-pour-reutiliser-facilement-les-images-et-documents.html

remplacer test_layer()	par  ???
*/
include_spip('inc/presentation');
include_spip('inc/documents');

function exec_galerie() {
	global $connect_toutes_rubriques,$connect_id_auteur, $connect_statut;
	global $spip_dir_lang, $spip_lang, $browser_layer,$spip_lang_right,$spip_lang_left;
	
	$GLOBALS['blocks'] = array();
	$GLOBALS['blocksDocks'] = array();
	$GLOBALS['blocksPleins'] = array();

	//debut_html('Galerie');
	//echo "<html><head><title>Galerie</title></head><body>";
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('galerie'), "galerie", "");

	echo "<style type=\"text/css\">
.bandeau-icones{display:none;}
.bandeau_couleur{display:none;}
#bandeau-gadgets{display:none;}
</style>";
	echo '<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>';
	debut_cadre_enfonce();
	gros_titre('Galerie de documents');
	list($data, $nbDocsTotal) = sous_arborescence(0);
	?>
	<script type="text/javascript" language="JavaScript" >
	<!--
	function addDoc(id_doc, alignement)
	{
		//top.opener.zone_selection
		//window.opener.barre_inserer('\n<doc' + id_doc + '|' + alignement + '>\n', window.opener.<?php echo $_GET['field']; ?>);
		window.opener.barre_inserer('\n<doc' + id_doc + '|' + alignement + '>\n', top.opener.zone_selection );
		window.close();

		return true;
	}
	-->
	</script>
	<?php
	if (test_layer()) {
		?>
		<script type="text/javascript" language="JavaScript" >
		<!--
		function showAll()
		{
			<?php
			reset($GLOBALS['blocks']);
			while (list(, $v) = each($GLOBALS['blocks'])) {
				?>
				ouvrir_couche('<?php echo $v; ?>', '<?=$spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
			showDocs();
		}
		
		function hideAll()
		{
			<?php
			reset($GLOBALS['blocks']);
			while (list(, $v) = each($GLOBALS['blocks'])) {
				?>
				fermer_couche('<?php echo $v; ?>', '<?=$spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
			hideDocs();
		}

		function showNice()
		{
			hideAll();
			<?php
			reset($GLOBALS['blocksPleins']);
			while (list(, $v) = each($GLOBALS['blocksPleins'])) {
				?>
				ouvrir_couche('<?php echo $v; ?>', '<?=$spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
			showDocs();
		}

		function showDocs()
		{
			<?php
			reset($GLOBALS['blocksDocs']);
			while (list(, $v) = each($GLOBALS['blocksDocs'])) {
				?>
				ouvrir_couche('<?php echo $v; ?>', '<?=$spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
		}

		function hideDocs()
		{
			<?php
			reset($GLOBALS['blocksDocs']);
			while (list(, $v) = each($GLOBALS['blocksDocs'])) {
				?>
				fermer_couche('<?php echo $v; ?>', '<?=$spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
		}
		-->
		</script>
		<p>
		Déplier : 
		<a href="javascript:showAll();">tout</a> -
		<a href="javascript:showNice();">les docs</a>
		<br />
		Replier :
		<a href="javascript:hideAll();">tout</a> -
		<a href="javascript:hideDocs();">les docs</a>
		</p>
		<?php
	}

	debut_cadre_relief('doc-24.gif');
	echo $data;
	fin_cadre_relief();
	fin_cadre_enfonce();
	echo '</td></tr></table></body></html>';
}

function afficher_un_document_nx($id_document){
	global $connect_id_auteur, $connect_statut;
	echo "<hr>$id_document";
	return afficher_case_document($id_document, $id, $script, $type, $deplier=false);
}
function afficher_un_document($id_document){
	global $connect_id_auteur, $connect_statut;
	
	$document = spip_fetch_array(spip_query("SELECT * FROM spip_documents WHERE id_document = " . intval($id_document)));
	//$document = fetch_document($id_document);

	$id_vignette = $document['id_vignette'];
	$id_type = $document['id_type'];
	$titre = $document['titre'];
	$descriptif = $document['descriptif'];
	$fichier = generer_url_document($id_document);
	$taille = $document['taille'];
	$mode = $document['mode'];

	if ($titre == '') {
		$titre = ereg_replace("^[^\/]*\/[^\/]*\/", "", $fichier);
	}

	$result = spip_query("SELECT * FROM spip_types_documents WHERE id_type=$id_type");
	if ($type = @spip_fetch_array($result))	{
		$type_extension = $type['extension'];
		$type_inclus = $type['inclus'];
		$type_titre = $type['titre'];
	}

	$retour = '';
	$bouton = bouton_block_invisible('doc'.$id_document);
	if (test_layer()) {
		$idBlock = ereg_replace(".*triangle([0-9]+)[^0-9].*", "\\1", $bouton);
		$GLOBALS['blocksDocs'][] = $idBlock;
	}
	$retour .= '<tr><td valign="top">'.$bouton.'</td>';
	$retour .= '<td><img src="'._DIR_IMG_PACK.'doc-24.gif" align="absbottom" /> '.$titre;
	$retour .= debut_block_invisible('doc'.$id_document);
	$retour .= '<div style="border: 1px dashed #666666; padding: 5px; background-color: #f0f0f0;">';
	$retour .= '<table border="0" cellspacing="3" cellpadding="3"><tr><td rowspan="'.(_GALERIE_MODE ? 5 : 4).'" valign="top">';
	$retour .= '<a href="'.$fichier.'" target="_blank">'.document_et_vignette($document, $url, true).'</a>';
	$retour .= '</td>';

	$retour .= '<th align="right" valign="top">Fichier&nbsp;:</th>';
	$retour .= '<td valign="top"><a href="'.$fichier.'">'.$fichier.'</a></td></tr>';
	
	$retour .= '<tr><th align="right" valign="top">Type&nbsp;:</th>';
	$retour .= '<td valign="top">'.($type_titre ? $type_titre : majuscules($type_extension)).'</td></tr>';

	$retour .= '<tr><th align="right" valign="top">Taille&nbsp;:</th>';
	$retour .= '<td valign="top">'.taille_en_octets($taille).'</td></tr>';

	$retour .= '<tr><th align="right" valign="top">Descriptif&nbsp;:</th>';
	$retour .= '<td valign="top">'.($descriptif ? propre($descriptif) : 'Aucun').'</td></tr>';

	$retour .= '<tr><th align="right" valign="top">Ajouter&nbsp;:</th>';
	$retour .= '<td valign="top">';
	$retour .= '<a href="javascript:addDoc('.$id_document.', \'left\');">left</a>';
	$retour .= ' | <a href="javascript:addDoc('.$id_document.', \'center\');">center</a>';
	$retour .= ' | <a href="javascript:addDoc('.$id_document.', \'right\');">right</a>';
	$retour .= '</td></tr>';

	$retour .= '</table></div>';

	$retour .= fin_block();
	$retour .= '</td></tr>';

	return $retour;
}

function sous_arborescence($id_rubrique) {
	$nbDocsTotal = 0;
	
	$sousRubriques = spip_query("SELECT id_rubrique, titre FROM spip_rubriques WHERE id_parent = $id_rubrique ORDER BY titre");
	$nbSousRubriques = spip_num_rows($sousRubriques);

	$documentsRubrique = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE id_rubrique = $id_rubrique");
	$nbDocumentsRubrique = spip_num_rows($documentsRubrique);
	$nbDocsTotal += $nbDocumentsRubrique;

	$articles = spip_query("SELECT DISTINCT a.id_article, a.titre, COUNT(d.id_document) AS nb FROM spip_articles a, spip_documents_articles d WHERE a.id_article = d.id_article AND a.id_rubrique = ".$id_rubrique." GROUP BY a.id_article ORDER BY a.titre");
	$nbArticles = spip_num_rows($articles);

	$nbDocumentsArticles = 0;
	$listeArticles = array();
	if ($nbArticles > 0) {
		while ($row = spip_fetch_array($articles)) {
			$listeArticles[] = array('id' => $row['id_article'], 'titre' => $row['titre'], 'nb' => $row['nb']);
			$nbDocumentsArticles += $row['nb'];
			$nbDocsTotal += $row['nb'];
		}
	}

	$retour = '';
	if (($nbSousRubriques + $nbDocumentsRubrique + $nbDocumentsArticles) > 0) {
		$retour .= '<table border="0" cellpadding="3" cellspacing="1">';
		while ($row = spip_fetch_array($sousRubriques)) {
			$tmpid = $row['id_rubrique'];
			list($content, $nbDocs) = sous_arborescence($tmpid);
			$nbDocsTotal += $nbDocs;
			$retour .= '<tr><td valign="top">';
			if ($content != '') {
				$bouton = bouton_block_invisible('rub'.$row['id_rubrique']);
				if (test_layer()) {
					$idBlock = ereg_replace(".*triangle([0-9]+)[^0-9].*", "\\1", $bouton);
					$GLOBALS['blocks'][] = $idBlock;
					if ($nbDocs > 0) {
						$GLOBALS['blocksPleins'][] = $idBlock;
					}
				}
				$retour .= $bouton;
			} else {

				$retour .= '<img src="'._DIR_IMG_PACK.'rien.gif" width="16" height="14" />';
			}
			$retour .= '</td><td valign="top"><img src="'._DIR_IMG_PACK.'rubrique-24.gif" align="absbottom" /> ';
			$retour .= $row['titre'].' ('.$nbDocs.' document'.($nbDocs > 1 ? 's' : '').')';
			if ($content != '') {
				$retour .= '<br />';
				$retour .= debut_block_invisible('rub'.$row['id_rubrique']);
				$retour .= $content;
				$retour .= fin_block();
			}
			$retour .= '</td></tr>';
		}
		if ($nbArticles > 0) {
			reset($listeArticles);
			
			//while (list($article) = each($listeArticles)) { // BUG while (list( , $article) = each($listeArticles)) {
			for($i=0;$i<count($listeArticles);$i++){
/*
echo "<pre>";
echo print_r($listeArticles)."<br />";
echo print_r($article)."<br />";
echo $listeArticles[0]['titre'];
echo "</pre>";
*/
$article['titre'] = $listeArticles[$i]['titre'];
$article['nb'] = $listeArticles[$i]['nb'];
$article['id'] = $listeArticles[$i]['id'];
				$documentsArticle = spip_query("SELECT id_document FROM spip_documents_articles WHERE id_article = ".$article['id']);
				$nbDocumentsArticle = spip_num_rows($documentsArticles);
				$retour .= '<tr><td valign="top">';
				$bouton = bouton_block_invisible('art'.$article['id']);
				if (test_layer()) {
					$idBlock = ereg_replace(".*triangle([0-9]+)[^0-9].*", "\\1", $bouton);
					$GLOBALS['blocks'][] = $idBlock;
					$GLOBALS['blocksPleins'][] = $idBlock;
				}
				$retour .= $bouton;
				$retour .= '</td><td valign="top"><img src="'._DIR_IMG_PACK.'article-24.gif" align="absbottom" /> ';
				$retour .= $article['titre'].' ('.$article['nb'].' document'.($article['nb'] > 1 ? 's' : '').')';
				//$retour .= $listeArticles[$i]['titre'].' ('.$listeArticles[$i]['nb'].' document'.($listeArticles[$i]['nb'] > 1 ? 's' : '').')';
				$retour .= '<br />';
				$retour .= debut_block_invisible('art'.$article['id']);
				$retour .= '<table border="0" cellpadding="3" cellspacing="1">';
				while ($doc = spip_fetch_array($documentsArticle)) {
					$retour .= afficher_un_document($doc['id_document']);
				}
				$retour .= '</table>';
				$retour .= fin_block();
				$retour .= '</td></tr>';
			}
		}
		while ($row = spip_fetch_array($documentsRubrique)) {
			$retour .= afficher_un_document($row['id_document']);
		}
		$retour .= '</table>';
	}

	spip_free_result($sousRubriques);
	spip_free_result($documentsRubrique);
	return array($retour, $nbDocsTotal);
}



?>