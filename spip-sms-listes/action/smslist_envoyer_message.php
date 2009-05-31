<?php
/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function action_smslist_envoyer_message(){
	// creer un lien en attente vers un message
	$id_donnee = _request('id_donnee');
	$res = spip_query("SELECT f.type_form,f.id_form FROM spip_forms_donnees AS d JOIN spip_forms AS f ON f.id_form=d.id_form WHERE d.id_donnee="._q($id_donnee));
	if ($row = spip_fetch_array($res)
	  AND $row['type_form']=='smslist_message'){
		spip_query("INSERT INTO spip_forms_donnees_donnees (id_donnee,id_donnee_liee) VALUES (".(0-$GLOBALS['auteur_session']['id_auteur']).","._q($id_donnee).")");
		// et passer le message en prop, non modifiable
		spip_query("UPDATE spip_forms_donnees SET statut='prop' WHERE statut='prepa' AND id_donnee="._q($id_donnee));
	}
	$redirect = generer_url_ecrire('smslist_envoyer_message',"message=$id_donnee",true);
	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

?>