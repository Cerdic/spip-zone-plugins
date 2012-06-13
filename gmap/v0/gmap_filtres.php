<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Filtres ps�cifiques utilis�s dans les squelettes (et/ou le code) du plugin
 *
 */

// Filtre pour l'encodage json
// - JSON est support� par des fichier Unicode uniquement, or un site SPIP peut
// rester en AINSI, m�me si ce n'est pas recommand�. Il faut donc faire une conversion
// entre le format du site et UTF-8.
// - La fonction json_encode de PHP n'est support�e qu'en PHP 5, donc �a ne marchera pas
// sur des sites restant en PHP 4 (tant pis, ils se contenteront des KML)
function texte_json($texte)
{
	$siteCharset = $GLOBALS['meta']['charset'];
	if ($siteCharset != 'utf-8')
	{
		include_spip('inc/charsets');
		$texte = unicode_to_utf_8(charset2unicode($texte, $siteCharset));
	}
	return json_encode($texte);
}

// Filtre pour proteger un contenu HTML lors du passage en javascript
function protege_html($html)
{
	$html = str_replace(array("\r\n", "\r", "\n", "\t"), array("", "", "", ""), $html);
	return addcslashes($html, "\"'\\"); // protection des ', " et \
}

// Renvoie le contenu de la balise <body>
function html_body($html)
{
	$html = str_replace(array("\r\n", "\r", "\n", "\t"), array("", "", "", ""), $html);
	if (preg_match('/<body([^>]*)>([\s\S]*)<\/body>/i', $html, $matches) === 1)
		return $matches[2];
	else
		return $html;
}

// Renvoie le contenu de la balise <body>, prot�g�e comme ci-dessus
function protege_html_body($html)
{
	return protege_html(html_body($html));
}

// Protection du titre des marqueurs dans le javascript g�n�r�
function protege_titre($titre)
{
	return addcslashes($titre, "\"'\\"); // protection des ', " et \
}

?>