<?php
if (!defined("_ECRIRE_INC_VERSION")) return;    #securite

function lien_imprim($id_article,$_squel,$_txtlien = NULL)
	{
	$ln = '<div class="mail_imp"><a href="spip.php?page=';
	$ln .= $_squel.'&amp;id_article='.$id_article;
	$ln .= '" target="_blank" onclick="javascript:window.open(\'spip.php?page=';
	$ln .= $_squel.'&amp;id_article='.$id_article;
	$ln .= '&amp;printver=1\', \'print_version\', \'scrollbars=yes, resizable=yes, menubar=yes, width=740, height=580\'); return false;">';
	if(!$_txtlien) 
		{$ln .= _T('public:ver_imprimer');}
	else
		{$ln .= _T("$_txtlien");}
	$ln .= '</a></div>';
	return $ln;
	}

function balise_CRYPTM_IMP($p) {
	// Usage : #CRYPTM_IMP  ou  #CRYPTM_IMP{squelette,texte ou raccourci_de_traduction}
	$id_article = champ_sql('id_article', $p);
	$_squel = interprete_argument_balise(1,$p);
	$_txtlien = interprete_argument_balise(2,$p);
	if(!$_squel) {	$_squel = "cryptm_imp";} // squelette de version imprimable par défaut
	if(!$_txtlien) 
		{$p->code = "lien_imprim($id_article,$_squel)";}
	else 
		{$p->code = "lien_imprim($id_article,$_squel,$_txtlien)";}
	return $p;
}

function cryptm_cryptage($texte) {
// Antispam
// Dernière mis a jour : 1/12/2005
//
// Variables qui peuvent être modifiées a volonté ; mais si $arobase est modifiée
// if faut aussi modifier la fonction JavaScript "dolink"

$arobase = '..&aring;t..';  // tip visible onMouseOver
$affichage = '[Email]';  // affichage par défaut d'un lien mail sans texte

// *** pour version imprimable
if ($_GET['printver']) {
	$masque = '<img src="'.find_in_path('img/commat.gif').'" alt="" width="13" height="13" style="vertical-align: middle;" />';
	preg_match_all('/<a[\\s]{1}[^>]*href=["\']?([^>\\s"\']+)["\']?[^>]*>([^<]*)<\\/a>/is', $texte, $found);
	$total = count($found[0]);
	for($i=0; $i < $total; $i++) {
		if (preg_match("/^mailto:(.*)/",$found[1][$i],$link)) {

		// *** alors il s'agit d'un lien email
		$href = $link[1];
		} else {

		// *** c'est une adresse web
		$href = $found[1][$i];
		}
		$linktext = $found[2][$i];

		// *** si le texte du lien contient déjà l'adresse ou s'il s'agit d'une
		// *** ancre locale on peut jeter l'adresse.
		if ($linktext == $href || strpos($href,"#") === 0) { $href = "";}
		// *** sinon l'ajouter entre crochets.
		if ($href > "") { $href = "[". trim(str_replace("@", $masque,$href)) ."]";}
		if (preg_match("/\s*\S+[\w_-]@[\w_-]\S+\s*/",$linktext)) {
		// *** attention : détecter précisément une adresse mail pour éviter de
		// *** corrompre des raccourcis comme [<imgXX>->quelquun@nullepart.net]
		// *** où Spip code la première partie dans un format
		// *** comme : @@SPIP_SOURCEPROPRE2@@
		$linktext = trim(str_replace("@", $masque,$linktext)." ".$href);
		} else {
		$linktext = trim($linktext." ".$href);
		}
	$texte = str_replace($found[0][$i], $linktext, $texte);
	}
} else {
// *** pour la page a l'écran
// *** agir seulement sur les emails
preg_match_all("/[\"\']mailto:([^@\"']*)@([^\"']*)[\"\']/",$texte,$found);
$total = count($found[0]);
for($i=0; $i < $total; $i++) {
	// *** extraire les deux parties de l'adresse (avant et après l'arobase)
	// *** et réécrire le lien
	$part1 = $found[1][$i];
	$part2 = $found[2][$i];
	$newstr ='"#" title="' . $part1 . $arobase . $part2 . '" onclick="location.href = dolink(this.title); return false;"';
	$texte = str_replace($found[0][$i], $newstr, $texte);
	}

// *** si le texte d'un lien contient une adresse email, le remplacer par le texte choisi
preg_match_all("/>\s*\S+[\w_-]@[\w_-]\S+\s*<\/a/",$texte,$found);
$total = count($found[0]);
for($i=0; $i < $total; $i++) {
	$texte = str_replace($found[0][$i],">$affichage</a",$texte);
	}
}
return $texte;
}
?>
