<?php
/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Facteur_email_wrap_to_html
 */

/**
 * Transformer un mail texte ou HTML simplifie en mail HTML complet avec le wrapper emails/texte.html
 * Si le mail est un mail texte :
 *   la premiere ligne est le sujet
 *   le reste est le corps du mail
 *
 * Si le mail est un mail HTML simplifie :
 *   le sujet est entre <title></title>
 *   le corps est entre <body></body>
 *   une eventuelle intro peut etre fournie entre <intro></intro>
 *
 * @param string $texte_ou_html
 * @return string
 */
function inc_facteur_mail_wrap_to_html_dist($texte_ou_html){
	$texte_ou_html = trim($texte_ou_html);
	// attention : si pas de contenu on renvoi du vide aussi (mail vide = mail vide)
	if (!strlen(trim($texte_ou_html))){
		return $texte_ou_html;
	}

	$contexte = array('sujet' => '', 'texte' => '', 'intro' => '');

	// tester si le mail est en html (simplifie)
	if (substr($texte_ou_html, 0, 1)=='<'
		and substr($texte_ou_html, -1, 1)=='>'
		and stripos($texte_ou_html, '</body>')!==false){

		// dans ce cas on ruse un peu : extraire le sujet du title
		$sujet = '';
		if (preg_match(",<title>(.*)</title>,Uims", $texte_ou_html, $m)){
			$contexte['sujet'] = $m[1];
			$texte_ou_html = preg_replace(",<title>(.*)</title>,Uims", '', $texte_ou_html, 1);
			$texte_ou_html = trim($texte_ou_html);
		}
		if (preg_match(",<intro>(.*)</intro>,Uims", $texte_ou_html, $m)){
			$contexte['intro'] = $m[1];
			$texte_ou_html = preg_replace(",<intro>(.*)</intro>,Uims", '', $texte_ou_html, 1);
			$texte_ou_html = trim($texte_ou_html);
		}
		$contexte['html'] = preg_replace(",</?body>,ims", '', $texte_ou_html);
	} else {
		// la premiere ligne est toujours le sujet
		$texte_ou_html = explode("\n", $texte_ou_html);
		$contexte['sujet'] = trim(array_shift($texte_ou_html));
		$contexte['texte'] = trim(implode("\n", $texte_ou_html));
	}

	// attention : si pas de contenu on renvoi du vide aussi (mail vide = mail vide)
	if (!strlen(trim(implode('', $contexte)))){
		return '';
	}

	return recuperer_fond('emails/texte', $contexte);
}
