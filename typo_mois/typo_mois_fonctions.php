<?php

function typo_mois_mois($texte){
	static $typo;
	if(!$typo) {
		$typo = array();
		for ($m=1; $m<=12; $m++)
			$typo[] = _T('date_mois_'.$m);
		$pre1 = _T('date_jnum1');
		$pre2 = _T('date_jnum2');
		$pre3 = _T('date_jnum3');
		$typo = ",([1-3]?[0-9]|$pre1|$pre2|$pre3) (".join('|', $typo).')\b,UimsS';
		include_spip('inc/charsets');
		$typo = unicode2charset(html2unicode($typo));
	}

	return preg_replace($typo, '\1&nbsp;\2', $texte);
}

?>