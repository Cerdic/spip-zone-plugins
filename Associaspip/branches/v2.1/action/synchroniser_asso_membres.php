<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_synchroniser_asso_membres() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$tous = _request('tous');
	if ($tous) {
		$where = "statut <> '5poubelle'";
	} else {
		$liste_statuts = array();
		if (_request('visiteurs')) $liste_statuts[] = '6forum';
		if (_request('redacteurs')) $liste_statuts[] = '1comite'; 
		if (_request('administrateurs')) $liste_statuts[] = '0minirezo';
		$where = sql_in("statut", $liste_statuts)." OR (statut='nouveau' AND ".sql_in("bio", $liste_statuts).")"; /* cas des redacteurs jamais connectes, leur statut est dans le champ bio */
	}

	if (!_request('forcer')) {
		/* on recupere les id de tous les membres deja presents pour ne pas les traiter */
		$id_membres = sql_select('id_auteur', 'spip_asso_membres');
		if ($id_membres) {
			$liste_membres = array();
			while ($id_membre = sql_fetch($id_membres)) {$liste_membres[]=$id_membre['id_auteur'];}
			$where = '('.$where.') AND '.sql_in("id_auteur", $liste_membres, "NOT");
		}
	}

	$auteurs = sql_select('id_auteur', 'spip_auteurs', $where);
	$nb_modifs = sql_count($auteurs);

	if ($auteurs) {
		include_spip('inc/post_edition');
		while ($auteur = sql_fetch($auteurs)) {
			update_spip_asso_membre($auteur['id_auteur']);
		}
	}

	return $nb_modifs; /* on retourne le nombre de membres inseres dans la table */
}
?>
