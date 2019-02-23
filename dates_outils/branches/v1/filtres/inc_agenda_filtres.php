<?php
/**
 * Filtre pour la gestion de dates emprunté du plugin agenda
 * Tirée de agenda/inc/agenda_filtres.php.
 * Déclaré deprecies/obsoletes par le plugin
 *
 * @plugin     Dates outils
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Dates_outils\Filtres
 */

/**
 * Afficher de facon textuelle les dates de debut et fin en fonction des cas
 * - Le lundi 20 fevrier a 18h
 * - Le 20 fevrier de 18h a 20h
 * - Du 20 au 23 fevrier
 * - du 20 fevrier au 30 mars
 * - du 20 fevrier 2007 au 30 mars 2008
 *
 * @param string $date_debut : la date de début au format mysql
 * @param string $date_fin : la date de fin au format mysql
 * @param string $horaire : oui / non, permet d'afficher l'horaire, toute autre valeur n'indique que le jour
 * @param string $forme : forme que prendra la date :
 *    - complet (afficher l'année même si anné en cours)
 *    - abbr (afficher le nom des jours en abbrege)
 *    - hcal (generer une date au format hcal)
 *    - h-event (generer une date au format h-event, dans une balise <time> HTML5)
 * @return string
 */

function dates_affdate_debut_fin($date_debut, $date_fin, $horaire = 'oui', $forme = '') {
	$abbr = '';
	if (strpos($forme, 'abbr') !== false) {
		$abbr = 'abbr';
	}
	$affdate = 'affdate_jourcourt';
	if (strpos($forme, 'annee') !== false) {
		$affdate = 'affdate';
	}

	$dtstart = $dtend = $dtabbr = '';
	if (strpos($forme, 'hcal') !== false) {
		$dtstart = "<abbr class='dtstart' title='".date_iso($date_debut)."'>";
		$dtend = "<abbr class='dtend' title='".date_iso($date_fin)."'>";
		$dtabbr = '</abbr>';
	} else if (strpos($forme, 'h-event') !== false) {
		$dtstart = "<time class='dt-start' datetime='".date_iso($date_debut)."'>";
		$dtend = "<time class='dt-end' datetime='".date_iso($date_fin)."'>";
		$dtabbr = '</time>';
	}

	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$ad = date('Y' , $date_debut);
	$af = date('Y' , $date_debut);
	$d = date('Y-m-d', $date_debut);
	$f = date('Y-m-d', $date_fin);
	$h = $horaire == 'oui';
	$hd = date('H:i', $date_debut);
	$hf = date('H:i', $date_fin);
	$au = ' ' . strtolower(_T('dates_outils:date_au')) . ' ';
	$du = _T('dates_outils:date_du') . ' ';
	$s = '';
	if ($d == $f) { // meme jour
		$s = ucfirst(nom_jour($d, $abbr)).' '.$affdate($d);
		if ($h) {
			$s .= " $hd";
		}
		$s = "$dtstart$s$dtabbr";
		if ($h and $hd != $hf) {
			$s .= "-$dtend$hf$dtabbr";
		}
	} elseif ((date('Y-m', $date_debut)) == date('Y-m', $date_fin)) {
		// meme annee et mois, jours differents
		if ($h) {
			$s = $du . $dtstart . affdate_jourcourt($d) . " $hd" . $dtabbr;
			if ($forme == 'complet') {
				$s .= ' ' . $ad;
			}
			$s .= $au . $dtend . $affdate($f);
			if ($forme == 'complet') {
				$s .= ' ' . $ad;
			}
			$s .= " $hf";
			$s .= $dtabbr;
		} else {
			$s = $du . $dtstart . jour($d) . $dtabbr;
			$s .= $au . $dtend . $affdate($f) . $dtabbr;
		}
	} elseif ((date('Y', $date_debut)) == date('Y', $date_fin)) {
		// meme annee, mois et jours differents
		$s = $du . $dtstart . affdate_jourcourt($d);
		if ($forme == 'complet') {
			$s .= ' ' . $ad;
		}
		if ($h) {
			$s .= " $hd";
		}
		$s .= $dtabbr . $au . $dtend . $affdate($f);
		if ($forme == 'complet') {
			$s .= ' ' . $ad;
		}
		if ($h) {
			$s .= " $hf";
		}
		$s .= $dtabbr;
	} else {
		// tout different
		$s = $du . $dtstart . affdate($d);
		if ($forme == 'complet') {
			$s .= ' ' . $ad;
		}
		if ($h) {
			$s .= ' '.date('(H:i)', $date_debut);
		}
		$s .= $dtabbr . $au . $dtend. affdate($f);
		if ($h) {
			$s .= ' '. $af;
		}
		$s .= $dtabbr;
	}
	return unicode2charset(charset2unicode($s, 'AUTO'));
}