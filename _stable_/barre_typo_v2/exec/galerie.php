<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/*
SCRIPT ORIGINAL POUR SPIP 1.7.2
http://www.gasteroprod.com/la-galerie-spip-pour-reutiliser-facilement-les-images-et-documents.html

remplacer test_layer()	par  ???
*/

include_spip('inc/minipres');
include_spip('inc/presentation');
include_spip('inc/documents');

if(!function_exists('test_layer')) { function test_layer() { return $GLOBALS['browser_layer']; } }
if(function_exists('bouton_block_depliable')) @define('_deplie193', '1');

function exec_galerie() {
	global $connect_toutes_rubriques,$connect_id_auteur, $connect_statut;
	global $spip_dir_lang, $spip_lang, $browser_layer,$spip_lang_right,$spip_lang_left;
	
	$GLOBALS['blocks'] = array();
	$GLOBALS['blocksDocs'] = array();
	$GLOBALS['blocksPleins'] = array();

	echo install_debut_html(_T('bartypenr:galerie'));

	// les pliages utilisent desormais jQuery
	if(version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) echo '<script src="../dist/javascript/jquery.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../spip.php?page=style_prive&amp;ltr=left&amp;couleur_claire=C5E41C&amp;couleur_foncee=9DBA00" id="cssprivee" />
<!--[if lt IE 8]>
<link rel="stylesheet" type="text/css" href="../spip.php?page=style_prive_ie&amp;ltr=left&amp;couleur_claire=C5E41C&amp;couleur_foncee=9DBA00" />
<![endif]-->';
	echo '<script type="text/javascript" src="../dist/javascript/layer.js"></script>
<table width="100%" border="0" cellpadding="5" cellspacing="0" style="text-align:'.$spip_lang_left.';"><tr><td>';
	list($data, $nbDocsTotal) = sous_arborescence(0);
	?>
	<script type="text/javascript" language="JavaScript" >
	<!--
	function addDoc(id_doc, alignement) {
		//top.opener.zone_selection
		//window.opener.barre_inserer('\n<doc' + id_doc + '|' + alignement + '>\n', window.opener.<?php echo $_GET['field']; ?>);
		window.opener.barre_inserer('\n<doc' + id_doc + '|' + alignement + '>\n', top.opener.zone_selection );
		window.close();

		return true;
	}
	-->
	</script>
	<?php
	if (!test_layer()) {	?>
		<script type="text/javascript" language="JavaScript" ><!--
		function showAll() {
<?php
			reset($GLOBALS['blocks']);
			while (list(, $v) = each($GLOBALS['blocks'])) 
				echo "\t\touvrir_couche('$v', '$spip_lang_rtl','"._DIR_IMG_PACK."');\n"; 
?>
			showDocs();
		}
		
		function hideAll() {
<?php
			reset($GLOBALS['blocks']);
			while (list(, $v) = each($GLOBALS['blocks']))
				echo "\t\tfermer_couche('$v', '$spip_lang_rtl','"._DIR_IMG_PACK."');\n"; 
?>
			hideDocs();
		}

		function showNice()	{
			hideAll();
				<?php
			reset($GLOBALS['blocksPleins']);
			while (list(, $v) = each($GLOBALS['blocksPleins'])) {
				?>
				ouvrir_couche('<?php echo $v; ?>', '<?php echo $spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
			showDocs();
		}

		function showDocs()	{
				<?php
			reset($GLOBALS['blocksDocs']);
			while (list(, $v) = each($GLOBALS['blocksDocs'])) {
				?>
				ouvrir_couche('<?php echo $v; ?>', '<?php echo $spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
		}

		function hideDocs(){
				<?php
			reset($GLOBALS['blocksDocs']);
			while (list(, $v) = each($GLOBALS['blocksDocs'])) {
				?>
				fermer_couche('<?php echo $v; ?>', '<?php echo $spip_lang_rtl?>','<?php echo _DIR_IMG_PACK; ?>');
				<?php
			}
			?>
		}
		--></script>
		<p>
		<?php echo _T('bartypenr:galerie_deplier'); ?> 
		<a href="javascript:showAll();"><?php echo _T('bartypenr:galerie_tout'); ?></a> -
		<a href="javascript:showNice();"><?php echo _T('bartypenr:galerie_docs'); ?></a>
		<br />
		<?php echo _T('bartypenr:galerie_replier'); ?>
		<a href="javascript:hideAll();"><?php echo _T('bartypenr:galerie_tout'); ?></a> -
		<a href="javascript:hideDocs();"><?php echo _T('bartypenr:galerie_docs'); ?></a>
		</p>
		<?php
	}

	echo $data;
	echo '</td></tr></table>';
	echo install_fin_html();
}

function afficher_un_document_nx($id_document){
	global $connect_id_auteur, $connect_statut;
	echo "<hr>$id_document";
	return afficher_case_document($id_document, $id, $script, $type, $deplier=false);
}
function afficher_un_document($id_document){
	global $connect_id_auteur, $connect_statut;
	// compatibilite SPIP 1.92
	$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';

	$document = $fetch(spip_query("SELECT * FROM spip_documents WHERE id_document = " . intval($id_document)));

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
	if ($type = @$fetch($result))	{
		$type_extension = $type['extension'];
		$type_inclus = $type['inclus'];
		$type_titre = $type['titre'];
	}

	$retour = '';
	$titre2 = '<img src="'._DIR_IMG_PACK.'doc-24.gif" style="vertical-align:bottom;" alt="" /> '.$titre;
	if(defined('_deplie193'))
		$bouton = '<tr><td>&nbsp;</td><td valign="top">'.bouton_block_depliable($titre, false, 'doc'.$id_document);
		else $bouton = '<tr><td valign="top">'.bouton_block_invisible('doc'.$id_document).'</td><td valign="top">'.$titre2;
	if (test_layer()) {
		$idBlock = ereg_replace(".*triangle([0-9]+)[^0-9].*", "\\1", $bouton);
		$GLOBALS['blocksDocs'][] = $idBlock;
	}
	$retour .= $bouton;
	if(defined('_deplie193')) // fonction de SPIP 1.93
		$retour .= debut_block_depliable(false, 'doc'.$id_document);
		else $retour .= debut_block_invisible('doc'.$id_document);
	$retour .= '<div style="border: 1px dashed #666666; padding: 5px; background-color: #f0f0f0;">';
	$retour .= '<table border="0" cellspacing="3" cellpadding="3"><tr><td rowspan="'.(_GALERIE_MODE ? 5 : 4).'" valign="top">';
	$retour .= '<a href="'.$fichier.'" target="_blank">'.document_et_vignette($document, $url, true).'</a>';
	$retour .= '</td>';

	$retour .= '<th align="right" valign="top">'._T('bartypenr:galerie_fichier').'</th>';
	$retour .= '<td valign="top"><a href="'.$fichier.'">'.$fichier.'</a></td></tr>';
	
	$retour .= '<tr><th align="right" valign="top">'._T('bartypenr:galerie_type').'</th>';
	$retour .= '<td valign="top">'.($type_titre ? $type_titre : majuscules($type_extension)).'</td></tr>';

	$retour .= '<tr><th align="right" valign="top">'._T('bartypenr:galerie_taille').'</th>';
	$retour .= '<td valign="top">'.taille_en_octets($taille).'</td></tr>';

	$retour .= '<tr><th align="right" valign="top">'._T('bartypenr:galerie_descrip').'</th>';
	$retour .= '<td valign="top">'.($descriptif ? propre($descriptif) : _T('bartypenr:galerie_aucun')).'</td></tr>';

	$retour .= '<tr><th align="right" valign="top">'._T('bartypenr:galerie_ajouter').'</th>';
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
	// compatibilite SPIP 1.92
	$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
	$sql_count=function_exists('spip_num_rows')?'spip_num_rows':'sql_count';
	
	$sousRubriques = spip_query("SELECT id_rubrique, titre FROM spip_rubriques WHERE id_parent = $id_rubrique ORDER BY titre");
	$nbSousRubriques = $sql_count($sousRubriques);

	$documentsRubrique = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE id_rubrique = $id_rubrique");
	$nbDocumentsRubrique = $sql_count($documentsRubrique);
	$nbDocsTotal += $nbDocumentsRubrique;

	$articles = spip_query("SELECT DISTINCT a.id_article, a.titre, COUNT(d.id_document) AS nb FROM spip_articles a, spip_documents_articles d WHERE a.id_article = d.id_article AND a.id_rubrique = ".$id_rubrique." GROUP BY a.id_article ORDER BY a.titre");
	$nbArticles = $sql_count($articles);

	$nbDocumentsArticles = 0;
	$listeArticles = array();
	if ($nbArticles > 0) {
		while ($row = $fetch($articles)) {
			$listeArticles[] = array('id' => $row['id_article'], 'titre' => $row['titre'], 'nb' => $row['nb']);
			$nbDocumentsArticles += $row['nb'];
			$nbDocsTotal += $row['nb'];
		}
	}

	$retour = '';
	if (($nbSousRubriques + $nbDocumentsRubrique + $nbDocumentsArticles) > 0) {
		$retour .= '<table border="0" cellpadding="3" cellspacing="1">';
		while ($row = $fetch($sousRubriques)) {
			$tmpid = $row['id_rubrique'];
			list($content, $nbDocs) = sous_arborescence($tmpid);
			$nbDocsTotal += $nbDocs;
			$titre = '<img src="'._DIR_IMG_PACK.'rubrique-24.gif" style="vertical-align:bottom;" alt="" /> '
				. $row['titre'].' ('.$nbDocs._T('bartypenr:galerie_document').($nbDocs > 1 ? 's' : '').')';
			if ($content != '') {
				if(defined('_deplie193'))
					$bouton = '<tr><td>&nbsp;</td><td valign="top">'.bouton_block_depliable($titre, false, 'rub'.$row['id_rubrique']);
					else $bouton = '<tr><td valign="top">'.bouton_block_invisible('rub'.$row['id_rubrique']).'</td><td valign="top">'.$titre;
				if (test_layer()) {
					$idBlock = ereg_replace(".*triangle([0-9]+)[^0-9].*", "\\1", $bouton);
					$GLOBALS['blocks'][] = $idBlock;
					if ($nbDocs > 0)
						$GLOBALS['blocksPleins'][] = $idBlock;
				}
				$retour .= $bouton;
			} else {
				$retour .= '<img src="'._DIR_IMG_PACK.'rien.gif" width="16" height="14" alt="" />';
			}
			if ($content != '') {
				$retour .= '<br />';
				if(defined('_deplie193')) // fonction de SPIP 1.93
					$retour .= debut_block_depliable(false, 'rub'.$row['id_rubrique']);
					else $retour .= debut_block_invisible('rub'.$row['id_rubrique']);
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
				$nbDocumentsArticle = $sql_count($documentsArticles);
				$titre = '<img src="'._DIR_IMG_PACK.'article-24.gif" style="vertical-align:bottom;" alt="" /> '
					. $article['titre'].' ('.$article['nb']._T('bartypenr:galerie_document').($article['nb'] > 1 ? 's' : '').')';
//				$retour .= '<tr><td valign="top">';
				if(defined('_deplie193')) // fonction de SPIP 1.93
					$bouton = '<tr><td>&nbsp;</td><td valign="top">'.bouton_block_depliable($titre, false, 'art'.$article['id']);
					else $bouton = '<tr><td valign="top">'.bouton_block_invisible('art'.$article['id']).'</td><td valign="top">'.$titre;
//		$bouton = bouton_block_depliable('', false, 'art'.$article['id']);
//		else $bouton = bouton_block_invisible('art'.$article['id']);
				if (test_layer()) {
					$idBlock = ereg_replace(".*triangle([0-9]+)[^0-9].*", "\\1", $bouton);
					$GLOBALS['blocks'][] = $idBlock;
					$GLOBALS['blocksPleins'][] = $idBlock;
				}
				$retour .= $bouton;
//		$retour .= '</td><td valign="top"><img src="'._DIR_IMG_PACK.'article-24.gif" style="vertical-align:bottom;" alt="" /> ';
//		$retour .= $article['titre'].' ('.$article['nb']._T('bartypenr:galerie_document').($article['nb'] > 1 ? 's' : '').')';
				//$retour .= $listeArticles[$i]['titre'].' ('.$listeArticles[$i]['nb'].' document'.($listeArticles[$i]['nb'] > 1 ? 's' : '').')';
				$retour .= '<br />';
				if(defined('_deplie193')) // fonction de SPIP 1.93
					$retour .= debut_block_depliable(false, 'art'.$article['id']);
					else $retour .= debut_block_invisible('art'.$article['id']);
				$retour .= '<table border="0" cellpadding="3" cellspacing="1">';
				while ($doc = $fetch($documentsArticle)) {
					$retour .= afficher_un_document($doc['id_document']);
				}
				$retour .= '</table>';
				$retour .= fin_block();
				$retour .= '</td></tr>';
			}
		}
		while ($row = $fetch($documentsRubrique)) {
			$retour .= afficher_un_document($row['id_document']);
		}
		$retour .= '</table>';
	}

	spip_free_result($sousRubriques);
	spip_free_result($documentsRubrique);
	return array($retour, $nbDocsTotal);
}



?>