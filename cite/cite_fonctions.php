<?php

function cite_authors_ris ($auts) {
	$auts = explode(';',$auts);
	foreach ($auts as $cle => $aut) {
		$aut = explode(',',$aut,2);
		$auts[$cle] = 'A1  - '.trim($aut[0]).',';
		if ($aut[1])
			$auts[$cle] .= trim($aut[1]);
	}
	return implode("\n",$auts);
}

function cite_authors_bibtex ($auts) {
	$auts = explode(';',$auts);
	foreach ($auts as $cle => $aut) {
		$aut = explode(',',$aut,2);
		$auts[$cle] = trim($aut[0]).',';
		if ($aut[1])
			$auts[$cle] .= " ".trim($aut[1]);
	}
	return 'author = {'.implode(" and ",$auts).'}';
}

function cite_pages_ris ($pages) {
	$pages = explode('-',trim($pages));
	$ret = "SP  - ".$pages[0];
	if (isset($pages[1]))
		$ret .= "\nEP  - ".$pages[1];
	return $ret;
}

function cite_editors_ris ($auts) {
	$auts = explode(';',$auts);
	foreach ($auts as $cle => $aut) {
		$aut = explode(',',$aut,2);
		$auts[$cle] = 'ED  - '.trim($aut[0]).',';
		if ($aut[1])
			$auts[$cle] .= trim($aut[1]);
	}
	return implode("\n",$auts);
}

function cite_editors_bibtex ($auts) {
	$auts = explode(';',$auts);
	foreach ($auts as $cle => $aut) {
		$aut = explode(',',$aut,2);
		$auts[$cle] = trim($aut[0]).',';
		if ($aut[1])
			$auts[$cle] .= " ".trim($aut[1]);
	}
	return 'editor = {'.implode(" and ",$auts).'}';
}

function bibtex_encode($texte) {
	include_spip('inc/transtab_unicode_bibtex');
	foreach (transtab_unicode_bibtex() as $utf8 => $bibtex)
		$texte = str_replace($utf8,$bibtex,$texte);
	return $texte;
}

?>