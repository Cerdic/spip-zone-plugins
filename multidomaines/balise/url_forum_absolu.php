<?php 

function balise_URL_FORUM_ABSOLU_dist($p) {
	
	include_spip('balise/url_');
	
	$id_forum = interprete_argument_balise(1,$p);
	if (strlen(trim($id_forum))==0)
	{
		$id_forum = calculer_balise('id_forum', $p)->code;
	}
	$p->code = "calculer_URL_SECTEUR(sinon(sql_getfetsel('id_rubrique','spip_forums','id_forum = \"'.".$id_forum.".'\"',null,null,1),0)).".generer_generer_url('forum', $p);

	
	$p->interdire_scripts = false;
	return $p;
}

?>