<?php


include_spip('inc/securiser_action');
include_spip('inc/texte');

if (_request('ignorer_autobr') == 'oui'
AND verifier_cle_action('autobr', _request('securite'))
) {
	ignorer_autobr();
}

autobr_transformer_silencieusement();

function autobr_correction($t) {
	static $nl = null;
	if(!$nl)
		$nl = preg_quote(trim(str_replace('A', '', PtoBR(propre("A\nA")))),',');


	$tm = autobr_marquer($t);

	$p = propre($tm);

	if (preg_match_all(", CHECKAUTOBR_(\d+)_$nl,", $p, $r, PREG_PATTERN_ORDER)) {
		$l = $r[1];
	} else {
		$l = array();
	}

	if ($l) {
		foreach($l as $n) {
			$tm = str_replace(' CHECKAUTOBR_'.$n.'_'."\n", ' ', $tm);
		}
	}
	$tm = preg_replace('/ CHECKAUTOBR_\d+_/', '', $tm);
	$tm = echappe_retour($tm, 'CHECK');

	#echo nl2br(htmlspecialchars($tm));

	return $tm;
}


function autobr_marquer($t) {
	$t = echappe_html($t, 'CHECK', true /*notransform*/);

	while ($t !== ($t1 = preg_replace("/(<[^>\r\n]*)\r?\n/S", '\1 CHECKNEWLINEINTAG ', $t)))
		$t = $t1;

	$t = preg_split("/\r?\n/", $t);

	foreach($t as $k=>$l) {
		if (strlen($t[$k] AND strlen($t[$k+1])))
		{
			$t[$k] = $t[$k].' CHECKAUTOBR_'.$k.'_';
		}
	}

	$t = join("\n", $t);

#echo "<pre>".(htmlspecialchars($t))."</pre>";
	$t = str_replace(' CHECKNEWLINEINTAG ', "\n", $t);

	return $t;
}


function autobr_simplifier($t) {
	$t = preg_replace("/ CHECKAUTOBR_\d+_\n(\n|-|_ |[]]|\|)/S", '', $t);

	return $t;
}

function ignorer_autobr() {
	spip_query('UPDATE spip_articles SET texte=CONCAT('._q(_AUTOBR_IGNORER).', texte) WHERE texte LIKE "%\n%" AND NOT (texte LIKE '._q(_AUTOBR_IGNORER.'%').')');
	spip_query('UPDATE spip_articles SET chapo=CONCAT('._q(_AUTOBR_IGNORER).', chapo) WHERE chapo LIKE "%\n%" AND NOT (chapo LIKE '._q(_AUTOBR_IGNORER.'%').')  AND NOT (chapo LIKE "=%")');
	spip_query('UPDATE spip_articles SET ps=CONCAT('._q(_AUTOBR_IGNORER).', ps) WHERE ps LIKE "%\n%" AND NOT (ps LIKE '._q(_AUTOBR_IGNORER.'%').')');
}


// transformer silencieusement les articles marques
function autobr_transformer_silencieusement() {
	$tt = sql_allfetsel('id_article,chapo,texte,ps,RAND() as alea', 'spip_articles',
	"id_article=32 AND (texte LIKE "._q(_AUTOBR_IGNORER."%")
	." OR chapo LIKE "._q(_AUTOBR_IGNORER."%")
	." OR ps LIKE "._q(_AUTOBR_IGNORER."%").')',
	'' /* group by */,
	'alea' /* order by */,
	100 /* limit */
	);

	foreach($tt as $t) {
		$transform = false;
		foreach(array('chapo', 'texte', 'ps') as $k) {
			$v = preg_replace("/\r?\n/", "\n", $t[$k]);
			if (substr($v, 0, strlen(_AUTOBR_IGNORER)) == _AUTOBR_IGNORER) {
				if ($c = autobr_correction($v)
				AND $c !== $v
				) {
					$transform = true;
				}
				# l'ignore ne changeant rien, on va le faire silencieusement
				else {
					$w = substr($v, strlen(_AUTOBR_IGNORER));
					sql_updateq('spip_articles', array($k => $w), 'id_article='.$t['id_article']);
					#echo "<li>je retablis silencieusement $k(".$t['id_article'].")</li>";
				}
			}
		}
	}

}

