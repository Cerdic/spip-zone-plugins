<?php
// Rediriger automatiquement un contact défini comme auteur
// sur la page des auteurs.
if (defined('_CO_REDIRIGER_SUR_AUTEURS') and _CO_REDIRIGER_SUR_AUTEURS) {

	$id_contact = _request('id_contact');
	$id_auteur = sql_getfetsel('id_objet', 'spip_contacts_liens', 'id_contact=' . intval($id_contact) . ' AND objet=\'auteur\'');

	if ($id_auteur) {
		include_spip('inc/headers');
		$redirect = generer_url_ecrire('auteur_infos','id_auteur='.$id_auteur, true);
		redirige_par_entete($redirect);
	}

}

include_spip('inc/presentation');

?>
