<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_mise_a_jour() {
	global $connect_id_auteur;
	$f = charger_fonction('auth', 'inc');
	$auth = $f();

	include_spip('inc/mise_a_jour');
	$spip_loader_liste = spip_loader_liste(_SPIP_MAJ_FILE);
	$redirect = urldecode(_request('redirect'));
	$paquet = _request('paquet');
	$elements_fichier = $spip_loader_liste[$paquet];

	$elements_paquet = array_merge( 
		array_map('_q', $elements_fichier),
		array(_q($paquet),$connect_id_auteur)
	);

	$query = "SELECT id_paquet FROM spip_paquets WHERE titre="._q($paquet);
	$result = spip_query($query);
	$row = spip_fetch_array($result);
	if($row) {
		//update de la date de maj du paquet
		$id_paquet = $row['id_paquet']; 
		spip_query("UPDATE spip_paquets SET date=NOW(), id_auteur=".$connect_id_auteur." WHERE id_paquet=".$id_paquet);
	}
	else {
		include_spip('base/abstract_sql');
		$id_paquet = spip_abstract_insert("spip_paquets",
			"(source, destination, revision, user, methode, titre, id_auteur, date)",
			"(".join(',', $elements_paquet).", NOW())");
	}
	
	if($redirect)
		redirige_par_entete($redirect);
	exit;
}

?>
