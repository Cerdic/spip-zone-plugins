<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Extraire les infos de ce fond
 * Les parametres sont passes dans le squelette de newsletter sous la forme :
 * par <!-- param=valeur -->
 *
 * Parametres utilises : titre
 *
 * @param $fond
 * @return array
 */
function newsletters_fond_extraire_infos($fond){
	$infos = array();

	if ($f = trouver_fond($fond,"newsletters")){
		lire_fichier($f,$contenu);
		preg_match_all('/(<!-- ([a-z0-9_]\w+)(\*)?=)(.*?)-->/sim',$contenu,$matches,PREG_SET_ORDER);
		if ($matches){
			foreach ($matches as $m){
				newsletters_fond_post_params($infos,$m);
			}
		}
	}
	return $infos;
}

/**
 * callback pour interpreter les parametres objets d'une newsletter
 * (tire du plugin cfg)
 *
 * Parametres :
 * - $regs[2] = 'parametre'
 * - $regs[3] = '*' ou ''
 * - $regs[4] = 'valeur'
 *
 * Lorsque des parametres sont passes dans le formulaire
 * par <!-- param=valeur --><br>
 * stocker $this->param['parametre']=valeur
 *
 * Si <!-- param*=valeur --><br>
 * Stocker $this->param['parametre'][]=valeur
 *
 * @param array $param
 * @param array $regs
 */
function newsletters_fond_post_params(&$param,$regs) {

	// $regs[3] peut valoir '*' pour signaler un tableau
	$regs[4] = trim($regs[4]);

	if (empty($regs[3])) {
		$param[$regs[2]] = $regs[4];
	} elseif (is_array($this->param[$regs[2]])) {
		$param[$regs[2]][] = $regs[4];
	}
}


/**
 * generer une version texte a partir d'une version HTML
 *
 * @param string $html
 * @return string
 */
function newsletters_html2text($html){
	// nettoyer les balises de mise en page html
	$html = preg_replace(",</(td|th)>,Uims","<br/>",$html);
	$html = preg_replace(",</(table)>,Uims","@@@hr@@@",$html);
	$html = preg_replace(",</?(html|body|table|td|th|tbody|thead|center|article|section|span)[^>]*>,Uims","\n\n",$html);
	$html = preg_replace(",<!--.*-->,Uims","\n",$html);
	$html = preg_replace(",<(/?)(div|tr|caption)([^>]*>),Uims","<\\1p>",$html);
	$html = preg_replace(",(<p>\s*)+,ims","<p>",$html);
	$html = preg_replace(",<br/?>\s*</p>,ims","</p>",$html);
	$html = preg_replace(",</p>\s*<br/?>,ims","</p>",$html);
	$html = preg_replace(",(</p>\s*(@@@hr@@@)?\s*)+,ims","</p>\\2",$html);
	$html = preg_replace(",(<p>\s*</p>),ims","",$html);

	// succession @@@hr@@@<hr> et <hr>@@@hr@@@
	$html = preg_replace(",@@@hr@@@\s*(<[^>]*>\s*)?<hr[^>]*>,ims","@@@hr@@@\n",$html);
	$html = preg_replace(",<hr[^>]*>\s*(<[^>]*>\s*)?@@@hr@@@,ims","\n@@@hr@@@",$html);

	$html = preg_replace(",<textarea[^>]*spip_cadre[^>]*>(.*)</textarea>,Uims","<code>\n\\1\n</code>",$html);

	// vider le contenu de qqunes :
	$html = preg_replace(",<head[^>]*>.*</head>,Uims","\n",$html);

	// Liens :
	// Nettoyage des liens des notes de bas de page
	$html = preg_replace("@<a href=\"#n(b|h)[0-9]+-[0-9]+\" name=\"n(b|h)[0-9]+-[0-9]+\" class=\"spip_note\">([0-9]+)</a>@", "\\3", $html);
	// Supprimer tous les liens internes
	$html = preg_replace("/\<a href=['\"]#(.*?)['\"][^>]*>(.*?)<\/a>/ims","\\2", $html);
	// Remplace tous les liens
	preg_match_all("/\<a href=['\"](.*?)['\"][^>]*>(.*?)<\/a>/ims", $html,$matches,PREG_SET_ORDER);
	$prelinks = $postlinks = array();
	foreach ($matches as $k => $match){
		$link = "@@@link$k@@@";
		$url = str_replace("&amp;","&",$matches[1]);
		if ($match[2]==$matches[1] OR $match[2]==$url){
			// si le texte est l'url :
			$prelinks[$match[0]] = "$link";
		}
		else {
			// texte + url
			$prelinks[$match[0]] = $match[2] . " ($link)";
		}
		$postlinks[$link] = $url;
	}
	$html = str_replace(array_keys($prelinks), array_values($prelinks),$html);

	// les images par leur alt ?
	// au moins les puces
	$html = preg_replace(',<img\s[^>]*alt="-"[^>]*>,Uims','-',$html);
	// les autres
	$html = preg_replace(',<img\s[^>]*alt=[\'"]([^\'"]*)[\'"][^>]*>,Uims',"\\1",$html);
	// on vire celles sans alt
	$html = preg_replace(",</?(img)[^>]*>,Uims","\n",$html);

	// espaces
	$html = str_replace("&nbsp;"," ",$html);
	$html = preg_replace(",<p>\s+,ims","<p>",$html);

	#return $html;
	include_spip("lib/markdownify/markdownify");
	$parser = new Markdownify('inline',false,false);
	$texte = $parser->parseString($html);

	$texte = str_replace(array_keys($postlinks), array_values($postlinks),$texte);


	// trim et sauts de ligne en trop ou pas assez
	$texte = trim($texte);
	$texte = str_replace("<br />\n","\n",$texte);
	$texte = preg_replace(",(@@@hr@@@\s*)+\Z,ims","",$texte);
	$texte = preg_replace(",(@@@hr@@@\s*\n)+,ims","\n\n\n".str_pad("-",75,"-")."\n\n\n",$texte);
	$texte = preg_replace(",(\n#+\s),ims","\n\n\\1",$texte);
	$texte = preg_replace(",(\n\s*)(\n\s*)+(\n)+,ims","\n\n\n",$texte);

	// entites restantes ? (dans du code...)
	include_spip('inc/charsets');
	$texte = unicode2charset($texte);
	$texte = str_replace(array('&#039;', '&#034;'),array("'",'"'), $texte);


	// Faire des lignes de 75 caracteres maximum
	return trim(wordwrap($texte));

}

/**
 * Transformer date+rule en string ics
 *
 * @param string $date
 * @param string $rule
 * @return string
 */
function newsletter_date_rule_to_ics($date,$rule){
	$ics = "BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\n";

	$ics .= "DTSTART:".gmdate("Ymd\THis\Z",strtotime($date))."\n";
	$ics .= "RRULE:".$rule;

	$ics .= "\nEND:VEVENT\nEND:VCALENDAR\n";
	return $ics;
}

/**
 * Transformer la string ics en date+rule
 * @param string $ics
 * @return array
 */
function newsletter_ics_to_date_rule($ics){
	$date = null;
	$rule = "";
	$ics = explode("\n",$ics);
	foreach($ics as $ic){
		if (strncmp($ic,"DTSTART:",8)==0){
			$date = date('Y-m-d H:i:s',strtotime(substr($ic,8)));
		}
		if (strncmp($ic,"RRULE:",6)==0){
			$rule = substr($ic,6);
		}
	}

	return array($date,$rule);
}
