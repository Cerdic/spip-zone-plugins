<?php

/*
 * gestion_documents
 *
 * interface de gestion des documents
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */
include_spip('inc/afficher_objets');

if (!defined('_DIR_PLUGIN_GESTIONDOCUMENTS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_GESTIONDOCUMENTS',(_DIR_PLUGINS.end($p)));
}


// Fonction appelee dans une boucle, calculer les invariants au premier appel.

// http://doc.spip.org/@inc_formater_article_dist
function inc_formater_article($row){
	global $options, $spip_lang_right, $spip_display;
	static $pret = false;
	static $chercher_logo, $img_admin, $formater_auteur, $nb, $langue_defaut, $afficher_langue;

	if (!$pret) {
		$chercher_logo = ($spip_display != 1 AND $spip_display != 4 AND $GLOBALS['meta']['image_process'] != "non");
		if ($chercher_logo) 
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
		$formater_auteur = charger_fonction('formater_auteur', 'inc');
		$img_admin = http_img_pack("admin-12.gif", "", " width='12' height='12'", _T('titre_image_admin_article'));

		if (($GLOBALS['meta']['multi_rubriques'] == 'oui' AND (!isset($GLOBALS['id_rubrique']))) OR $GLOBALS['meta']['multi_articles'] == 'oui') {
			$afficher_langue = true;
			$langue_defaut = !isset($GLOBALS['langue_rubrique'])
			  ? $GLOBALS['meta']['langue_site']
			  : $GLOBALS['langue_rubrique'];
		}
		$pret = true;
	}

	$id_article = $row['id_article'];

	if ($chercher_logo) {
		if ($logo = $chercher_logo($id_article, 'id_article', 'on')) {
			list($fid, $dir, $nom, $format) = $logo;
			include_spip('inc/filtres_images');
			$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
		}
	} else $logo ='';

	$vals = array();

	$titre = sinon($row['titre'], _T('ecrire:info_sans_titre'));
	$id_rubrique = $row['id_rubrique'];
	$date = $row['date'];
	$statut = $row['statut'];
	$descriptif = $row['descriptif'];
	$lang_dir = lang_dir(($lang = $row['lang']) ? changer_typo($lang):'');

	$vals[]= "<input type=\"checkbox\" name=\"lista_articulos[]\" value=\"".$id_article."\" />";

	$vals[]= "<div>"
	. "<a href='"
	. generer_url_ecrire("articles","id_article=$id_article")
	. "'"
	. (!$descriptif ? '' : 
	     (' title="'.attribut_html(typo($descriptif)).'"'))
	. " dir='$lang_dir'>"
	. (!$logo ? '' :
	   ("<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>" . $logo . "</span>"))
	. (acces_restreint_rubrique($id_rubrique) ? $img_admin : '')
	. typo($titre)
	. (!($afficher_langue AND $lang != $GLOBALS['meta']['langue_site']) ? '' :
	   (" <span class='spip_xx-small' style='color: #666666' dir='$lang_dir'>(".traduire_nom_langue($lang).")</span>"))
	  . (!$row['petition'] ? '' :
	     ("</a> <a href='" . generer_url_ecrire('controle_petition', "id_article=$id_article") . "' class='spip_xx-small' style='color: red'>"._T('lien_petitions')))
	. "</a>"
	. "</div>";
	
	$result = auteurs_article($id_article);
	$les_auteurs = array();
	while ($r = spip_fetch_array($result)) {
		list($s, $mail, $nom, $w, $p) = $formater_auteur($r['id_auteur']);
		$les_auteurs[]= "$mail&nbsp;$nom";
	}
	$vals[] = join('<br />', $les_auteurs);

	$s = affdate_jourcourt($date);
	$vals[] = $s ? $s : '&nbsp;';

	$vals[]= afficher_numero_edit($id_article, 'id_article', 'article');

	// Afficher le numero (JMB)
	  $largeurs = array(11, '', 80, 100, 50);
	  $styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');

	return ($spip_display != 4)
	? afficher_liste_display_neq4($largeurs, $vals, $styles)
	: afficher_liste_display_eq4($largeurs, $vals, $styles);
}



function generer_query_string($conteneur,$id_type,$nb_aff,$filtre){
  $query = ($conteneur?"conteneur=$conteneur&":"")
		.($id_type?"id_type=$id_type&":"")
		.(($nb_aff)?"nb_aff=$nb_aff&":"")
		.(($filtre)?"filtre=$filtre&":"");

  return $query;
}	

charger_generer_url();

function exec_edicion_masa(){
	global $updatetable;
	global $connect_statut,  $connect_id_auteur;
	//global $modif;
	
	include_spip ("inc/presentation");
	include_spip ("inc/documents");
	include_spip('inc/indexation');
	include_spip ("inc/logos");
	include_spip ("inc/session");

	//
	// Recupere les donnees
	//

	debut_page(_T("edicion:tous_docs"), "documents", "documents");
	debut_gauche();


	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//

	debut_boite_info();

	echo propre(_T('edicion:info_doc'));

	fin_boite_info();

	global $connect_statut;
	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}
	
creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'articles_page'),'data'=>''));
debut_droite();
echo _request('filtro');
echo afficher_objets('article',_T('Tu seleccion'),	array("FROM" =>"spip_articles AS articles", 'ORDER BY' => "articles.date DESC"));
//echo pipeline('affiche_milieu',array('args'=>array('exec'=>'articles_page'),'data'=>''));



}

?>
