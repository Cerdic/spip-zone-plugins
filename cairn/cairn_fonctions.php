<?php

// stocker une chaine dans un CDATA
// a noter qu'il faut "echapper" un eventuel "]]>"
// http://www.w3.org/TR/xml/#charsets
function filtre_cdata($t) {
	if (preg_match(',[<>&\x0-\x8\xb-\xc\xe-\x1f],u', $t)) {
		$t = preg_replace('/[\x0-\x8\xb-\xc\xe-\x1f]/ue', '"&#x".bin2hex(\'$0\').";"', $t);
		return "<![CDATA[" . str_replace(']]>', ']]]]><![CDATA[>', $t).']]>';
	} else
		return $t;
}

define('_CHEVRONA', '* [oo *');
define('_CHEVRONB', '* oo] *');

// transformer les images ou logos spip en "figure" cairn
function cairn_figure($html, $numero, $titre=null, $desc=null) {
	static $cpt = array();

	$c = ++ $cpt[$numero];
	foreach (extraire_balises($html, 'figure') as $fig) {
		$legende = extraire_balise($fig, 'figcaption');
		$titre = extraire_balise($legende, 'h3');
		if ($titre) $legende = str_replace($titre,'', $legende);

		$titre = supprimer_tags($titre);
		$legende = supprimer_tags($legende);
		$figure = cairn_figure(extraire_balise($fig,'img'), $numero, $titre,$legende);
		$html = str_replace($fig, $figure, $html);
	}

	foreach (extraire_balises($html, 'img') as $img) {

		$src = extraire_attribut($img, 'src');

		if ($src AND $l = copie_locale(url_absolue($src))) {
			$file = "images/".basename($l);
			@mkdir("$numero/images");
			rename($l, "$numero/$file");

			$ext = preg_replace(',^.*\.,', '', $file);
			if ($ext == 'jpg') $ext = 'jpeg';
		}

		if ($titre)
			$titre = "<titre>".filtre_cdata(trim($titre))."</titre>";
		if ($desc)
			$desc = "<alinea>".filtre_cdata(trim($desc))."</alinea>";
		if ($titre OR $desc) {
			$legende = "    <legende lang='fr'>
        $titre
        $desc
        </legende>
      ";
		} else {
			$legende = '';
		}

		$figure = "<figure id='fi$c'>
$legende
      <objetmedia>
            <image id='im$c' typeimage='figure' typemime='image:$ext' xlink:type='simple' xlink:href='$file' xlink:actuate='onRequest' />
      </objetmedia>
</figure>
";

		$html = str_replace($img, $figure, $html);

	}

	return $html;
}

function cairn_prenom_nom($blaze) {
	return preg_replace(",(.*)[*_](.*),Se",'\'<prenom>\'.filtre_cdata(\'$2\').\'</prenom> <nomfamille>\'.filtre_cdata(\'$1\').\'</nomfamille>\'',$blaze);
}

// convertir un HTML en format eruditArticle
function cairn_traiter($t, $reset) {

	$t = cairn_decoupe_h3($t, $reset);

	return str_replace(array(_CHEVRONA,_CHEVRONB), array('<', '>'), $t);
}

// convertir un HTML en format eruditArticle
function cairn_traiter_notes($t, $reset) {

	$t = cairn_decoupe_para_cdata($t, $reset);

	return str_replace(array(_CHEVRONA,_CHEVRONB), array('<', '>'), $t);
}

function cairn_decoupe_h3($texte, $reset) {
	static $cpt;
	if ($reset) $cpt=0;

	if (!strlen(trim($texte))) return '';

	$sections = preg_split('/<h[23]\b[^>]*>/i', $texte);

	$t = array_shift($sections);
	if (strlen($t)) {
		$cpt ++;
		$t = _CHEVRONA."section1 id=\"s1n$cpt\""._CHEVRONB
			. cairn_decoupe_para_cdata($t, $reset)
			. _CHEVRONA."/section1"._CHEVRONB;
	}

	foreach ($sections as $p) {
		$cpt++;
		list($para, $suite) = preg_split(',</h3\b[^>]*>,i', $p);

		$t .= _CHEVRONA."section1 id=\"s1n$cpt\""._CHEVRONB
			. _CHEVRONA."titre"._CHEVRONB.cairn_decoupe_para_cdata($para)._CHEVRONA."/titre"._CHEVRONB
			. cairn_decoupe_para_cdata($suite)
			. _CHEVRONA."/section1"._CHEVRONB;
	}

	return $t;

}

function callback_poesie($r) {
	# <verbatim typeverb="poeme"><bloc><ligne>………
	$p = $r[1];
	$p = preg_replace(',<div>(.*)</div>,UimsS',
		_CHEVRONA.'ligne'._CHEVRONB
		.'\1'.
		_CHEVRONA.'/ligne'._CHEVRONB, $p);

	return _CHEVRONA.'verbatim typeverb="poeme"'._CHEVRONB
		._CHEVRONA.'bloc'._CHEVRONB
		.$p
		._CHEVRONA.'/bloc'._CHEVRONB
		._CHEVRONA.'/verbatim'._CHEVRONB;
}

function cairn_decoupe_para_cdata($texte, $reset=false) {
	static $cpt;

	if ($reset) $cpt=0;

	// la poesie
	$texte = preg_replace_callback(
	',<blockquote class="spip_poesie">(.*)</blockquote>,UimsS',
	'callback_poesie', $texte);

	// UL et OL doivent être dans des para
	$texte = preg_replace(',<(ol|ul)\b,iS', '<p>$0', $texte);
	$texte = preg_replace(',</(ol|ul)\b[^>]*>,iS', '$0</p>', $texte);

	// sauts de ligne
	$texte = preg_replace(',<br\b[^>]*>,iS', _CHEVRONA."br /"._CHEVRONB."\n", $texte);

	// liens a href
	foreach (extraire_balises($texte, 'a') as $l) {
		if (preg_match('/^http/', extraire_attribut($l, 'href'))) {
			$lien = preg_replace(',<a,i', "<liensimple", $l);
			$lien = str_replace('href=', "xlink:href=", $lien);
			$lien = preg_replace(',</a,i', "</liensimple", $lien);
			$lien = str_replace(array('<','>'), array(_CHEVRONA, _CHEVRONB), $lien);
			$texte = str_replace($l, $lien, $texte);
		}
	}

	// images (seront traitees a la fin)
	foreach (extraire_balises($texte, 'figure') as $l) {
		$l2 = str_replace(array('<','>'), array(_CHEVRONA, _CHEVRONB), $l);
		$texte = str_replace($l, $l2, $texte);
	}

	// images simples
	foreach (extraire_balises($texte, 'img') as $l) {
		$l2 = str_replace(array('<','>'), array(_CHEVRONA, _CHEVRONB), $l);
		$texte = str_replace($l, $l2, $texte);
	}

	$paragraphes = preg_split('/<p\b[^>]*>/i', $texte);

	# traiter le premier bloc ; ce n'est pas un paragraphe
	# (il est peut-être vide, d'ailleurs)
	$t = filtrer_texte_cairn(array_shift($paragraphes));

	# traiter les para suivants
	# en <para id="pa2"><alinea> ... </alinea></para> + reste
	foreach ($paragraphes as $p) {
		$cpt++;
		list($para, $suite) = preg_split(',</p\b[^>]*>,i', $p);

		if ($a = extraire_balise($para, 'a')
		AND extraire_attribut($a, 'rev') == 'footnote') {
			$note = supprimer_tags($a);
			$t .= "<note id=\"no$note\"><alinea>"
				. filtrer_texte_cairn(str_replace($a, supprimer_tags($a), $para))
				. "</alinea></note>"
				. filtrer_texte_cairn($suite);
		}
		else
			$t .= "<para id=\"pa$cpt\"><alinea>"
				. filtrer_texte_cairn($para)
				. "</alinea></para>"
				. filtrer_texte_cairn($suite);
	}

	return $t;
}

function filtrer_texte_cairn($t) {
	$t = preg_replace(',<(i|em)\b[^>]*>(.*)</\1>,UimsS', _CHEVRONA.'marquage typemarq="italique"'._CHEVRONB.'$2'._CHEVRONA.'/marquage'._CHEVRONB, $t);
	$t = preg_replace(',<(b|strong)\b[^>]*>(.*)</\1>,UimsS', _CHEVRONA.'marquage typemarq="gras"'._CHEVRONB.'$2'._CHEVRONA.'/marquage'._CHEVRONB, $t);

	$t = preg_replace(',<(ul)\b[^>]*>(.*)</\1>,UimsS', _CHEVRONA.'listenonord signe="disque"'._CHEVRONB.'$2'._CHEVRONA.'/listenonord'._CHEVRONB, $t);
	$t = preg_replace(',<(ol)\b[^>]*>(.*)</\1>,UimsS', _CHEVRONA.'listeord numeration="decimal"'._CHEVRONB.'$2'._CHEVRONA.'/listeord'._CHEVRONB, $t);
	$t = preg_replace(',<(li)\b[^>]*>(.*)</\1>,UimsS', _CHEVRONA.'elemliste'._CHEVRONB._CHEVRONA.'alinea'._CHEVRONB.'$2'._CHEVRONA.'/alinea'._CHEVRONB._CHEVRONA.'/elemliste'._CHEVRONB, $t);


	// appels de notes
	# <renvoi id="reXnoY" idref="noY" typeref="note">X</renvoi>
	foreach (extraire_balises($t, 'a') as $a) {
		if (extraire_attribut($a, 'class') == 'spip_note') {
			if (extraire_attribut($a, 'rel') == 'footnote') {
				$n = supprimer_tags($a);
				$b = _CHEVRONA."renvoi id=\"re".$n."no".$n."\" idref=\"no".$n."\" typeref=\"note\""._CHEVRONB.$n._CHEVRONA."/renvoi"._CHEVRONB;
				$t = str_replace($a, $b, $t);
			}
		}
	}

	$t = proteger_amp(unicode_to_utf_8(html2unicode($t)));
	$t = str_replace('&#8217;', '’', $t);

	$t = trim(supprimer_tags($t));


	return $t;

	#return filtre_cdata($t);
}



?>