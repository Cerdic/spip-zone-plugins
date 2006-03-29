<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/article_update');
include_spip('inc/rubriques');
include_spip('inc/documents');
include_spip('inc/barre');

include_once(_DIR_RESTREINT.'exec/articles_edit.php');


function exec_affiche_articles_edit($row, $lier_trad, $new, $champs_article) {
	global $champs_extra;
	$id_article = $row['id_article'];
	$id_rubrique = $row['id_rubrique'];
	$titre = $row['titre'];
	$soustitre = $row['soustitre'];
	$surtitre = $row['surtitre'];
	$descriptif = $row['descriptif'];
	$chapo = $row['chapo'];
	$texte = $row['texte'];
	$ps = $row['ps'];
	$nom_site = $row['nom_site'];
	$url_site = $row['url_site'];
	$extra = $row['extra'];
	$id_secteur = $row['id_secteur'];
	$date = $row['date'];
	$onfocus = $row['onfocus'];

	debut_page(_T('titre_page_articles_edit', array('titre' => $titre)), "documents", "articles", "hauteurTextarea();", "", $id_rubrique);

	debut_gauche();

	afficher_hierarchie($id_rubrique);

	$GLOBALS['id_article_bloque'] = $id_article;	// globale dans debut_droite
	debut_droite();
	debut_cadre_formulaire();

?>
<script language="javascript" type="text/javascript"
	src="../plugins/tinymce/js/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
function beforeSaveCallBack(element_id, html, body) {
	return '<!-- TINY_MCE -->'+html;
}

tinyMCE.init({
	mode : "exact",
	elements : "text_area,chapo",

	theme : "advanced",
	plugins : "table,save,advimage,advlink,iespell,searchreplace,contextmenu",

	theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
	theme_advanced_buttons3_add_before : "bold,italic,separator",
	theme_advanced_buttons3_add : "iespell,separator,tablecontrols",

	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",

	theme_advanced_disable : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontselect,fontsizeselect,styleselect,sub,sup,forecolor,backcolor,newdocument",

	save_callback : "beforeSaveCallBack"
});
</script>

<!--
????? OU PLACER UNE OPTION tinyMCE / textarea ???
-->

<?php

	$texte= propre($texte);
	$chapo= propre($chapo);

	formulaire_articles_edit($id_article, $id_rubrique, $titre, $soustitre, $surtitre, $descriptif, $chapo, $texte, $ps, $new, $nom_site, $url_site, $extra, $id_secteur, $date, $onfocus, $lier_trad, $champs_article);
	fin_cadre_formulaire();

	fin_page();
}


function exec_articles_edit()
{
	$row = article_update(_request('id_article'), _request('id_rubrique'), _request('lier_trad'), _request('new'));

	if (!$row) die ("<h3>"._T('info_acces_interdit')."</h3>");

	exec_affiche_articles_edit($row, $lier_trad, $new,$GLOBALS['meta']);
}

?>
