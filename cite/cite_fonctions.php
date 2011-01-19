<?php

function cite_authors_ris ($auts) {
	if (trim($auts)) {
		$auts = explode(';',$auts);
		foreach ($auts as $cle => $aut) {
			$aut = explode(',',$aut,2);
			$auts[$cle] = 'A1  - '.trim($aut[0]).',';
			if ($aut[1])
				$auts[$cle] .= trim($aut[1]);
		}
		return implode("\n",$auts);
	}
	else return '';
}

function cite_authors_bibtex ($auts) {
	if (trim($auts)) {
		$auts = explode(';',$auts);
		foreach ($auts as $cle => $aut) {
			$aut = explode(',',$aut,2);
			$auts[$cle] = trim($aut[0]).',';
			if ($aut[1])
				$auts[$cle] .= " ".trim($aut[1]);
		}
		return 'author = {'.implode(" and ",$auts).'}';
	}
	else return '';
}

function cite_pages_ris ($pages) {
	if (trim($pages)) {
		$pages = explode('-',trim($pages));
		$ret = "SP  - ".$pages[0];
		if (isset($pages[1]))
			$ret .= "\nEP  - ".$pages[1];
		return $ret;
	}
	else return '';
}

function cite_editors_ris ($auts) {
	if (trim($auts)) {
		$auts = explode(';',$auts);
		foreach ($auts as $cle => $aut) {
			$aut = explode(',',$aut,2);
			$auts[$cle] = 'ED  - '.trim($aut[0]).',';
			if ($aut[1])
				$auts[$cle] .= trim($aut[1]);
		}
		return implode("\n",$auts);
	}
	else return '';
}

function cite_editors_bibtex ($auts) {
	if (trim($auts)) {
		$auts = explode(';',$auts);
		foreach ($auts as $cle => $aut) {
			$aut = explode(',',$aut,2);
			$auts[$cle] = trim($aut[0]).',';
			if ($aut[1])
				$auts[$cle] .= " ".trim($aut[1]);
		}
		return 'editor = {'.implode(" and ",$auts).'}';
	}
	else return '';
}

function bibtex_encode($texte) {
	include_spip('inc/transtab_unicode_bibtex');
	foreach (transtab_unicode_bibtex() as $utf8 => $bibtex)
		$texte = str_replace($utf8,$bibtex,$texte);
	return $texte;
}

function bibtex_month($month) {
	$mois = array(
		1 => 'jan',
		2 => 'feb',
		3 => 'mar',
		4 => 'apr',
		5 => 'may',
		6 => 'jun',
		7 => 'jul',
		8 => 'aug',
		9 => 'sep',
		10 => 'oct',
		11 => 'nov',
		12 => 'dec'
	);
	return $mois[intval($month)];
}

?>