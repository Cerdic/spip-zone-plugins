<?php
/*
 * Glossaire
 * Gestion des listes de definitions techniques
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function exec_glossaire(){
	include_spip('base/forms_base_api');
	$liste = Forms_liste_tables('glossaire');
	$id_form = reset($liste);
	include_spip('inc/headers');
	$url = generer_url_ecrire('donnees_tous',"id_form=$id_form",true);
	redirige_par_entete($url);
}

?>