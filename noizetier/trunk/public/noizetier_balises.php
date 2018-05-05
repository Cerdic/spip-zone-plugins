<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_OBJET_INFOS_dist($p) {
	$objet = interprete_argument_balise(1, $p);
	if (isset($objet)) {
		$objet = str_replace('\'', '"', $objet);
		$id_objet = interprete_argument_balise(2, $p);
		$id_objet = isset($id_objet) ? $id_objet : '0';
		$information = interprete_argument_balise(3, $p);
		$information = isset($information) ? str_replace('\'', '"', $information) : '""';
		$p->code = "noizetier_objet_lire($objet, $id_objet, $information)";
	} else {
		$p->code = "noizetier_objet_repertorier()";
	}

	return $p;
}


// Cette balise renvoie le tableau de la liste des blocs
function balise_NOIZETIER_BLOC_INFOS_dist($p) {
	$bloc = interprete_argument_balise(1, $p);
	if (isset($bloc)) {
		$bloc = str_replace('\'', '"', $bloc);
		$information = interprete_argument_balise(2, $p);
		$information = isset($information) ? str_replace('\'', '"', $information) : '""';
		$p->code = "calculer_infos_bloc($bloc, $information)";
	} else {
		$p->code = "calculer_infos_bloc()";
	}

	return $p;
}

function calculer_infos_bloc($bloc = '', $information = '') {

	include_spip('inc/noizetier_bloc');
	return noizetier_bloc_informer($bloc, $information);
}


function balise_NOIZETIER_NOISETTE_PREVIEW_dist($p) {
	$id_noisette = champ_sql('id_noisette', $p);
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);

	$inclusion = "recuperer_fond(
		'noisette_preview',
		array_merge(unserialize($parametres), array('type_noisette' => $type_noisette))
	)";

	$p->code = "$inclusion";
	$p->interdire_scripts = false;

	return $p;
}
