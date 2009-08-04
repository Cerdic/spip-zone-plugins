<?php 

function balise_URL_ARTICLE_ABSOLU_dist($p) {
	
	include_spip('balise/url_');
	
	// Cas particulier des boucles (SYNDIC_ARTICLES)
	if ($p->type_requete == 'syndic_articles') {
		$p->code = "vider_url(".champ_sql('url', $p).")";
	} else
	{
		$id_article = interprete_argument_balise(1,$p);
		if (strlen(trim($id_article))==0)
		{
			$id_article = calculer_balise('id_article', $p)->code;
		}

		$p->code = "calculer_URL_SECTEUR(sinon(sql_getfetsel('id_secteur','spip_articles','id_article = \"'.".$id_article.".'\"',null,null,1),0)).".generer_generer_url('article', $p);
	}
	
	$p->interdire_scripts = false;
	return $p;
	
}


?>