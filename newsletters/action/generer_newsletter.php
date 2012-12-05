<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_generer_newsletter_dist($id_newsletter = null, $force = false){
	if (is_null($id_newsletter)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_newsletter = $securiser_action();
	}

	include_spip('inc/autoriser');
	if (autoriser('generer', 'newsletter', $id_newsletter)){
		$row = sql_fetsel('*', 'spip_newsletters', 'id_newsletter=' . intval($id_newsletter));

		// si cuite on ne genere pas, sauf si force
		if (!$row['baked'] OR $force){
			$patron = $row['patron'];
			$date = intval($row['date_redac'])?$row['date_redac']:$row['date'];

			$set = array();
			$set['html_email'] = newsletters_recuperer_fond($id_newsletter, $patron, $date);
			if (trouver_fond("$patron.texte","newsletters"))
				$set['texte_email'] = newsletters_recuperer_fond($id_newsletter, "$patron.texte", $date);
			else
				$set['texte_email'] = newsletters_html2text($set['html_email']);

			$set['html_page'] = '';
			if (trouver_fond("$patron.page","newsletters"))
				$set['html_page'] = newsletters_recuperer_fond($id_newsletter, "$patron.page", $date);

			#header('Content-Type: text/plain; charset=utf-8');
			#echo($set['texte_email']);
			#die();

			include_spip("action/editer_objet");
			objet_modifier("newsletter",$id_newsletter,$set);

		}
	}
}

function newsletters_recuperer_fond($id_newsletter, $patron, $date = null){

	if (is_null($date))
		$date = date('Y-m-d 00:00:00');

	// on passe la globale lien_implicite_cible_public en true
	// pour avoir les liens internes en public (en non prive d'apres le contexte)
	// credit de l'astuce: denisb & rastapopoulos
	$GLOBALS['lien_implicite_cible_public'] = true;

	$texte = recuperer_fond(
		"newsletters/$patron",
		array(
			'date' => $date,
			'id_newsletter' => $id_newsletter,
		)
	);

	// on revient a la config initiale
	unset($GLOBALS['lien_implicite_cible_public']);

	return $texte;
}

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
	#return $html;

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
		$prelinks[$match[0]] = $match[2]." ($link)";
		$postlinks[$link] = $match[1];
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
	$parser = new Markdownify(false,false,false);
	$texte = $parser->parseString($html);

	$texte = str_replace(array_keys($postlinks), array_values($postlinks),$texte);


	// trim et sauts de ligne en trop ou pas assez
	$texte = trim($texte);
	$texte = str_replace("<br />\n","\n",$texte);
	$texte = preg_replace(",(@@@hr@@@\s*)+\Z,ims","",$texte);
	$texte = preg_replace(",(@@@hr@@@\s*)+,ims","\n\n\n".str_pad("-",75,"-")."\n\n\n",$texte);
	$texte = preg_replace(",(\n#+\s),ims","\n\n\\1",$texte);
	$texte = preg_replace(",(\n\s*)(\n\s*)(\n\s*)+,ims","\n\n\n",$texte);

	// entites restantes ? (dans du code...)
	include_spip('inc/charsets');
	$texte = unicode2charset($texte);
	$texte = str_replace(array('&#039;', '&#034;'),array("'",'"'), $texte);


	// Faire des lignes de 75 caracteres maximum
	return trim(wordwrap($texte));

}

