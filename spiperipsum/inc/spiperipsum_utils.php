<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_SPIPERIPSUM_FORCER_CHARGEMENT'))
	define('_SPIPERIPSUM_FORCER_CHARGEMENT', false);

// Convertir le code de langue SPIP en code langue du serveur
function langue2code($langue) {
	switch(strtolower($langue))
	{
		case 'br':
		case 'co':
		case 'cpf':
		case 'cpf_hat':
		case 'fr':
		case 'fr_sc':
		case 'fr_lpc':
		case 'fr_lsf':
		case 'fr_spl':
		case 'fr_tu':
		case 'lb':
		case 'oc':
		case 'oc_lnc':
		case 'oc_ni':
		case 'oc_ni_la':
		case 'oc_prv':
		case 'oc_gsc':
		case 'oc_lms':
		case 'oc_auv':
		case 'oc_va':
		case 'rm':
		case 'roa':
		case 'wa':
		case 'ty':
			$code = 'FR'; break;
		case 'en':
		case 'en_hx':
		case 'en_sm':
		case 'ga':
		case 'gd':
			$code = 'AM'; break;
		case 'de':
		case 'fy':
			$code = 'DE'; break;
		case 'an':
		case 'ast':
		case 'ca':
		case 'cpf_dom':
		case 'es':
		case 'es_mx_pop':
		case 'es_co':
		case 'eu':
		case 'gl':
			$code = 'SP'; break;
		case 'pt':
		case 'pt_br':
			$code = 'PT'; break;
		case 'el':
		case 'grc':
			$code = 'GR'; break;
		case 'it':
		case 'it_fem':
		case 'nap':
		case 'la':
		case 'sc':
		case 'scn':
		case 'src':
		case 'sro':
			$code = 'IT'; break;
		case 'ar':
		case 'ber_tam':
		case 'ber_tam_tfng':
			$code = 'AR'; break;
		case 'mg':
			$code = 'MG'; break;
		case 'nl':
			$code = 'NL'; break;
		case 'pl':
			$code = 'PL'; break;
		case 'hy':
			$code = 'ARM'; break;
		default:
			$code = _SPIPERIPSUM_LANGUE_DEFAUT; break;
	}
	return $code;
}

function code2charset($code_langue) {
	switch(strtoupper($code_langue))
	{
		case 'FR':
		case 'PT':
		case 'IT':
		case 'NL':
		case 'AM':
		case 'DE':
		case 'SP':
		case 'TRF':
		case 'TRA':
			$charset = 'iso-8859-1'; break;
		case 'PL':
			$charset = 'iso-8859-2'; break;
		case 'GR':
			$charset = 'iso-8859-7'; break;
		case 'AR':
		case 'MAA':
		case 'BYA':
			$charset = 'windows-1256'; break;
		case 'ARM':
			$charset = 'utf-8'; break;
		default:
			$charset = 'iso-8859-1'; break;
	}
	return $charset;
}

function lecture2code($lecture) {
	switch($lecture)
	{
		case _SPIPERIPSUM_LECTURE_EVANGILE:
			$code = 'GSP'; break;
		case _SPIPERIPSUM_LECTURE_PREMIERE:
			$code = 'FR'; break;
		case _SPIPERIPSUM_LECTURE_SECONDE:
			$code = 'SR'; break;
		case _SPIPERIPSUM_LECTURE_PSAUME:
			$code = 'PS'; break;
		default:
			$code = 'GSP'; break;
	}
	return $code;
}


// Convertir la date Y-m-d en url avec l'annee, le mois et le jour
function date2url_date($date) {
	$url = '';
	$infos = getdate(strtotime($date));
	$url = '&year=' . $infos['year'] . '&month=' . $infos['mon'] .'&day=' . $infos['mday'];
	return $url;
}

// Determine le jour de la semaine a partir de la date fournie (0=dimanche)
function date2jour_semaine($date) {
	$infos = getdate(strtotime($date));
	return $infos['wday'];
}


// Determine le jour de la semaine a partir de la date fournie (0=dimanche)
function page2page_propre($page, $charset, $no_tag=false) {
	static $nettoyage_commun = array(
				'regexp' => array('#(<|&lt;)/?br\b.*(>|&gt;)#UimsS', '#(&nbsp;){2,}#UimsS'),
				'replace' => array('<br />', '')
	);
	static $nettoyage_charset = array(
				'iso-8859-1' => array(
						'regexp' => array('#Å#UimsS', ',Ü,UimsS'),
						'replace' => array('&oelig;', '&dagger;')),
	);

	$regexp = $nettoyage_commun['regexp'];
	$replace = $nettoyage_commun['replace'];
	if (isset($nettoyage_charset[$charset])) {
		$regexp = array_merge($regexp, $nettoyage_charset[$charset]['regexp']);
		$replace = array_merge($replace, $nettoyage_charset[$charset]['replace']);
	}

	if ($no_tag) {
		$regexp = array_merge($regexp, array('#<p\b.*>.*</p>#UimsS', '#<a\b.*>.*</a>$#UimsS', '#(<br />)+$#UimS', '#</?font\b.*>#UimsS'));
		$replace = array_merge($replace, array('', '', '', ''));
	}

	$page = preg_replace($regexp, $replace, $page);

	return trim($page);
}

function flux2element($url, $charset, $no_tag=false) {
	$element = '';

	$page = recuperer_page($url);
	if (strpos($page, 'Error : ') === false) {
		$page = page2page_propre(importer_charset($page, $charset), $charset, $no_tag);
		$element = $page;
	}

	return $element;
}

function flux2texte($url, $charset, $lettrine=false) {
	$texte = array('texte' => '', 'copyright' => '', 'credit' => '');

	$page = recuperer_page($url);
	if (strpos($page, 'Error : ') === false) {
		$page = page2page_propre(importer_charset($page, $charset), $charset, false);
		$segments = explode('<br />', $page);

		$index = count($segments) - 1;
		$texte['credit'] = trim(extraire_balise($segments[$index], 'a'));
		unset($segments[$index]);

		$index = $index - 1;
		$texte['copyright'] = trim($segments[$index]);
		unset($segments[$index]);

		$index = $index - 1;
		if (preg_match('#<script\b.*>.*</script\b.*>#UimsS', $segments[$index], $t)) {
			unset($segments[$index]);
			$index = $index - 1;
		}
		while (!$segments[$index] AND $index>0) {
			unset($segments[$index]);
			$index = $index - 1;
		}
		$page = trim(implode('<br />', $segments));
		if ($lettrine) {
			$lettre = mb_substr($page, 0, 1, $GLOBALS['meta']['charset']);
			$texte['texte'] = '<span class="lettrine">' . $lettre . '</span>' . mb_substr($page, 1);
		}
		else
			$texte['texte'] = $page;

	}
	return $texte;
}


function flux2lecture($lecture, $url_base, $charset, $lettrine=false) {
	$tableau = array();

	// -- Titre court de l'evangile : on extrait la reference du verset uniquement
	//    Ce titre court est de la forme "Lc 11,25-23"
	$no_tag = true;
	$url = $url_base . '&type=reading_st&content=' . lecture2code($lecture);
	$tableau['verset'] = flux2element($url, $charset, $no_tag);

	// -- Titre long de l'evangile : on extrait le titre uniquement sans le verset
	//    Ce titre long est de la forme "bla bla 11,25-23."
	$url = $url_base . '&type=reading_lt&content=' . lecture2code($lecture);
	$tableau['titre'] = flux2element($url, $charset, $no_tag);

	// -- Texte de la lecture
	//    On decoupe le texte en 3 parties : le texte proprement dit, sa reference de traduction et un credit
    $no_tag = false;
	$url = $url_base . '&type=reading&content=' . lecture2code($lecture);
	$textes = flux2texte($url, $charset, $lettrine);
	$tableau = array_merge($tableau, $textes);

	return $tableau;
}


function flux2commentaire($url_base, $charset) {
	$tableau = array();

	$no_tag = true;
	// -- titre du commentaire
	$tableau['titre'] = flux2element($url_base.'&type=comment_t', $charset, $no_tag);

	// -- auteur du commentaire
	$tableau['auteur'] = flux2element($url_base.'&type=comment_a', $charset, $no_tag);

	// -- source du commentaire
	$tableau['source'] = flux2element($url_base.'&type=comment_s', $charset, $no_tag);

	// -- texte du commentaire : on n'insère jamais de lettrine
	$textes = flux2texte($url_base.'&type=comment', $charset, false);
	$tableau = array_merge($tableau, $textes);

	return $tableau;
}


function flux2saint($url_base, $charset) {
	$tableau = array('titre' => '', 'url' => '', 'texte' => '');

	$page = recuperer_page($url_base.'&type=saint');
	if (strpos($page, 'Error : ') === false) {
		// Traitement du nom seul et de l'url permettant de recuperer les textes
		// -- nom
		$balises_a = extraire_balises($page, 'a');
		$titre = preg_replace(',</?a\b.*>,UimsS', '', $balises_a[0]);
		$tableau['titre'] = page2page_propre(importer_charset($titre, $charset), $charset, false);

		// -- url
		$attribut = extraire_attribut($balises_a, 'onclick');
		preg_match(';window.open\(\'(.[^\s,\']+);i', $attribut[0], $url_texte);
		$tableau['url'] = $url_texte[1];

		// -- Traitement des textes
		$page = recuperer_page($url_texte[1]);
		if (strpos($page, 'Error : ') === false) {
			$contenu = extraire_balise($page, 'body');
			$contenu = strip_tags($contenu, '<p><em>');
			$contenu = preg_replace(',<em\b.*>,UimsS', '<em>', $contenu);
			$balises_p = extraire_balises($contenu, 'p');
			foreach($balises_p as $_cle => $_balise_p) {
				// TODO : cela fonctionne car le premier <p> est mal fermé donc englobe le deuxième qui contient
				// TODO : le titre qui devrait être supprimé
				if (!extraire_attribut($_balise_p, 'align')) {
					$contenu_p = trim(strip_tags($_balise_p, '<em>'));
					if ($contenu_p) {
						if (mb_strlen($contenu_p, $GLOBALS['meta']['charset']) == 1)
							$tableau['texte'] .= '<span class="lettrine">' . $contenu_p . '</span>';
						else
							$tableau['texte'] .= $contenu_p . '<br />';
					}
				}
			}

			if (!$tableau['texte'])
				$tableau['texte'] = preg_replace(',</?div\b.*>,UimsS', '', extraire_balise($page, 'div'));

			$tableau['texte'] = page2page_propre(importer_charset($tableau['texte'], $charset), $charset, false);
			$tableau['texte'] = preg_replace('#(<br />)+$#UimS', '', $tableau['texte']);
			$tableau['texte'] = trim(str_replace('&nbsp;', '', $tableau['texte']));
		}
	}

	return $tableau;
}


function flux2fete($url_base, $charset) {
	$tableau = array('titre' => '', 'url' => '', 'texte' => '');

	$page = recuperer_page($url_base.'&type=feast');
	if ($page AND (strpos($page, 'Error : ') === false)) {
		// -- nom
		$balises_a = extraire_balises($page, 'a');
		$titre = preg_replace(',</?a\b.*>,UimsS', '', $balises_a[0]);
		$tableau['titre'] = page2page_propre(importer_charset($titre, $charset), $charset, false);

		// -- url
		$attribut = extraire_attribut($balises_a, 'onclick');
		preg_match(';window.open\(\'(.[^\s,\']+);i', $attribut[0], $url_texte);
		$tableau['url'] = $url_texte[1];
	}

	return $tableau;
}


function flux2date($url_base, $charset, $date) {
	$tableau = array('date_iso' => '', 'date_liturgique' => '');

	// Date iso
	$tableau['date_iso'] = $date;

	// Date liturgique
	$no_tag = false;
	$url = $url_base . '&type=liturgic_t';
	$tableau['date_liturgique'] = flux2element($url, $charset, $no_tag);

	return $tableau;
}


// Charger le fichier des lectures et du saint du jour j
// - si le fichier existe on retourne directement son nom complet
// - sinon on le cree dans le cache du plugin
function charger_lectures($langue, $jour) {

	include_spip('inc/charsets');

	$date = ($jour == _SPIPERIPSUM_JOUR_DEFAUT) ? date('Y-m-d') : $jour;
	$code_langue = langue2code($langue);
	$charset = code2charset($code_langue);
	$lettrine = ($code_langue == 'AR' OR $code_langue == 'ARM') ? false : true;

	$dir = sous_repertoire(_DIR_CACHE,"spiperipsum");
	$dir = sous_repertoire($dir,substr(md5($code_langue),0,1));
	$cache = $dir . $code_langue . "_" . $date . ".txt";

	if (!file_exists($cache) OR _SPIPERIPSUM_FORCER_CHARGEMENT) {
		// Determination de la sous-chaine url correspondant a la date (vide si jour courant)
		$url_date = ($jour == _SPIPERIPSUM_JOUR_DEFAUT) ? '' : date2url_date($date);
		// Url de base de tous les flux
		$url_base = 'http://feed.evangelizo.org/reader.php?lang=' . $code_langue . '&date=' . date('Ymd', strtotime($date));

		// traitement des différentes versions de la date
		$tableau['date'] = flux2date($url_base, $charset, $date);

		// Traitement de l'evangile
		$tableau['evangile'] = flux2lecture(_SPIPERIPSUM_LECTURE_EVANGILE, $url_base, $charset, $lettrine);

		// Traitement de la premiere lecture
		$tableau['premiere'] = flux2lecture(_SPIPERIPSUM_LECTURE_PREMIERE, $url_base, $charset, $lettrine);

		// Traitement de la seconde lecture - uniquement le dimanche
		if (date2jour_semaine($date) == 0) {
			$tableau['seconde'] = flux2lecture(_SPIPERIPSUM_LECTURE_SECONDE, $url_base, $charset, $lettrine);
		}

		// Traitement du psaume
		$tableau['psaume'] = flux2lecture(_SPIPERIPSUM_LECTURE_PSAUME, $url_base, $charset, $lettrine);

		// Traitement du commentaire
		$tableau['commentaire'] = flux2commentaire($url_base, $charset);

		// Traitement du saint du jour
		$tableau['saint'] = flux2saint($url_base, $charset);

		// Traitement de la fête du jour
		// TODO : à faire
		$tableau['fete'] = flux2fete($url_base, $charset);

//		var_dump($tableau);
 		ecrire_fichier($cache, serialize($tableau));
	}
	return $cache;
}

?>