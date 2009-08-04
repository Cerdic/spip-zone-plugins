<?php 

function balise_URL_RUBRIQUE_ABSOLU_dist($p) {
	
	include_spip('balise/url_');
	
	$id_rubrique = interprete_argument_balise(1,$p);
	if (strlen(trim($id_rubrique))==0)
	{
		$id_rubrique = calculer_balise('id_rubrique', $p)->code;
	}
	$p->code = "calculer_URL_SECTEUR(sinon(sql_getfetsel('id_secteur','spip_rubriques','id_rubrique = \"'.".$id_rubrique.".'\"',null,null,1),0)).".generer_generer_url('rubrique', $p);

	
	$p->interdire_scripts = false;
	return $p;
}

?>