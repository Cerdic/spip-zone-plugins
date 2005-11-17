<?php

//Quelques codes de langues
//$GLOBALS['i18n_spip_fr']['table_matiere'] = 'Table des mati&egrave;res';
//fausse des codes si place ici.

//
// http://www.spip-contrib.net/spikini/VarianteContribAjouter-des-ID-aux-intertitres
//
function ancres_intertitres($texte) {
	$regexp = "/{{{[[:space:]]*(.+)[[:space:]]*}}}/";
	$texte = preg_replace_callback($regexp, 'remplace_intertitre', $texte);
	return $texte;
}

function remplace_intertitre($matches) {
	static $cId = 0;
	$cId++;
	$url = translitteration(corriger_caracteres(
		supprimer_tags(supprimer_numero(extraire_multi($matches[1])))
	));
	$url = @preg_replace(',[[:punct:][:space:]]+,u', ' ', $url);
	// S'il reste des caracteres non latins, utiliser l'id a la place
	if (preg_match(",[^a-zA-Z0-9 ],", $url)) {
		$url = "ancre$cId";
	}
	else {
		$mots = explode(' ', $url);
		$url = '';
		foreach ($mots as $mot) {
			if (!$mot) continue;
			$url2 = $url.'-'.$mot;
			if (strlen($url2) > 35) {
				break;
			}
		$url = $url2;
		}
		$url = substr($url, 1);
		if (strlen($url) < 2) $url = "ancre$cId";
	}
	table_matiere('', $url, $matches[1]);
	return '{{{ ['.$url.'<-] '.$matches[1].' }}}';
}

//
//balise #TABLE_MATIERE
//
function balise_TABLE_MATIERE_dist($p) {
	$p->code = "
	compose_table_matiere(
		'\t<li><a href=\"#@url@\">@titre@</a></li>\n',
		'\n<ul>\n@texte@</ul>\n',
		table_matiere(\"retour\")
	)";
	$p->statut = 'php';
	return $p;
}

function table_matiere($mode = '', $url = '', $titre ='') {
	static $tableau = array();
	if($mode == 'retour') return $tableau;
	$tableau[$url] = $titre;
	return '';
}

function compose_table_matiere($cadre_lien,	$cadre_global, $table_matiere) {
	$texte = '';
	if(!empty($table_matiere))
		foreach($table_matiere as $url => $titre)
			$texte .= preg_replace(array(',@url@,', ',@titre@,'), array($url, $titre), $cadre_lien);
	return $texte ? preg_replace(',@texte@,', $texte, $cadre_global) : '';	
}

?>
