<?php
// Convertir le code de langue SPIP en code langue du serveur
function langue2code($langue){
	switch(strtolower($langue))
	{
		case 'fr':
		case 'co':
		case 'fr_tu':
		case 'oc':
		case 'oc_lnc':
		case 'oc_ni':
		case 'oc_ni_la':
		case 'oc_prv':
		case 'oc_gsc':
		case 'oc_lms':
		case 'oc_auv':
		case 'oc_va':
		case 'roa':
		case 'ty':
			$code = 'FR'; break;
		case 'en':
		case 'en_hx':
		case 'ga':
		case 'gd':
			$code = 'AM'; break;
		case 'de':
			$code = 'DE'; break;
		case 'es':
		case 'an':
		case 'ast':
		case 'ca':
		case 'es_co':
		case 'eu':
		case 'gl':
		case 'la':
			$code = 'SP'; break;
		case 'pt':
		case 'pt_br':
			$code = 'PT'; break;
		case 'it':
		case 'it_fem':
		case 'nap':
		case 'ro':
		case 'sc':
		case 'scn':
		case 'src':
		case 'sro':
			$code = 'IT'; break;
		case 'nl':
			$code = 'NL'; break;
		case 'trf':  $code = 'TRF'; break;
		case 'maa':  $code = 'MAA'; break;
		default: 
			$code = _SPIPERIPSUM_LANGUE_DEFAUT; break;
	}
	return $code;
}

// Convertir la date Y-m-d en url avec l'annee, le mois et le jour
function date2url_date($date){
	$url = '';
	$infos = getdate(strtotime($date));
	$url = '&year=' . $infos['year'] . '&month=' . $infos['mon'] .'&day=' . $infos['mday'];
	return $url;
}

// Charger le fichier des lectures du jour j
// - si le fichier existe on retourne directement son nom complet
// - sinon on le cree dans le cache du plugin
function charger_lectures($langue, $jour){

	$date = ($jour == _SPIPERIPSUM_JOUR_DEFAUT) ? date('Y-m-d') : $jour;
	$code_langue = langue2code($langue);
	
	$dir = sous_repertoire(_DIR_CACHE,"spiperipsum");
	$dir = sous_repertoire($dir,substr(md5($code_langue),0,1));
	$f = $dir . $code_langue . "_" . $date . ".txt";

	if (!file_exists($f)) {
		// Determination de la sous-chaine url correspondant a la date (vide si jour courant)
		$url_date = ($jour == _SPIPERIPSUM_JOUR_DEFAUT) ? '' : date2url_date($date);
		// Date du jour
		$tableau['date'] = $date;
		// Traitement de l'evangile
		$url = "http://www.levangileauquotidien.org/ind-gospel-d.php?language=".$code_langue.$url_date;
		$textes = extraire_balises(recuperer_page($url), 'font');
		$tableau['evangile']['titre'] = $textes[0];
		$tableau['evangile']['verset'] = $textes[1];
		$tableau['evangile']['texte'] = $textes[2];
		$tableau['evangile'] = preg_replace(',</?font\b.*>,UimsS', '', $tableau['evangile']);
		$tableau['evangile'] = preg_replace(',</?br\b.*>,UimsS', '<br />', $tableau['evangile']);
		// Traitement de la premiere lecture
		$url = "http://www.levangileauquotidien.org/ind-gospel-d.php?language=".$code_langue."&typeRead=FR".$url_date;
		$textes = extraire_balises(recuperer_page($url), 'font');
		$tableau['premiere']['titre'] = $textes[0];
		$tableau['premiere']['verset'] = $textes[1];
		$tableau['premiere']['texte'] = $textes[2];
		$tableau['premiere'] = preg_replace(',</?font\b.*>,UimsS', '', $tableau['premiere']);
		$tableau['premiere'] = preg_replace(',</?br\b.*>,UimsS', '<br />', $tableau['premiere']);
		// Traitement de la seconde lecture
		$url = "http://www.levangileauquotidien.org/ind-gospel-d.php?language=".$code_langue."&typeRead=SR".$url_date;
		$textes = extraire_balises(recuperer_page($url), 'font');
		if ($textes) {
			$tableau['seconde']['titre'] = $textes[0];
			$tableau['seconde']['verset'] = $textes[1];
			$tableau['seconde']['texte'] = $textes[2];
			$tableau['seconde'] = preg_replace(',</?font\b.*>,UimsS', '', $tableau['seconde']);
			$tableau['seconde'] = preg_replace(',</?br\b.*>,UimsS', '<br />', $tableau['seconde']);
		}
		// Traitement du psaume
		$url = "http://www.levangileauquotidien.org/ind-gospel-d.php?language=".$code_langue."&typeRead=PS".$url_date;
		$page = recuperer_page($url);
		// -- on traite façon particuliere les numéros de verset du psaume
		preg_match('/,[0-9\.\-]*\./', $page, $fin_verset);
		$textes = extraire_balises($page, 'font');
		$tableau['psaume']['titre'] = $textes[0];
		$tableau['psaume']['verset'] = $textes[1] . $fin_verset[0];
		$tableau['psaume']['texte'] = $textes[2];
		$tableau['psaume'] = preg_replace(',</?font\b.*>,UimsS', '', $tableau['psaume']);
		$tableau['psaume'] = preg_replace(',</?br\b.*>,UimsS', '<br />', $tableau['psaume']);

 		ecrire_fichier($f, serialize($tableau));
	}
	return $f;
}

?>