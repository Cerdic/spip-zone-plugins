<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function typo_guillemets_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/typo_guillemets.css').'" media="all" />'."\n";
	return $flux;
}

/*
Fichier de formatage typographique des guillemets, par Vincent Ramos
<spip_dev AD kailaasa PVNCTVM net>, sous licence GNU/GPL.

Ne sont touchees que les paires de guillemets.

Le formatage des guillemets est tire de
<http://en.wikipedia.org/wiki/Quotation_mark%2C_non-English_usage>
Certains des usages indiques ne correspondent pas a ceux que la
barre d'insertion de caracteres speciaux de SPIP propose.

Les variables suivies du commentaire LRTEUIN sont confirmees par le
_Lexique des regles typographiques en usage a l'Imprimerie nationale_.

Les variables entierement commentees sont celles pour lesquelles
aucune information n'a ete trouvee. Par defaut, les guillements sont alors
de la forme &ldquo;mot&rdquo;, sauf si la barre d'insertion de SPIP proposait
deja une autre forme.
*/
function typo_guillemets_remplacements($texte) {

	// si le texte ne contient pas de guill droit
	// ou s'il contient deja des guillemets élaborés
	// on ne touche pas
	if ((strpos($texte, '"') === false)
	OR (strpos($texte, '&#171;') !== false)
	OR (strpos($texte, '&#187;') !== false)
	OR (strpos($texte, '&#8220;') !== false)
	OR (strpos($texte, '&#8221;') !== false)
	)
		return $texte;

	switch ($GLOBALS['spip_lang']){
		case 'fr':
			$guilles="&laquo;&nbsp;$2&nbsp;&raquo;"; //LRTEUIN
		break;
//		case 'ar':
//			$guilles="";
//		break;
		case 'bg':
			$guilles="&bdquo;$2&ldquo;";
		break;
//		case 'br':
//			$guilles="";
//		break;
//		case 'bs':
//			$guilles="";
//		break;
		case 'ca':
			$guilles="&laquo;$2&raquo;";
		break;
		case 'cpf':
			$guilles="&laquo;&nbsp;$2&nbsp;&raquo;";
		break;
//		case 'cpf_hat':
//			$guilles="";
//		break;
		case 'cs':
			$guilles="&bdquo;$2&ldquo;";
		break;
		case 'da':
			$guilles="&raquo;$2&laquo;";
		break;
		case 'de':
			$guilles="&bdquo;$2&ldquo;"; //ou "&raquo;$2&laquo;" // LRTEUIN
		break;
		case 'en':
			$guilles="&ldquo;$2&rdquo;"; //LRTEUIN
		break;
		case 'eo':
			$guilles="&laquo;$2&raquo;";
		break;
		case 'es':
			$guilles="&laquo;$2&raquo;";
		break;
//		case 'eu':
//			$guilles="";
//		break;
//		case 'fa':
//			$guilles="";
//		break;
//		case 'fon':
//			$guilles="";
//		break;
//		case 'gl':
//			$guilles="";
//		break;
		case 'hu':
			$guilles="&bdquo;$2&rdquo;";
		break;
		case 'it':
			$guilles="&laquo;$2&raquo;";
		break;
		case 'it_fem':
			$guilles="&laquo;$2&raquo;";
		break;
		case 'ja':
			$guilles="&#12300;$2&#12301;";
		break;
//		case 'lb':
//			$guilles="";
//		break;
		case 'nl':
			$guilles="&bdquo;$2&rdquo;";
		break;
//		case 'oc_auv':
//			$guilles="";
//		break;
//		case 'oc_gsc':
//			$guilles="";
//		break;
//		case 'oc_lms':
//			$guilles="";
//		break;
//		case 'oc_lnc':
//			$guilles="";
//		break;
//		case 'oc_ni':
//			$guilles="";
//		break;
//		case 'oc_ni_la':
//			$guilles="";
//		break;
//		case 'oc_prv':
//			$guilles="";
//		break;
//		case 'oc_va':
//			$guilles="";
//		break;
		case 'pl':
			$guilles="&bdquo;$2&rdquo;";
		break;
		case 'pt':
			$guilles="&laquo;$2&raquo;";
		break;
		case 'pt_br':
			$guilles="&laquo;$2&raquo;";
		break;
		case 'ro':
			$guilles="&bdquo;$2&rdquo;";
		break;
		case 'ru':
			$guilles="&laquo;$2&raquo;";
		break;
		case 'tr':
			$guilles="&laquo;$2&raquo;";
		break;
//		case 'vi':
//			$guilles="";
//		break;
		case 'zh':
			$guilles="&#12300;$2&#12301;"; // ou "&ldquo;$2&rdquo;" en chinois simplifie
		break;
		default:
			$guilles="&ldquo;$2&rdquo;";
	}

	// on echappe les " dans les tags ;
	// attention ici \01 est le caractere chr(1), et $m[0] represente le tag
	$texte = preg_replace_callback(',<[^>]*"[^>]*(>|$),msS', function($m) { return str_replace("\'", "'", str_replace('"',"\01", $m[0])); }, $texte);

	// on corrige les guill restants, qui sont par definition hors des tags
	// Un guill n'est pas pris s'il suit un caractere autre que espace, ou
	// s'il est suivi par un caractere de mot (lettre, chiffre)
	$texte = preg_replace('/(^|\s)"\s?([^"]*?)\s?"(\W|$)/S', '$1'.$guilles.'$3', $texte);

	// et on remet les guill des tags
	return str_replace("\01", '"', $texte);
}

