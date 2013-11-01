<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_importer_boussole_charger_dist(){

	return array(
		'boussole' => 'spip',
		'id_parent' => 0,
		'langue_site' => 1,
		'importer_statut_publie' => 0,
	);
}


function formulaires_importer_boussole_verifier_dist(){
	$erreurs = array();

	if (!_request('id_parent'))
		$erreurs['id_parent'] = _T('info_obligatoire');

	return $erreurs;
}


function formulaires_importer_boussole_traiter_dist(){
	$retour = array();

	$id_parent = intval(_request('id_parent'));
	$langue_site = _request('langue_site') ? true : false;
	$forcer_statut_publie = _request('importer_statut_publie') ? true : false;
	$boussole = _request('boussole');

	// Importer les sites de la boussole
	$nb_sites = importer_sites_boussole($boussole, $id_parent, $langue_site, $forcer_statut_publie);
	// Actualiser la boussole (en fait uniquement les id_syndic) maintenant que les sites référencés sont créés.
	// On utilise la fonction qui actualise toutes les boussoles même si ce n'est pas nécessaire pour les autres boussoles.
	include_spip('inc/client');
	boussole_actualiser_boussoles();

	if (!$nb_sites)
		$retour['message_erreur'] = _T('boussole:message_nok_0_site_importe', array('boussole' => boussole_traduire($boussole, 'nom_boussole')));
	else
		$retour['message_ok'] = singulier_ou_pluriel(
									$nb_sites,
									'boussole:message_ok_1_site_importe',
									'boussole:message_ok_n_sites_importes',
									'nb',
									array('boussole' => boussole_traduire($boussole, 'nom_boussole')));
	$retour['editable'] = true;

	return $retour;
}


function importer_sites_boussole($boussole, $id_parent, $langue_site=true, $forcer_statut_publie=false) {
	$nb_sites = 0;

	if ($id_parent) {
		$from = array('spip_boussoles as b', 'spip_boussoles_extras as x');
		$select = array('b.url_site', 'b.id_syndic', 'x.nom_objet', 'x.slogan_objet', 'x.descriptif_objet', 'x.logo_objet');
		$where = array(
					'b.aka_boussole=' . sql_quote($boussole),
					'b.aka_boussole=x.aka_boussole', 'b.aka_site=x.aka_objet',
					'x.type_objet=' . sql_quote('site'));
		$sites = sql_allfetsel($select, $from, $where);

		if ($sites) {
			include_spip('action/editer_site');
			include_spip('inc/filtres');
			foreach($sites as $_site) {
				// Nouveau site : il faut le créer préalablement dans la rubrique d'accueil
				$id_syndic = !$_site['id_syndic']  ? site_inserer($id_parent) : $_site['id_syndic'];

				if ($id_syndic) {
					// Mise à jour complète du site existant ou venant d'être créé
					$contenu = array(
								'url_site' => $_site['url_site'],
								'nom_site' => ($langue_site ? extraire_multi($_site['nom_objet']) : $_site['nom_objet']),
								'date' => date('Y-m-d H:i:s'),
								'descriptif' => ($langue_site ? extraire_multi($_site['descriptif_objet']) : $_site['descriptif_objet']));
					if (!$_site['id_syndic'])
						$contenu = array_merge($contenu, array('statut' => (($forcer_statut_publie AND autoriser('publierdans','rubrique',$id_parent)) ? 'publie' : 'prop')));
					$erreur = site_modifier($id_syndic, $contenu);

					if (!$erreur) {
						// Mise à jour de son logo normal ("on")
						$iconifier = charger_fonction('iconifier_site', 'inc');
						$iconifier($id_syndic, 'on', $_site['logo_objet']);

						$nb_sites ++;
					}
					else {
						// On traite l'erreur en supprimant le site si celui-ci vient d'être inséré
						if (!$_site['id_syndic'])
							sql_delete('spip_syndic', 'id_syndic=' . sql_quote($id_syndic));
					}
				}
			}
		}
	}

	return $nb_sites;
}