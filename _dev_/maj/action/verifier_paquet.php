<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
//include_spip('inc/mise_a_jour');
include_spip('inc/distant');	//recuperer_page()
include_spip('inc/filtres');	//normaliser_date()

function action_verifier_paquet() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_auteur = $securiser_action();
	$redirect = urldecode(_request('redirect'));
	$id_paquet = intval(_request('id_paquet'));

	//verifier
	$query = "SELECT date, source, methode FROM spip_paquets WHERE id_paquet=".$id_paquet;
	$resultat = spip_query($query);
	if($row = spip_fetch_array($resultat)){
		$date = $row['date'];
		if($resultat = recuperer_page(
			$row['source'],
			false, true, 1048576, '', '', false,
			$date
		)){
			$spip_loader['date_verif'] = date('Y-m-d H:i:s', time());
			if($resultat != '200' AND preg_match(',Last-Modified: (.*),', $resultat, $r)){
				$date_reference = normaliser_date($r[1]);				
			}
		}
	}

	//stocker

	//renvoyer
	if($redirect)
		redirige_par_entete($redirect);
	exit;
}

?>