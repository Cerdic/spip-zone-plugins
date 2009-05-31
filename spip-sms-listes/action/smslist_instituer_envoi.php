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

function action_smslist_instituer_envoi(){
	$id_donnee = _request('id_donnee');
	$nouveau_statut = _request('statut');
	if (in_array($nouveau_statut,array('prepa','prop','refuse','poubelle'))){
		$res = spip_query("SELECT f.type_form,d.statut FROM spip_forms_donnees AS d JOIN spip_forms AS f ON f.id_form=d.id_form WHERE d.id_donnee="._q($id_donnee));
		if(
		  $row = spip_fetch_array($res) 
		  AND $row['type_form']=='smslist_boiteenvoi'
		  AND $row['statut']!='publie')
			spip_query("UPDATE spip_forms_donnees SET statut='$nouveau_statut' WHERE id_donnee="._q($id_donnee));
	}
	$redirect = generer_url_ecrire('spip_sms_listes');
	include_spip('inc/headers');
	redirige_par_entete($redirect);
}

?>