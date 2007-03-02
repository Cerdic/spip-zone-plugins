<?php

function exec_sigles_tous(){
	include_spip('base/forms_base_api');
	$liste = Forms_liste_tables('acronymes_sigles');
	$id_form = reset($liste);
	include_spip('inc/headers');
	$url = generer_url_ecrire('donnees_tous',"id_form=$id_form",true);
	redirige_par_entete($url);
}

?>