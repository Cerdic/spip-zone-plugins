<?php

/*
 *  Plugin Bouquinerie pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_editer_livre_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	// si id_livre n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les données qu'il faut.
	if (!$id_livre = intval($arg)) {
		$id_livre = insert_livre();
	}

	$r = sql_fetsel('id_catalogue','spip_livres',"id_livre=$id_livre");
	$old_catalogue = $r['id_catalogue'];
	if ($old_catalogue != _request('id_catalogue')) { // si on change de catalogue
		sql_query('DELETE FROM spip_livres_catalogues WHERE id_livre='.$id_livre);
	}

	// Enregistre l'envoi dans la BD
	$err = livres_set($id_livre);

	$url = attribut_html(generer_url_ecrire('livres','id_livre='.$id_livre));
	$url = str_replace('#38;','',$url); // bidouille
	redirige_par_entete($url);
}

// Appelle toutes les fonctions de modification d'un livre
// $err est de la forme '&trad_err=1'
function livres_set($id_livre, $c=false) {
	$err = '';

	$c = array();
	foreach (array('id_catalogue', 'id_reference', 'titre', 'auteur', 'illustrateur', 'edition',
			 'prix_vente', 'isbn', 'statut', 'etat_livre', 'format', 'etat_jaquette', 'reliure',
			 'type_livre', 'lieu_edition', 'annee_edition', 'num_edition', 'inscription', 'remarque',
			 'commentaire','prix_achat', 'lieu', 'date_ajout','id_auteur','num_facture') as $champ)
	       $c[$champ] = _request($champ);
			
	revision_livre($id_livre, $c);

	return $err;
}

function insert_livre() {

	include_spip('base/abstract_sql');

	// ajout du nouveau livre dans la BD

	sql_insertq('spip_livres', array('titre' => ''));

	$ret = sql_fetsel(
		array('MAX(id_livre) as id_livre'),
		array('spip_livres')
	);

	$id_livre = $ret['id_livre'];

	return $id_livre;
}

function revision_livre($id_livre, $c=false) {
	include_spip('inc/autoriser');
	include_spip('inc/filtres');

	// Ces champs seront pris nom pour nom (_POST[x] => spip_articles.x)
	$champs_normaux = array('id_catalogue', 'id_reference', 'titre', 'auteur', 'illustrateur', 'edition',
			 'prix_vente', 'isbn', 'statut', 'etat_livre', 'format', 'etat_jaquette', 'reliure',
			 'type_livre', 'lieu_edition', 'annee_edition', 'num_edition', 'inscription', 'remarque',
			 'commentaire','prix_achat', 'lieu', 'date_ajout','id_auteur','num_facture');

	// ne pas accepter de titre vide
	if (_request('titre', $c) === '')
		$c = set_request('titre', _T('ecrire:info_sans_titre'), $c);

	$champs = array();
	foreach ($champs_normaux as $champ) {
		$val = _request($champ, $c);
		if ($val !== NULL)
			$champs[$champ] = corriger_caracteres($val);
	}

	$update = array();
	foreach ($champs as $champ => $val)
		$update[] = $champ . '=' . _q($val);

	if (!count($update)) return;

	spip_query("UPDATE spip_livres SET ".join(', ', $update)." WHERE id_livre=$id_livre");

}

?>
