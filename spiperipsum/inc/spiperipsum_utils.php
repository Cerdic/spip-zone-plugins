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

// Determine le jour de la semaine a partir de la date fournie (0=dimanche)
function date2jour_semaine($date){
	$infos = getdate(strtotime($date));
	return $infos['wday'];
}

// Charger le fichier des lectures et du saint du jour j
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
		$tableau['evangile'] = str_replace('©', '&copy;', $tableau['evangile']);
		// Traitement de la premiere lecture
		$url = "http://www.levangileauquotidien.org/ind-gospel-d.php?language=".$code_langue."&typeRead=FR".$url_date;
		$textes = extraire_balises(recuperer_page($url), 'font');
		$tableau['premiere']['titre'] = $textes[0];
		$tableau['premiere']['verset'] = $textes[1];
		$tableau['premiere']['texte'] = $textes[2];
		$tableau['premiere'] = preg_replace(',</?font\b.*>,UimsS', '', $tableau['premiere']);
		$tableau['premiere'] = preg_replace(',</?br\b.*>,UimsS', '<br />', $tableau['premiere']);
		$tableau['premiere'] = str_replace('©', '&copy;', $tableau['premiere']);
		// Traitement de la seconde lecture - uniquement le dimanche
		if (date2jour_semaine($date) == 0) {
			$url = "http://www.levangileauquotidien.org/ind-gospel-d.php?language=".$code_langue."&typeRead=SR".$url_date;
			$textes = extraire_balises(recuperer_page($url), 'font');
			$tableau['seconde']['titre'] = $textes[0];
			$tableau['seconde']['verset'] = $textes[1];
			$tableau['seconde']['texte'] = $textes[2];
			$tableau['seconde'] = preg_replace(',</?font\b.*>,UimsS', '', $tableau['seconde']);
			$tableau['seconde'] = preg_replace(',</?br\b.*>,UimsS', '<br />', $tableau['seconde']);
			$tableau['seconde'] = str_replace('©', '&copy;', $tableau['seconde']);
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
		$tableau['psaume'] = str_replace('©', '&copy;', $tableau['psaume']);
		// Traitement du commentaire
		// -- titre du commentaire
		$url = "http://feed.evangelizo.org/reader.php?lang=".$code_langue."&type=comment_t&date=".date("Ymd", strtotime($date));
		$page = recuperer_page($url);
		$tableau['commentaire']['titre'] = $page;
		// -- auteur du commentaire
		$url = "http://feed.evangelizo.org/reader.php?lang=".$code_langue."&type=comment_a&date=".date("Ymd", strtotime($date));
		$page = recuperer_page($url);
		$tableau['commentaire']['auteur'] = $page;
		// -- source du commentaire
		$url = "http://feed.evangelizo.org/reader.php?lang=".$code_langue."&type=comment_s&date=".date("Ymd", strtotime($date));
		$page = recuperer_page($url);
		$tableau['commentaire']['source'] = $page;
		$tableau['commentaire'] = preg_replace(',</?br\b.*>,UimsS', '', $tableau['commentaire']);
		$tableau['commentaire'] = preg_replace(',<p\b.*>.*</p\b.*>,UimsS', '', $tableau['commentaire']);
		// -- texte du commentaire
		$url = "http://feed.evangelizo.org/reader.php?lang=".$code_langue."&type=comment&date=".date("Ymd", strtotime($date));
		$page = recuperer_page($url);
		$tableau['commentaire']['texte'] = $page;
		$tableau['commentaire']['texte'] = preg_replace('#(</?br\b.*>)#UimsS', '<br />', $tableau['commentaire']['texte']);
 		$tableau['commentaire']['texte'] = preg_replace('#(&nbsp;){2,}#UimsS', '', $tableau['commentaire']['texte']);
		$tableau['commentaire']['texte'] = preg_replace(',<p\b.*>.*</p\b.*>,UimsS', '', $tableau['commentaire']['texte']);
		$tableau['commentaire']['texte'] = preg_replace(',ú,UimsS', '&oelig;', $tableau['commentaire']['texte']);
		$tableau['commentaire']['texte'] = trim($tableau['commentaire']['texte']);

		// Traitement du saint du jour
		// -- Traitement du nom seul et de l'url permettant de recuperer les textes
		$url = "http://feed.evangelizo.org/reader.php?lang=".$code_langue."&type=saint&date=".date("Ymd", strtotime($date));
		$page = recuperer_page($url);
		$balise = extraire_balises($page, 'a');
		$titre = preg_replace(',</?a\b.*>,UimsS', '', $balise[0]);
		$tableau['saint']['titre'] = preg_replace(',Ü,UimsS', '&dagger;', $titre);
		// -- Traitement des textes
		$attribut = extraire_attribut($balise, 'onclick');
		preg_match(';window.open\(\'(.[^\s,\']+);i', $attribut[0], $url_texte);
 		$page = recuperer_page($url_texte[1]);
		$textes = extraire_balises(extraire_balise($page, 'div'), 'p');
		$tableau['saint']['texte'] = '';		
 		foreach($textes as $p) {
 			if ((!extraire_attribut($p, 'align')) AND (!extraire_attribut($p, 'style')))
				$tableau['saint']['texte'] .= $p;		
		}
		if (!$tableau['saint']['texte'])
			$tableau['saint']['texte'] = preg_replace(',</?div\b.*>,UimsS', '', extraire_balise($page, 'div'));
		$tableau['saint']['texte'] = trim(str_replace('&nbsp;', '', $tableau['saint']['texte']));
		$tableau['saint']['url'] = $url_texte[1];

 		ecrire_fichier($f, serialize($tableau));
	}
	return $f;
}

?>