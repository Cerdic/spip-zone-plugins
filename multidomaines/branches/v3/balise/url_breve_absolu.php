<?php 

function balise_URL_DOCUMENT_ABSOLU_dist($p) {
	include_spip('balise/url_');
	$id_breve = interprete_argument_balise(1,$p);
	if (strlen(trim($id_breve))==0)
	{
		$id_breve = calculer_balise('id_breve', $p)->code;
	}
	$p->code = "calculer_URL_SECTEUR(sinon(sql_getfetsel('id_rubrique','spip_breves','id_breve' . intval($id_breve)),0)).".generer_generer_url('breve', $p);
	$p->interdire_scripts = false;
	return $p;
}
?>
