<?php
$id_organisation = _request('id_organisation');
$id_auteur = sql_getfetsel('id_auteur', 'spip_organisations', 'id_organisation='. intval($id_organisation) );

if ($id_auteur) {
	include_spip('inc/headers');
	$redirect = generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur, true);
	redirige_par_entete($redirect);
}
else
	include_spip('inc/presentation');
?>
