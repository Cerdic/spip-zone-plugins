<?php

// Gerer les articles virtuels rediriges
// on ne traite que les #URL_ARTICLE, pas les #URL_ARTICLE{13}
// http://doc.spip.org/@balise_URL_ARTICLE_dist
function balise_URL_ARTICLE($p) {
	include_spip('balise/url_');
	balise_URL_ARTICLE_dist($p); // traitement de base de SPIP

	if ($p->type_requete == 'articles' AND !interprete_argument_balise(1,$p)) {
		include_spip('inc/lien');
		if (function_exists('chapo_redirigetil')){
			$_chapo = champ_sql('chapo', $p);
			$p->code = "(chapo_redirigetil(\$c=$_chapo)?chapo_redirige(substr(\$c,1),true):".$p->code.')';
		}
		else {
			$_virtuel = champ_sql('virtuel', $p);
			$_redirige = (function_exists('virtuel_redirige')?"virtuel_redirige($_virtuel,true)":"($_virtuel)");
			$p->code = "(($_virtuel)?$_redirige:".$p->code.')';
		}
	}
	return $p;
}