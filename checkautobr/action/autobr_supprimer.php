<?php


function action_autobr_supprimer_dist() {
	if ($id_article = intval(_request('id_article'))
	AND autoriser('modifier', 'article', $id_article)) {

		include_spip('prive/exec/checkautobr_fonctions');

		$t = sql_fetsel('chapo,texte,ps', 'spip_articles',
		"id_article=".$id_article);

		foreach($t as $k => $v) {
			$v = preg_replace("/\r?\n/", "\n", $v);
			if (substr($v, 0, strlen(_AUTOBR_IGNORER)) == _AUTOBR_IGNORER) {
				$w = substr($v, strlen(_AUTOBR_IGNORER));
				if ($c = autobr_correction($w)
				AND $c !== $v
				) {
					sql_updateq('spip_articles', array($k => $c), 'id_article='.$id_article);
#					echo "<li>je nettoie $k(".$id_article.")</li>";
				}
			}
		}

		autobr_transformer_silencieusement();
	}

	include_spip('inc/headers');
	redirige_par_entete(generer_url_ecrire('checkautobr'));
}