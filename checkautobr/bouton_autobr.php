<?php

function checkautobr_ajoute_bouton_autobr($x) {
	include_spip('inc/presentation');
	if ($x['args']['exec'] == 'articles'
	AND $id_article = intval($x['args']['id_article'])
	AND autoriser('article', 'modifier', $id_article)
	) {
		include_spip('prive/exec/checkautobr_fonctions');
		$t = sql_fetsel('chapo,texte,ps', 'spip_articles', "id_article=$id_article");

		$transform = false;
		foreach($t as $k=>$v) {
			$v = preg_replace("/\r?\n/", "\n", $v);
			if (substr($v, 0, strlen(_AUTOBR_IGNORER)) == _AUTOBR_IGNORER
			AND $w = substr($v, strlen(_AUTOBR_IGNORER))
			AND $c = autobr_correction($w)
			AND $c !== $v
			)
				$transform = true;
		}

		if ($transform) {
			$x['data'] .= "<br />\n"
			.debut_boite_info(true)
			."<p>"._L('Cet article comporte des sauts de ligne ignorés').'</p>'
			.icone_horizontale(
				'<span style="color:black;"><s>&para;</s></span> les supprimer',
				generer_url_action('autobr_supprimer', 'id_article='.$id_article),
				"",  # grml!!
				"rien.gif", false)

			.icone_horizontale(
				'<span style="color:orange;">&para;</span> les valider',
				generer_url_action('autobr_valider', 'id_article='.$id_article),
				'',  # grml!!
				"rien.gif", false)
			.fin_boite_info(true);
		}

		// transformer silencieusement des articles qui ne posent pas de probleme
#		autobr_transformer_silencieusement();

	}


	return $x;
}



