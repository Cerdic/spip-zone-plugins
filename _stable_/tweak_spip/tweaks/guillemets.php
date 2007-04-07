<?php
// integration 2007 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1592

/*
Fichier de formatage typographique des guillemets, par Vincent Ramos
<www-lansargues AD kailaasa POINT net>, sous licence GNU/GPL.

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

function typo_guillemets_callback($matches) {
 // en utilisant 'TWEAKS', on permet a la fonction tweak_exclure_balises() 
 // de retablir a la fin le code original
 return str_replace('"', "'", code_echappement($matches[1], 'TWEAKS'));
}

function typo_guillemets_echappe_balises($texte) {
 return preg_replace_callback('/(<[^>]+"[^>]*>)/Ums', 'typo_guillemets_callback', $texte);
}

function typo_guillemets_rempl($texte){
	if (strpos($texte, '"')===false) return $texte;
	// prudence : on protege TOUTES les balises contenant des guillemets
	if (strpos($texte, '<')!==false) $texte = typo_guillemets_echappe_balises($texte);

	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	switch ($lang) {
//	switch ($GLOBALS['spip_lang']){
		case 'fr':
			$guilles="&laquo;&nbsp;$1&nbsp;&raquo;"; //LRTEUIN
		break;
//		case 'ar':
//			$guilles="";
//		break;
		case 'bg':
			$guilles="&bdquo;$1&ldquo;";
		break;
//		case 'br':
//			$guilles="";
//		break;
//		case 'bs':
//			$guilles="";
//		break;
		case 'ca':
			$guilles="&laquo;$1&raquo;";
		break;
		case 'cpf':
			$guilles="&laquo;&nbsp;$1&nbsp;&raquo;";
		break;
//		case 'cpf_hat':
//			$guilles="";
//		break;
		case 'cs':
			$guilles="&bdquo;$1&ldquo;";
		break;
		case 'da':
			$guilles="&raquo;$1&laquo;";
		break;
		case 'de':
			$guilles="&bdquo;$1&ldquo;"; //ou "&raquo;$1&laquo;" // LRTEUIN
		break;
		case 'en':
			$guilles="&ldquo;$1&rdquo;"; //LRTEUIN
		break;
		case 'eo':
			$guilles="&laquo;$1&raquo;";
		break;
		case 'es':
			$guilles="&laquo;$1&raquo;";
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
			$guilles="&bdquo;$1&rdquo;";
		break;
		case 'it':
			$guilles="&laquo;$1&raquo;";
		break;
		case 'it_fem':
			$guilles="&laquo;$1&raquo;";
		break;
		case 'ja':
			$guilles="&#12300;$1&#12301;";
		break;
//		case 'lb':
//			$guilles="";
//		break;
		case 'nl':
			$guilles="&bdquo;$1&rdquo;";
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
			$guilles="&bdquo;$1&rdquo;";
		break;
		case 'pt':
			$guilles="&laquo;$1&raquo;";
		break;
		case 'pt_br':
			$guilles="&laquo;$1&raquo;";
		break;
		case 'ro':
			$guilles="&bdquo;$1&rdquo;";
		break;
		case 'ru':
			$guilles="&laquo;$1&raquo;";
		break;
		case 'tr':
			$guilles="&laquo;$1&raquo;";
		break;
//		case 'vi':
//			$guilles="";
//		break;
		case 'zh':
			$guilles="&#12300;$1&#12301;"; // ou "&ldquo;$1&rdquo;" en chinois simplifie
		break;
		default:
			$guilles="&ldquo;$1&rdquo;";
	}
	// Remplacement des autres paires de guillemets (et suppression des espaces apres/avant)
	return preg_replace('/"\s*(.*?)\s*"/', $guilles, $texte);
}

function typo_guillemets($texte){
	if (strpos($texte, '"')===false) return $texte;
	return tweak_exclure_balises('html|code|cadre|frame|script|acronym|cite', 'typo_guillemets_rempl', $texte);
}

?>