<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction qui n'a rine à faire mais qui est appelée par le pipeline autoriser
function marquepages_autoriser(){}

// On a le droit d'ajouter un marque-page si on peut voir la rubrique voulue
// et suivant la configuration du plugin
function autoriser_rubrique_creermarquepagedans_dist($faire, $type, $id, $qui, $opt){
	
	return
		$id
		and autoriser('voir', 'rubrique', $id, $qui, $opt)
		and (
			$qui['statut']=='0minirezo'
			or (intval(lire_config('marquepages/qui')) >= ($qui['statut']=='1comite' ? 1 : 2))
		);
	
}

// Pour voir un marque-page il faut :
// - voir la rubrique où il est placé
// - vouloir voir un marque-page public
// - ou vouloir voir un marque-page privé et en être l'auteur ou être admin
function autoriser_marquepage_voir_dist($faire, $type, $id, $qui, $opt){
	if (!$id)
		return false;
	
	$requete = sql_fetsel(
		'marquepage.statut, marquepage.id_auteur, site.id_rubrique',
		array(
			'marquepage' => 'spip_forum',
			'site' => 'spip_syndic'
		),
		array(
			array('=', 'marquepage.id_forum', intval($id)),
			array('=', 'site.id_syndic', 'marquepage.id_syndic')
		)
	);
	
	return
		$requete
		and autoriser('voir', 'rubrique', intval($requete['id_rubrique']), $qui, $opt)
		and(
			$requete['statut'] == 'mppublic'
			or(
				$requete['statut'] == 'mpprive'
				and(
					intval($qui['id_auteur']) == intval($requete['id_auteur'])
					or autoriser('publierdans', 'rubrique', intval($requete['id_rubrique']), $qui, $opt)
				)
			)
		);
}

// Teste si on peut modifier un marque-page
// Pour avoir le droit il faut :
// - pouvoir voir la rubrique où il est
// - en être l'auteur ou être admin de la rubrique
function autoriser_marquepage_modifier_dist($faire, $type, $id, $qui, $opt){
	
	if (!$id)
		return false;
	
	$requete = sql_fetsel(
		'marquepage.id_auteur, site.id_rubrique',
		array(
			'marquepage' => 'spip_forum',
			'site' => 'spip_syndic'
		),
		array(
			array('=', 'marquepage.id_forum', intval($id)),
			array('=', 'site.id_syndic', 'marquepage.id_syndic')
		)
	);
	
	return
		$requete
		and autoriser('voir', 'rubrique', intval($requete['id_rubrique']), $qui, $opt)
		and(
			intval($qui['id_auteur']) == intval($requete['id_auteur'])
			or autoriser('publierdans', 'rubrique', intval($requete['id_rubrique']), $qui, $opt)
		);
	
}

// Teste si on peut supprimer un marque-page
function autoriser_marquepage_supprimer_dist($faire, $type, $id, $qui, $opt){
	
	// On dit que si on a le droit de modifier un MP, alors on a le droit de le supprimer
	return autoriser('modifier', 'marquepage', $id, $qui, $opt);
	
}

?>
