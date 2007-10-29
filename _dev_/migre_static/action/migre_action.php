<?php
#-----------------------------------------------------#
#  Plugin  : migre_static - Licence : GPL             #
#  File    : action/migre_action - migration process  #
#  Authors : Chryjs, 2007 - Beurt, 2006               #
#  Contact : chryjs¡@!free¡.!fr                       #
#-----------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// [fr] Etape de realisation de la migration
// [en] Migration step

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/migre"); // [fr] Charge les fonctions de migre_static [en] Loads migre_static functions
include_spip("base/abstract_sql");
include_spip('inc/rubriques');
include_spip('inc/charsets');
include_spip('inc/minipres');
include_spip("inc/presentation");

global $migre_meta;
$migre_meta = $GLOBALS['migrestatic'];

// ------------------------------------------------------------------------------
// [fr] Action principale : realise la presentation
// [en] Main action : shows everything
// ------------------------------------------------------------------------------
function action_migre_action()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_rubrique = intval($arg);
	$step = _request('etape');
	$go_back = generer_url_ecrire("naviguer","id_rubrique=$id_rubrique");
	$link_back  = icone(_T('icone_retour'), $go_back, "rubrique-12.gif", "rien.gif", ' ',false);
	$corps = $link_back;

	switch ($step) {
	case 1 :
		$debug  = migre_check_var($id_rubrique);

/* debug
		$corps .= $debug;
		$form   = "<input type='hidden' name='etape' id='etape' value='2'>";
		$form  .= "<div align='right'><input class='fondo' type='submit' value='"._T('bouton_suivant')."' /></div>" ;
		$corps .= generer_action_auteur("migre_action",$id_rubrique, $retour,$form," method='post' name='formulaire'");
		$corps .= $link_back;
		break;
	case 2 :
*/
		$corps .= migre_affiche_pages();
		$form   = "<input type='hidden' name='etape' id='etape' value='2'>";
		$form  .= "<div align='right'><input class='fondo' type='submit' value='"._T('bouton_suivant')."' /></div>" ;
		$corps .= generer_action_auteur("migre_action",$id_rubrique, $retour,$form," method='post' name='formulaire'");
		$corps .= $link_back;
		break;
	case 2 :
		$corps .= migre_pages($id_rubrique);
		$corps .= $link_back;
		break;
	default :
		$corps .= "<strong>"._T('avis_non_acces_page')."</strong>";
	}

	echo minipres(_T("migrestatic:titre_migre_action_etape")." $step",$corps);
} // action_migre_action


// ------------------------------------------------------------------------------
// [fr] Affiche la liste des pages (URIs) qui seront importees
// [en] Shows the URI list of pages to be imported
// ------------------------------------------------------------------------------
function migre_affiche_pages()
{
global $migre_meta;
	$listepages=$migre_meta['migre_liste_pages'];
	$dochtml=get_list_of_pages($listepages);
	$i=0;
	$res  = _T('migrestatic:resultat_liste_pages');
	$res .= "\n<div style='text-align:left;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;overflow:auto;height:15em;'><ol>";
	while(list ($key, $migre_uri) = each ($dochtml) )
	{
		$res .= "\n<li><a href='$migre_uri'>".$migre_uri."</a></li>";
	}
	$res .= "\n</ol></div>";
	return $res;
} // migre_affiche_pages

// ------------------------------------------------------------------------------
// [fr] Realise la migration des pages
// [en] Runs the pages migration
// ------------------------------------------------------------------------------
function migre_pages($id_rubrique)
{
global $migre_meta, $dir_lang;

	// Si id_rubrique vaut 0 ou n'est pas definie, creer l'article
	// dans la premiere rubrique racine
	if (!$id_rubrique = intval($id_rubrique)) {
		$row = spip_fetch_array(spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0 ORDER by 0+titre,titre LIMIT 1"));
		$id_rubrique = $row['id_rubrique'];
	}

	$listepages=$migre_meta['migre_liste_pages'];
	$dochtml=get_list_of_pages($listepages);

	// [fr] Récup des URIs du site statique et traitement
	// [en] Get all URIs and process them
	$res_list= _T('migrestatic:resultat_liste_pages'). "\n<div $dir_lang style='width:98%;height:4em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>" ;

	$res  = "";
	$i=0;
	while(list ($key, $migre_uri) = each ($dochtml) )
	{
		$res_list .= "<a href='$migre_uri'>".$migre_uri."</a><br />";
		$res .= debut_cadre_relief('article-24.gif',true,'',
			_T('migrestatic:processing_page')." <a href='$migre_uri'>".$migre_uri."</a>");
		$res .= migre_infos_page($migre_uri,$id_rubrique);
		$res .= fin_cadre_relief(true);
		if ($migre_meta['migre_test'] AND $i>5) break; else $i++; // [fr] On ne teste que les 5 premieres pages [en] Test only first 5 pages
	 };
	$res .= _T('migrestatic:migre_fini');
	$res_list.= "<br style='clear: both;' />\n</div>\n";
	return $res_list.$res;
}

// ------------------------------------------------------------------------------
// [fr] Recupere les infos de la page web, et traite son contenu
// [fr] apres l'avoir telechargee
// ------------------------------------------------------------------------------
function migre_infos_page($adresse,$id_rubrique) {
global $dir_lang, $migre_meta;
	$auteur=$GLOBALS['auteur_session']['id_auteur'];	// [fr] id_auteur de tous les articles récupérés dans Spip
	$res = "";

	// [fr] On recupere la page a traiter
	$page_a_traiter = recuperer_page($adresse,true);

	if (strlen($page_a_traiter) < 10) { return "<strong>"._T('migrestatic:err_page_vide')."</strong>";}

	// [fr] Un coup de nettoyage HTML
	// [en] Try to clean HTML
	$page_a_traiter= migre_nettoie_html($page_a_traiter);

	// [fr] On extrait les éléments de la page:

	// [fr] la langue [en] the language
	// [fr] d abord dans la balise HTML [en] In the HTML tag
	@preg_match("/<html.*lang\=['\"](.*)['\"].*>/iUs",$page_a_traiter,$result);
	$lang = $result[1];

	// [fr] puis dans les META [en] Then in the META tags
	if (empty($lang)) {
	unset($result);
	@preg_match("/<meta.*content-language.*content\=['\"](.*)['\"].*>/iUs",$page_a_traiter,$result);
	$lang = $result[1];
	}

	if (empty($lang)) {
		// La page importee n a pas de langue definie on utilise celle de SPIP
		// La langue a la creation : si les liens de traduction sont autorises
		// dans les rubriques, on essaie avec la langue de l'auteur,
		// ou a defaut celle de la rubrique
		// Sinon c'est la langue de la rubrique qui est choisie + heritee
		if ($GLOBALS['meta']['multi_articles'] == 'oui') {
			lang_select($GLOBALS['auteur_session']['lang']);
			if (in_array($GLOBALS['spip_lang'],
			explode(',', $GLOBALS['meta']['langues_multilingue']))) {
				$lang = $GLOBALS['spip_lang'];
			}
		}

		if (!$lang) {
			$lang = $GLOBALS['meta']['langue_site'];
		}
	}

	// [fr] le titre
	$titre = migre_chercher_titre($page_a_traiter);
//	$titre = migre_html_entity_decode($titre); //un titre intelligible !
	$titre = migre_html_to_spip($titre); // au format de SPIP
	if ( empty($titre)
	    OR ($titre=="Untitled Document")
	    OR ($titre=="Document sans titre")
	    OR ($titre=="Page normale sans titre")
	   ) $titre=$adresse; //pas de titre ? -> le titre sera l'URL

	// [fr] le body
	$body=migre_chercher_body($page_a_traiter);
	$body=migre_filtrer_body($body);
	$body=migre_nettoie_url($body,$adresse);
	$body=migre_html_to_spip($body);

	// [fr] Si ce n est pas un test : integration de l'article dans SPIP
	// [en] If it s not a test, load into SPIP
	$migretest = $migre_meta['migre_test'];
	$id_mot = $migre_meta['migreidmot'];

	if (!$migretest)
	{
		$res .= "\n<div $dir_lang style='float:left;width:98%;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>".migre_cree_article($titre,$body,$adresse,$id_rubrique,$auteur,$id_mot,$lang)."\n</div>\n";
	}
	else
	{
		$res .= "\n<div $dir_lang style='float:left;width:48%;text-align:center;'>"._T('migrestatic:article_affiche_par_spip')."\n</div>\n";
		$res .= "\n<div $dir_lang style='float:left;width:48%;text-align:center;'>"._T('migrestatic:article_edite_par_spip')."\n</div>\n<br />\n";
		$res .= "\n<div $dir_lang style='float:left;width:48%;height:6em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>".propre($body)."<br style='clear: both;' />\n</div>\n";
		$res .= "\n<div $dir_lang style='float:left;width:48%;height:6em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>".nl2br($body)."<br style='clear: both;' />\n</div>\n";
	}

	return $res;
} // migre_pages

// ------------------------------------------------------------------------------
// [fr] met le contenu de <title></title> dans une chaîne
// [en] Extracts the content between <title></title> into a string
// ------------------------------------------------------------------------------
function migre_chercher_titre($ascruter)
{
	preg_match("/(<title>)(.*?)(<\/title>)/i",$ascruter, $recherche);
	$titre = $recherche [2];
	return $titre;
} // migre_chercher_titre

// ------------------------------------------------------------------------------
// [fr] met le contenu de <body></body> dans une chaîne
// [en] Extracts the content between <body></body> into a string
// ------------------------------------------------------------------------------
function migre_chercher_body ($ascruter)
{
global $migre_meta;
	// [fr] Extraction du corps de la page
	// [en] Extract the body
	preg_match('/(<body.*>)(.*)(<\/body>)/iUs',$ascruter, $extraction);
	$contenu = $extraction[2];

	// [fr] Extraction d une sous partie du corps
	// [en] Extracts a sub part of the body
	$bcentredebut = transcoder_page($migre_meta['migre_bcentredebut']);
	$bcentrefin  = transcoder_page($migre_meta['migre_bcentrefin']);

	if (!empty($bcentredebut) AND !empty($bcentrefin))
	{
		if ( @preg_match('/('.$bcentredebut.')(.*)('.$bcentrefin.')/iUs',$contenu,$souspartie) ) {
			if (is_array($souspartie) AND !empty($souspartie[2])) $contenu = $souspartie[2];
		}
	}

	return $contenu;
} // migre_chercher_body

// ------------------------------------------------------------------------------
// [fr] Extrait et filtre le BODY d'une page importee
// ------------------------------------------------------------------------------
function migre_filtrer_body($contenu) {
	global $migre_meta;

/* rajouts fwn *//*
	$contenu=preg_replace('/(<span[ ]class\=\"code\">)(.*)(<\/span><br>)/iUs',"\r<code>\$2</code>\r",$contenu);
	$contenu=preg_replace('/(<span[ ]class\=\"titre1-nb\">)(.*)(<\/span><span[ ]class\=\"titre1\">)(.*)(<\/span>)/iUs',"<h1>\$2\$4</h1>",$contenu);
/* fin rajouts speciaux fwn */

	if (count($migre_meta['migre_htos'])>0) {
		reset($migre_meta['migre_htos']);
		while ( list($key,$val) = each($migre_meta['migre_htos']) ) {
			if (!empty($migre_meta['migre_htos'][$key]['filtre'])) {
				$filtre=@preg_replace('/@r/iUs',"\r",$migre_meta['migre_htos'][$key]['filtre']); 
				$filtre=@preg_replace('/@n/iUs',"\n",$filtre);
				$conv=@preg_replace('/@r/iUs',"\r",$migre_meta['migre_htos'][$key]['spip']);
				$conv=@preg_replace('/@n/iUs',"\n",$conv);

				$contenu=@preg_replace($filtre,$conv,$contenu);
				if ( function_exists('preg_last_error') AND preg_last_error()<>PREG_NO_ERROR ) {
					spip_log("migre_static: migre_filtrer_body() erreur regexp:".$key.":filtre:".$filtre.":conv:".$conv);
					echo "migrestatic: warning function migre_filtrer_body() : invalid regexp key : $key\n<br>";
				}
			}
		}
	}

	return $contenu;
} // migre_filtrer_body

// ------------------------------------------------------------------------------
// [fr] Insere un nouvel article dans Spip.
// [en] Add a new article into SPIP
// ------------------------------------------------------------------------------
function migre_cree_article($titre,$texte,$url_site,$id_rub,$auteur,$id_mot,$lang)
{
	// [fr] Rechercher une occurence deja presente
	unset($id_article);
	$url_site = addslashes(corriger_caracteres($url_site));
	$sql = "SELECT id_article,statut FROM spip_articles WHERE url_site='".$url_site."' AND statut!='poubelle' AND statut!='refuse' LIMIT 1";
	$result=spip_query($sql);
	if ($row = spip_fetch_array($result)) $id_article=$row['id_article'];

	if ($row['statut'] == 'publie') {
		return "<strong>"._T('migrestatic:err_article_deja_publie').$id_article."</strong>";
	}

	$titre = addslashes(corriger_caracteres($titre));
	$texte = addslashes(corriger_caracteres($texte));

	// article
	if (!$id_article) {
		$sql = "INSERT INTO spip_articles (titre, texte, id_rubrique , nom_site, url_site, statut, date, lang) VALUES ('".$titre."','".$texte."','$id_rub','".$titre."','".$url_site."', 'prepa',NOW(),'".$lang."')";
		$result = spip_query($sql);
		$id_article=spip_insert_id();
		$t_mess='migrestatic:insert_article_id';
	}
	else {
		$sql = "UPDATE spip_articles SET titre='$titre', url_site='$url_site', id_rubrique='$id_rub', nom_site='$titre', texte='$texte', ps='$ps' WHERE id_article=$id_article";
		$result = spip_query($sql);
		$t_mess='migrestatic:update_article_id';
	}

	if (empty($id_article))
	{
		return "<strong>"._T('migrestatic:err_insert_article').$titre."</strong>";
	}
	else
	{
		spip_log('migre_static : insert article #'.$id_article);

		// auteur
		$sql = "REPLACE INTO spip_auteurs_articles (id_auteur, id_article) VALUES (" . $auteur . ", " . $id_article . ")";
		$result = spip_query($sql);

		if (!empty($id_mot) AND is_array($id_mot))
		{
			// mot-cles
			reset($id_mot);
			while (list($key,$val)=each($id_mot)) {
				if (!empty($val)) {
					$sql = "REPLACE INTO spip_mots_articles (id_mot, id_article) VALUES (" . $val . ", " . $id_article . ")";
					$result = spip_query($sql);
				}
			}
		}

		return _T($t_mess) . $id_article. _T('migrestatic:insert_article_titre') . "<a href='" . generer_url_ecrire("articles","id_article=$id_article") . "'>". $titre ."</a>" ;
	}

} // migre_cree_article

// ------------------------------------------------------------------------------
// [fr] Nettoie les urls des differents liens et images
// [fr] Clean urls from links and images
// ------------------------------------------------------------------------------
function migre_nettoie_url($texte,$url)
{
	$texte=preg_replace('/src=\"(.*?)\"/ie',"url_absolues('\$1','".$url."', 'src=\"')",$texte);
	$texte=preg_replace('/href=\"(.*?)\"/ie',"url_absolues('\$1','".$url."','href=\"')",$texte);
	$texte=preg_replace('/href=\".*\" onClick=\".*\(\'(http:\/\/.*)\'.*\"/iUs',"href=\"\$1\"",$texte); // [fr] suppr le js [en] remove the js
	return $texte;
} // migre_nettoie_url

// ------------------------------------------------------------------------------
// [fr] transforme des URL relatives en absolues
// [fr] trouvée ici: http://www.web-max.ca/PHP/misc_24.php
// [fr] et nulle part ailleurs, licence ???
// ------------------------------------------------------------------------------
function url_absolues($rel,$url,$rajout)
{
	$com = InternetCombineURL($url,$rel);
	$com= $rajout.$com.'"';
	return $com;
} // url_absolues

// ------------------------------------------------------------------------------
// ------------------------------------------------------------------------------
function InternetCombineURL($absolute,$relative)
{
	$p = parse_url($relative);
	if($p["scheme"])return $relative;
	
	extract(parse_url($absolute));
	$path = dirname($path);
	
	if($relative{0} == '/') {
		$cparts = array_filter(explode("/", $relative));
	}
	else {
		$aparts = array_filter(explode("/", $path));
		$rparts = array_filter(explode("/", $relative));
		$cparts = array_merge($aparts, $rparts);
		foreach($cparts as $i => $part) {
		if($part == '.') {
			$cparts[$i] = null;
		}
		if($part == '..') {
			$cparts[$i - 1] = null;
			$cparts[$i] = null;
		}
		}
		$cparts = array_filter($cparts);
	}
	$path = implode("/", $cparts);
	$url = "";
	if($scheme) {
		$url = "$scheme://";
	}
	if($user) {
		$url .= "$user";
		if($pass) {
		$url .= ":$pass";
		}
		$url .= "@";
	}
	if($host) {
		$url .= "$host/";
	}
	$url .= $path;
	return $url;
} // InternetCombineURL

// ------------------------------------------------------------------------------
// [fr] remplace du HTML par des raccourcis Spip
// ------------------------------------------------------------------------------
function migre_html_to_spip($texte)
{
global $migre_meta;

	$texte=preg_replace('/<a[ ]name=\"(.*)\".*>(.*)<\/a>/iUs',"[\$1<-]",$texte);
	$texte=preg_replace('/<a.+href=\"(.*)\".*>(.*)<\/a>/iUs',"[\$2->\$1]",$texte);

	// Suite tableaux
	$texte = preg_replace(",\n[| ]+\n,", "", $texte);
	$texte = preg_replace(",\n[|].+?[|].+?[|].+,", "\\0|\r", $texte);

	// retablir les gras
	$texte = preg_replace(",@@b@@(.*)@@/b@@,Uims","{{\\1}}",$texte);
	$texte = preg_replace(",@@/?b@@,"," ",$texte);

	$texte = preg_replace ('/<p>/i', "", $texte); // on enleve les balises <p> non fermées - nettoyage
	$texte = preg_replace ('/([ a-z0-9éà])[\n\r]([-a-z0-9\)])/iUs', "\$1\$2", $texte); // on supprime les retours à la lignes inutiles

	$texte_lignes = preg_split("/\r\n|\n\r|\n|\r/", $texte);
	unset($texte);
	while (list($key,$ligne) = each($texte_lignes))
	{
		$ligne=trim($ligne);
		$ligne = ereg_replace("[-_]{10,}", "\n------", $ligne);
		$texte.= $ligne."\n";
		if ((strlen($ligne)>200)AND(strlen($ligne)<980))
			$texte.="\n";
	}

	$texte = preg_replace ('/-\*\s*[\n\r]/is', "-* ", $texte); // on supprime les sous éléments vides
	$texte = preg_replace ('/\{(.*)[\n\r]+(.*)\}/Us', "{\$1 \$2}", $texte); // on supprime les retours à la ligne à l'intérieur des titres
	$texte = preg_replace ('/[\n\r]{2,}/i', "\n", $texte); // on supprime les dernieres lignes multiples

	include_spip("inc/plugin");
	$plug=liste_plugin_actifs();
	if ( is_array($plug) 
		AND array_key_exists("COUTEAU_SUISSE",$plug)
		AND isset($GLOBALS['meta']['cs_decoupe'])
		AND $migre_meta['migre_cs_decoupe'] )
	{
		$texte = preg_replace ('/\{\{\{/s',"\n++++\n{{{",$texte);
	}

	return $texte;
} // migre_html_to_spip

// ------------------------------------------------------------------------------
// [fr] nettoie une page web avec Tidy
// ------------------------------------------------------------------------------
function migre_nettoie_html($anetoyer)
{
	$res=$anetoyer;
	if (function_exists('tidy_parse_string'))
	{
		$tidy = tidy_parse_string($anetoyer);
		tidy_clean_repair($tidy);
		$res=$tidy;
	}

	// Premier nettoyage
	$res = str_replace("\n\r", "\r", $res);
	$res = str_replace("\n", "\r", $res);

	return $res;
} migre_nettoie_html

// ------------------------------------------------------------------------------
// [fr] Compare le formulaire avec les valeur en meta et les met à jour
// [en] Compare the form values with the meta ones and updates them
// ------------------------------------------------------------------------------
function migre_check_var($id_rubrique)
{
	global $migre_meta;

	$out = "\n<table width='100%' cellspacing='0' cellpadding='0' border='1'>";
	$migre_meta=array();
	$migre_meta['migre_id_rubrique']=$id_rubrique;
	$migre_meta['migreidmot'] = _request('form_idmot');
	$migre_meta['migre_liste_pages']=_request('form_liste_pages');
	$migre_meta['migre_test'] = _request('form_migre_test');
	$migre_meta['migre_bcentredebut'] = _request('form_bcentredebut');
	$migre_meta['migre_bcentrefin'] = _request('form_bcentrefin');

	$out .= "\n<tr><td>bcentredebut</td><td>".$migre_meta['migre_bcentredebut']."</td></tr>";
	$out .= "\n<tr><td>bcentrefin</td><td>".$migre_meta['migre_bcentrefin']."</td></tr>";

	$migre_meta['migre_htos'] = array();
	$migre_meta['migre_htos_changed']=false;
	$htos=get_list_htos();
	if (count($htos)>0) {
		reset($htos);
		while ( list($key,$val) = each($htos) ) {
			$filtre=_request($key.'-filtre');
			$conv=_request($key.'-htos');
			$out .= "\n<tr><td>".$key."</td><td>".$filtre;
			// code ajoute pour pister les eventuels problemes de conversions
			if ($val['filtre']!=$filtre) {
				$migre_meta['migre_htos_changed']=true;
				$migre_meta['migre_htos_old'][$key]['filtre'] = $val['filtre'];
				$out .= "*";
				spip_log("migrestatic: filtre-spip($key):".$val['filtre'].":modifie:".$filtre.":");
			}
			$out .= "</td></tr>";
			$out .= "\n<tr><td>".$key."</td><td>".$conv;
			if ($val['spip']!=$conv) {
				$migre_meta['migre_htos_changed']=true;
				$migre_meta['migre_htos_old'][$key]['spip'] = $val['spip'];
				$out .= "*";
				spip_log("migrestatic: filtre-htos($key):".$val['spip'].":modifie:".$conv.":");
			}
			$out .= "</td></tr>";

			$migre_meta['migre_htos'][$key]['filtre']=$filtre;
			$migre_meta['migre_htos'][$key]['spip']=$conv;
		}
	}
	$migre_meta['migre_cs_decoupe']= _request('form_migre_cs_decoupe');
	if ($migre_meta!= $GLOBALS['meta']['migrestatic']) {
		include_spip('inc/meta');
		ecrire_meta('migrestatic', serialize($migre_meta));
		ecrire_metas();
	}
	$out .= "\n</table>";
	return $out;
} // migre_check_var

?>
