<?php

// Gerer les articles virtuels rediriges
// on ne traite que les #URL_ARTICLE, pas les #URL_ARTICLE{13}
// http://doc.spip.org/@balise_URL_ARTICLE_dist
function balise_URL_ARTICLE($p) {
	include_spip('balise/url_');
	balise_URL_ARTICLE_dist($p); // traitement de base de SPIP

	if ($p->type_requete == 'articles' AND !interprete_argument_balise(1,$p)) {
		$_chapo = champ_sql('chapo', $p);
		$p->code = "(chapo_redirigetil(\$c=$_chapo)?chapo_redirige(substr(\$c,1),true):".$p->code.')';
	}
	return $p;
}