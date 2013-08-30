<?php 

function balise_URL_DOCUMENT_ABSOLU_dist($p) {
	include_spip('balise/url_');
	$id_breve = interprete_argument_balise(1,$p);
	if (strlen(trim($id_breve))==0)
	{
		$id_breve = calculer_balise('id_document', $p)->code;
	}
	$p->code = "calculer_URL_SECTEUR(sinon(sql_getfetsel('id_article','spip_documents','id_document' . intval($id_document)),0)).".generer_generer_url('document', $p);
	$p->interdire_scripts = false;
	return $p;
}
?>
