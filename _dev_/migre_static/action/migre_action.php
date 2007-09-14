<?
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

function action_migre_action() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_rubrique = intval($arg);

	// [fr] compatibilite ascendante
	// [en] backward compatibility
	if ($GLOBALS['spip_version_code']<1.92)
		debut_page(_T('migre:titre_migre_action'), 'action', 'migre_static');
	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('migre:titre_migre_action'), "action", 'migre_static');
	}

	debut_cadre_formulaire();
	echo migre_action_presentation_haut($id_rubrique,_T('migre:titre_migre_action'));
	echo migre_pages($id_rubrique);
	fin_cadre_formulaire();

	echo fin_page();
}

// [fr] Haut de la presentation
// [en] Top of page
function migre_action_presentation_haut($id_rubrique,$titre) {
	$go_back=generer_url_ecrire("naviguer","id_rubrique=$id_rubrique");
	return
		"\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>" .
		"<tr>".
		"\n<td style='width: 130px;'>" .
		icone(_T('icone_retour'), $go_back, "article-24.gif", "rien.gif", 'rien',false) .
		"</td>\n<td>" .
		"<img src='" . _DIR_IMG_PACK . "rien.gif' width='10' alt='rien' />" .
		"</td>\n<td>" .
		_T('migre:sur_titre_migre_static') .
		gros_titre($titre,'',false) .
		"</td></tr></table>\n" .
		"<hr />\n";
}

function migre_pages($id_rubrique) {
	include_spip('base/abstract_sql');
	include_spip('inc/rubriques');
	include_spip("inc/migre"); // [fr] Charge liste des balises [en] Loads HTML marks

	// Si id_rubrique vaut 0 ou n'est pas definie, creer l'article
	// dans la premiere rubrique racine
	if (!$id_rubrique = intval($id_rubrique)) {
		$row = spip_fetch_array(spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=0 ORDER by 0+titre,titre LIMIT 1"));
		$id_rubrique = $row['id_rubrique'];
	}

	$listepages=_request('listepages'); // html_entity_decode ??
	$dochtml=get_list_of_pages($listepages);
	// [fr] Récup des URIs du site statique et traitement
	// [en] Get all URIs and process them
	$res="";
	while(list ($key, $migre_uri) = each ($dochtml))
	{
		$res .= debut_cadre_relief('article-24.gif',true,'',
			_T('migre:processing_page')." <a href='$migre_uri'>".$migre_uri."</a>");
		$res .= migre_infos_page($migre_uri,$id_rubrique);
		$res .= fin_cadre_relief(true);
	 };
	$res .= _T('migre:migre_fini');
	return $res;
}

// [fr] Recupere les infos de la page web, et traite son contenu
// [fr] apres l'avoir telechargee
function migre_infos_page($adresse,$id_rubrique) {
global $dir_lang;
	$auteur=$GLOBALS['auteur_session']['id_auteur'];	// [fr] id_auteur de tous les articles récupérés dans Spip
	$res = "";

	// [fr] On recupere la page a traiter
	$page_a_traiter = recuperer_page($adresse,true);

	if (strlen($page_a_traiter) < 10) { return "<strong>"._T('migre:err_page_vide')."</strong>";}

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
//	$body=migre_html_entity_decode($body); // [fr] convertir les accents [en] convert special chars


	// [fr] Si ce n est pas un test : integration de l'article dans SPIP
	// [en] If it s not a test, load into SPIP
	$migretest = _request('migretest');
	$id_mot = _request('id_mot');

	if ($migretest!="test")
	{
		$res .= _T('migre:page_title').$titre."<br>\n";
		$res .= migre_cree_article($titre,$body,$adresse,$id_rubrique,$auteur,$id_mot,$lang);
	}
	else
	{
		$res .= _T('migre:article_affiche_par_spip')."<br>\n";
		$res .= "\n<div $dir_lang style='width:95%;height:8em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>".propre($body)."<br style='clear: both;' />\n</div>\n";
		$res .= _T('migre:article_edite_dans_spip')."<br>\n";
		$res .= "\n<div $dir_lang style='width:95%;height:8em;overflow:auto;border: 1px dashed #ada095;padding:2px;margin:2px;background-color:#eee;text-align:left;'>".nl2br($body)."<br style='clear: both;' />\n</div>\n";
	}

	return $res;
}

// [fr] met le contenu de <title></title> dans une chaîne
function migre_chercher_titre($ascruter)
{
	preg_match("/(<title>)(.*?)(<\/title>)/i",$ascruter, $recherche);
	$titre = $recherche [2];
	return $titre;
}

// [fr] met le contenu de <body></body> dans une chaîne
function migre_chercher_body ($ascruter)
{
	// [fr] Extraction du corps de la page
	// [en] Extract the body
	preg_match('/(<body.*>)(.*)(<\/body>)/iUs',$ascruter, $extraction);
	$contenu = $extraction[2];

	// [fr] Extraction d une sous partie du corps
	// [en] Extracts a sub part of the body
	$bcentredebut = transcoder_page(_request('bcentredebut'));
	$bcentrefin  = transcoder_page(_request('bcentrefin'));

	if (!empty($bcentredebut) AND !empty($bcentrefin))
	{
		if ( @preg_match('/('.$bcentredebut.')(.*)('.$bcentrefin.')/iUs',$contenu,$souspartie) ) {
			if (is_array($souspartie) AND !empty($souspartie[2])) $contenu = $souspartie[2];
		}
	}

	return $contenu;
}

function migre_filtrer_body($contenu) {
	include_spip("inc/migre"); // [fr] Charge liste des balises [en] Loads HTML marks
	$htos=get_list_htos();

/* rajouts fwn */
	$contenu=preg_replace('/(<span[ ]class\=\"code\">)(.*)(<\/span><br>)/iUs',"\r<code>\$2</code>\r",$contenu);
	$contenu=preg_replace('/(<span[ ]class\=\"titre1-nb\">)(.*)(<\/span><span[ ]class\=\"titre1\">)(.*)(<\/span>)/iUs',"<h1>\$2\$4</h1>",$contenu);
/* fin rajouts speciaux fwn */

	if (count($htos)>0) {
		reset($htos);
		while ( list($key,$val) = each($htos) ) {
			$filtre=transcoder_page(_request($key.'-filtre'));
			$conv=preg_replace('/\\\r/iUs',"\r",transcoder_page(_request($key.'-htos'))); // [fr] peut etre vide [en] empty value allowed
			if (!empty($filtre)) $contenu=preg_replace($filtre,$conv,$contenu);
		}
	}

	return $contenu;
}

// [fr] Insère un nouvel article dans Spip.
// [en] Add a new article into SPIP
function migre_cree_article($titre,$texte,$url_site,$id_rub,$auteur,$id_mot,$lang)
{
	// [fr] Rechercher une occurence deja presente
	unset($id_article);
	$url_site = addslashes(corriger_caracteres($url_site));
	$sql = "SELECT id_article,statut FROM spip_articles WHERE url_site='".$url_site."' AND statut!='poubelle' AND statut!='refuse' LIMIT 1";
	$result=spip_query($sql);
	if ($row = spip_fetch_array($result)) $id_article=$row['id_article'];

	if ($row['statut'] == 'publie') {
		return "<strong>"._T('migre:err_article_deja_publie').$id_article."</strong>";
	}

	$titre = addslashes(corriger_caracteres($titre));
	$texte = addslashes(corriger_caracteres($texte));

	// article
	if (!$id_article) {
		$sql = "INSERT INTO spip_articles (titre, texte, id_rubrique , nom_site, url_site, statut, date, lang) VALUES ('".$titre."','".$texte."','$id_rub','".$titre."','".$url_site."', 'prepa',NOW(),'".$lang."')";
		$result = spip_query($sql);
		$id_article=spip_insert_id();
		$t_mess='migre:insert_article_id';
	}
	else {
		$sql = "UPDATE spip_articles SET titre='$titre', url_site='$url_site', id_rubrique='$id_rub', nom_site='$titre', texte='$texte', ps='$ps' WHERE id_article=$id_article";
		$result = spip_query($sql);
		$t_mess='migre:update_article_id';
	}

	if (!empty($id_article))
	{
		// auteur
		$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES (" . $auteur . ", " . $id_article . ")";
		$result = spip_query($sql);

		if (!empty($id_mot))
		{
			// mot-cle
			$sql = "INSERT INTO spip_mots_articles (id_mot, id_article) VALUES (" . $id_mot . ", " . $id_article . ")";
			$result = spip_query($sql);
		}

		return _T($t_mess) . $id_article. _T('migre:insert_article_titre') . $titre ;
	}
	else
	{
		return "<strong>"._T('migre:err_insert_article').$titre."</strong>";
	}
}

// [fr] Nettoie les urls des differents liens et images
// [fr] Clean urls from links and images
function migre_nettoie_url($texte,$url)
{
	$texte=preg_replace('/src=\"(.*?)\"/ie',"url_absolues('\$1','".$url."', 'src=\"')",$texte);
	$texte=preg_replace('/href=\"(.*?)\"/ie',"url_absolues('\$1','".$url."','href=\"')",$texte);
	$texte=preg_replace('/href=\".*\" onClick=\".*\(\'(http:\/\/.*)\'.*\"/iUs',"href=\"\$1\"",$texte); // [fr] suppr le js [en] remove the js
	return $texte;
}

// [fr] transforme des URL relatives en absolues
// [fr] trouvée ici: http://www.web-max.ca/PHP/misc_24.php
// [fr] et nulle part ailleurs, licence ???
function url_absolues($rel,$url,$rajout)
{
	$com = InternetCombineURL($url,$rel);
	$com= $rajout.$com.'"';
	return $com;
}

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
}

// [fr] remplace du HTML par des raccourcis Spip
function migre_html_to_spip($texte)
{
//	if (isset($GLOBALS['meta']['cs_decoupe'])) echo "";

	$texte=preg_replace('/<a[ ]name=\"(.*)\".*>(.*)<\/a>/iUs',"[\$1<-]",$texte);
	$texte=preg_replace('/<a.+href=\"(.*)\".*>(.*)<\/a>/iUs',"[\$2->\$1]",$texte);
//	$texte = preg_replace ('/<br>-/iUs', "\n-", $texte);
//	$texte = preg_replace ('/<br>\n/iUs', "\n_ ", $texte);
//	$texte = preg_replace ('/&nbsp;/i', " ", $texte);

	// Suite tableaux
	$texte = preg_replace(",\n[| ]+\n,", "", $texte);
	$texte = preg_replace(",\n[|].+?[|].+?[|].+,", "\\0|\r", $texte);

	// retablir les gras
	$texte = preg_replace(",@@b@@(.*)@@/b@@,Uims","<b>\\1</b>",$texte);
	$texte = preg_replace(",@@/?b@@,"," ",$texte);

	$texte = preg_replace ('/<p>/i', "", $texte); // on enleve les balises <p> non fermées - nettoyage
/*	$texte = preg_replace ('/\n[ \t]+/is', "\n", $texte); // on supprime les espaces multiples cf ltrim ?
	$texte = preg_replace ('/([ a-z0-9éà])\n([-a-z0-9\)])/iUs', "\$1\$2", $texte); // on supprime les retours à la lignes inutiles
	$texte = preg_replace ('/\n{2,}/i', "\n", $texte); // on supprime les dernieres lignes multiples
*/
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

	return $texte;
}

// [fr] nettoie une page web avec Tidy
function migre_nettoie_html ($anetoyer)
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
}

/*
// [fr] decodage utf8 extrait de : http://fr.php.net/html_entity_decode
// [en] decode utf8 function from :  http://www.php.net/html_entity_decode
function migre_html_entity_decode($string)
{
	if ($GLOBALS['meta']['charset'] == 'utf-8')
	{
		// [fr] Site SPIP en utf-8
		// [en] SPIP Website using UTF-8

		$ver = explode( '.', PHP_VERSION );
		$ver_num = $ver[0] . $ver[1] . $ver[2];
		if ( $ver_num < 500 )
		{
			// [fr] PHP v4 : eviter erreur cannot yet handle MBCS in html_entity_decode
			// [en] PHP r4 : avoid error cannot yet handle MBCS in html_entity_decode
			return html_entity_decode_utf8($string);
		}
		else
		{
			// [fr] PHP v 5 ou ulterieur
			return html_entity_decode($string,ENT_COMPAT,'UTF-8');
		}
	}
	else
	{
		// [fr] Site SPIP en iso "classique"
		// [en] SPIP website using iso
		return html_entity_decode($string);
	}
}

function html_entity_decode_utf8($string)
{
    static $trans_tbl;

    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'code2utf(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'code2utf(\\1)', $string);

    // replace literal entities
    if (!isset($trans_tbl))
    {
        $trans_tbl = array();

        foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
            $trans_tbl[$key] = utf8_encode($val);
    }

    // [fr] Ajout d un traitement pour elements oublies dans la fonction fournie
    // [en] added missing chars - dont know why ... but that's fine !
    for ($i = 224; $i <= 254; $i++) {
     $c=chr($i); $trans_tbl[$c] = utf8_encode($c);
    }

    return strtr($string, $trans_tbl);
}

function code2utf($number)
{
        if ($number < 0)
            return FALSE;

        if ($number < 128)
            return chr($number);

        // Removing / Replacing Windows Illegals Characters
        if ($number < 160)
        {
                if ($number==128) $number=8364;
            elseif ($number==129) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==130) $number=8218;
            elseif ($number==131) $number=402;
            elseif ($number==132) $number=8222;
            elseif ($number==133) $number=8230;
            elseif ($number==134) $number=8224;
            elseif ($number==135) $number=8225;
            elseif ($number==136) $number=710;
            elseif ($number==137) $number=8240;
            elseif ($number==138) $number=352;
            elseif ($number==139) $number=8249;
            elseif ($number==140) $number=338;
            elseif ($number==141) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==142) $number=381;
            elseif ($number==143) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==144) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==145) $number=8216;
            elseif ($number==146) $number=8217;
            elseif ($number==147) $number=8220;
            elseif ($number==148) $number=8221;
            elseif ($number==149) $number=8226;
            elseif ($number==150) $number=8211;
            elseif ($number==151) $number=8212;
            elseif ($number==152) $number=732;
            elseif ($number==153) $number=8482;
            elseif ($number==154) $number=353;
            elseif ($number==155) $number=8250;
            elseif ($number==156) $number=339;
            elseif ($number==157) $number=160; // (Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
            elseif ($number==158) $number=382;
            elseif ($number==159) $number=376;
        } //if
       
        if ($number < 2048)
            return chr(($number >> 6) + 192) . chr(($number & 63) + 128);
        if ($number < 65536)
            return chr(($number >> 12) + 224) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
        if ($number < 2097152)
            return chr(($number >> 18) + 240) . chr((($number >> 12) & 63) + 128) . chr((($number >> 6) & 63) + 128) . chr(($number & 63) + 128);
       
       
        return FALSE;
} //code2utf()
*/

?>
