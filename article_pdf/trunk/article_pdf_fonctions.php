<?php

if (!defined('_ECRIRE_INC_VERSION'))
	return;

$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
if (!defined('_DIR_PLUGIN_ARTICLE_PDF')) define('_DIR_PLUGIN_ARTICLE_PDF',(_DIR_PLUGINS.end($p)));

function pdf_first_clean_prepropre($texte){
	// Cette fonction est appelé avant propre.
	$texte = preg_replace('#(<code class=(\'|")([\w]+)(\'|")>)#','<code>',$texte);	// si on a coloration code, on décolorie d'abord
	$texte = preg_replace('#(<cadre class=(\'|")([\w]+)(\'|")>)#','<cadre>',$texte);
	return propre($texte);
}

function pdf_first_clean($texte){
	// Cette focntion est appelé après la fonction propre
	// $texte = ereg_replace("<p class[^>]*>", "<P>", $texte);
	//Translation des codes iso
	// PB avec l'utilisation de <code>
	$trans = get_html_translation_table(HTML_ENTITIES);
	$texte = preg_replace(',<!-- .* -->,msU', '', $texte); // supprimer les remarques HTML (du Couteau Suisse ?)
	$trans = array_flip($trans);
	$trans["<br />\n"] = "<BR>";        // Pour éviter que le \n ne se tranforme en espace dans les <DIV class=spip_code> (TT, tag SPIP : code)
	$trans['&#176;'] = "°";
	$trans["&#339;"] = "oe";
	$trans["&#8206;"] = "";
	$trans["&#8211;"] = "-";
	$trans["&#8216;"] = "'";
	$trans["&#8217;"] = "'";
	$trans["&#8220;"] = "\"";
	$trans["&#8221;"] = "\"";
	$trans["&#8230;"] = "...";
	$trans["&#8364;"] = "Euros";
	$trans["&ucirc;"] = "û";
	$trans['->'] = '-»';
	$trans['<-'] = '«-';
	$trans['&nbsp;'] = ' ';
	// certains titles font paniquer l'analyse
	$texte = preg_replace(',title=".*",msU', 'title=""', $texte);

	$texte = unicode2charset(charset2unicode($texte), 'iso-8859-1'); // repasser tout dans un charset acceptable par export PDF
	$texte = strtr($texte, $trans);

	return $texte;
}

function filtre_supprimer_param_logo($texte){
	return preg_replace('`\?[0-9]*$`','',$texte);
}

/**
 * [(#TITRE|pdf_nommer{article, #ID_ARTICLE})]
 *
 * Fonction reprise de SPIP (plugin dist urls_etendues)
 * pour le filtre url_nettoyer
 *
**/

function pdf_nommer($titre_parent, $objet, $id_parent){
// utiliser la constante _DIR_STOCK_PDF
//  au choix, selon le cas (voir les avantages et les inconvéniants de chaque selon le site):
//	$path_pdf = sous_repertoire(_DIR_CACHE, "article_PDF"); // stockage dans le cache SPIP
//	$path_pdf = sous_repertoire(_DIR_IMG, "article_PDF"); //stockage sous le dossier IMG

	include_spip('action/editer_url');
	$titre = url_nettoyer($titre_parent,60);
	$lettre = substr($objet, 0, 1);
	$nom_pdf = $titre."_".$lettre.$id_parent.".pdf";
	return $nom_pdf;
}

/**
 *
 * [(#TITRE|pdf_nommer_ancien{article, #ID_ARTICLE})]
 *
**/
function pdf_nommer_ancien($titre_parent, $objet='article', $id_article){

	//$titre_article = translitteration(corriger_caracteres('[(#TITRE|supprimer_numero|pdf_first_clean|supprimer_tags|texte_script)]'));
	$titre_article = translitteration(corriger_caracteres(texte_script(supprimer_tags(pdf_first_clean($titre_parent)))));

	// Reprise du code de urls/propres.php (pourquoi c'est pas une fonction de l'API de SPIP ?)

	// on va convertir tous les caracteres de ponctuation et espaces
	// a l'exception de l'underscore (_), car on veut le conserver dans l'url
	$titre_article = str_replace('_', chr(7), $titre_article);
	$titre_article = @preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre_article);
	$titre_article = str_replace(chr(7), '_', $titre_article);

	// S'il reste trop de caracteres non latins, les gerer comme wikipedia
	// avec rawurlencode :
	if (preg_match_all(",[^a-zA-Z0-9 _]+,", $titre_article, $r, PREG_SET_ORDER)) {
		foreach ($r as $regs) {
			$titre_article = substr_replace($titre_article, rawurlencode($regs[0]),
				strpos($titre_article, $regs[0]), strlen($regs[0]));
		}
	}

	// S'il reste trop peu, on retombe sur article12
	if (strlen($titre_article) == 0 ) {
		$titre_article = "article";
	}

	// Sinon couper les mots et les relier par des tirets
	else {
		$mots = preg_split(",[^a-zA-Z0-9_%]+,", $titre_article);
		$titre_article = '';
		foreach ($mots as $mot) {
			if (!strlen($mot)) continue;
			$titre_article2 = $titre_article.'-'.$mot;

			// Si on depasse _URLS_PROPRES_MAX caracteres, s'arreter
			// ne pas compter 3 caracteres pour %E9 mais un seul
			$long = preg_replace(',%.,', '', $titre_article2);
			if (strlen($long) > _URLS_PROPRES_MAX) {
				break;
			}

			$titre_article = $titre_article2;
		}
		$titre_article = substr($titre_article, 1);

		// On enregistre en utf-8 dans la base
		$titre_article = rawurldecode($titre_article);

		if (strlen($titre_article) == 0)
			$titre_article = "article";
	}

	$files_pdf = $titre_article."_a".$id_article.".pdf";
	return $files_pdf;

}
