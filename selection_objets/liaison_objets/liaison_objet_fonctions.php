<?php

if (!defined('_ECRIRE_INC_VERSION'))
	return;

//Applique des filtres sur des champs spéciciques
function filtrer_champ($data) {
	include_spip('inc/texte');
	$exceptions = charger_fonction('exceptions', 'inc');
	$titres = $exceptions('titre');
	$titres = array_merge(array(0 => 'titre'), $titres);
	$propres = array(
		'descriptif',
		'texte'
	);
	$extraire_multi = array_merge($titres, array(
		'descriptif',
		'texte'
	));
	$filtres = array(
		'extraire_multi' => $extraire_multi,
		'supprimer_numero' => $titres,
		'propre' => $propres,
	);

	foreach ($filtres as $filtre => $champ) {
		if (is_array($data)) {
			if (is_array($champ)) {
				foreach ($champ as $c) {
					if ($data[$c])
						$data[$c] = $filtre($data[$c]);
				}
			}
		}
		else
			$data = $filtre($data);
	}
	return $data;

}

/*Etablit le titre de l'objet*/
function titre_objet_sel($objet, $contexte) {

	$exceptions = charger_fonction('exceptions', 'inc');
	$exception_titre = $exceptions('titre');
	//Les exceptions du titre
	if (!$titre = $contexte[$exception_titre[$objet]] and isset($contexte['titre']))
		$titre = $contexte['titre'];
	if (!$titre) {
		if ($objet == 'document') {
			$f = explode('/', $contexte['fichier']);
			$titre = $f[1];
		}
		elseif ($objet) {
			$table_sql = table_objet_sql($objet);
			$tables = lister_tables_objets_sql();
			$titre_objet = _T($tables[$table_sql]['texte_objet']);
			if (isset($contexte['id_objet']))
				$id = $contexte['id_objet'];
			if ($objet = 'selection_objet' AND isset($contexte['id_selection_objet']))
				$id = $contexte['id_selection_objet'];
			$titre = $titre_objet . ' ' . $id;
		}

	}
	return $titre;
}

/* Fournit les champs désirés d'un objet donné */
function info_objet($objet, $id_objet = '', $champ = '*', $where = array()) {
	include_spip('inc/filtres');
	$exceptions = charger_fonction('exceptions', 'inc');
	$exception_objet = $exceptions();
	$exception_titre = $exceptions('titre');

	//Les tables non conforme
	if ($objet) {
		include_spip('inc/pipelines_ecrire');
		$ancien_objet = $objet;
		$e = trouver_objet_exec($objet);

		$objet = $e['type'];
		$id_table_objet = $e['id_table_objet'];
		// Pour les récalcitrants
		if (!$objet) {
			$objet = $ancien_objet;
			$id_table_objet = 'id_' . $objet;
		}
		$table = table_objet_sql($objet);

		if ($id_objet) {
			if (!$where)
				$where = array($id_table_objet . '=' . $id_objet);
			if ($champ == '*') {
				$data = sql_fetsel($champ, $table, $where);
			}
			else {
				if (isset($exception_titre[$objet])) {
					$champ = $exception_titre[$objet];
				}
				$data = sql_getfetsel($champ, $table, $where);
			}

			$data = filtrer_champ($data);
		}
		else {
			$data = array();
			$sql = sql_select($champ, $table, $where);
			while ($d = sql_fetch($sql)) {

				if ($d)
					$data[$d[$id_table_objet]] = filtrer_champ($d);
			}
		}
	}
	else
		$data = array();
	return $data;

}

/* Fonction qui fournit le lien d'un objet*/
function url_objet($id_objet, $objet, $titre = '', $url = '') {

	if (!$titre AND !$url) {
		$objet_sel = sql_fetsel('titre,url', 'spip_selection_objets', 'id_objet=' . $id_objet . ' AND objet=' . sql_quote($objet));
		$url = $objet_sel['url'];
		$titre = $objet_sel['titre'];
	}

	if (!$titre)
		$titre = info_objet($objet, $id_objet, 'titre');
	if (!$url)
		$url = generer_url_entite($id_objet, $objet);

	$lien = '<a href="' . $url . '" title="' . $titre . '">' . $titre . '</a>';
	return $lien;
}

/*Fournit un tableau avec id_objet=>donnees_objet*/
function tableau_objet($objet, $id_objet = '', $champs = '*', $where = array(), $filtrer = array(), $array_donnes = true) {

	$d = info_objet($objet, $id_objet, $champs, $where);

	//Les tables non conforme, faudrait inclure une pipeline
	$exceptions = charger_fonction('exceptions', 'inc');
	$exception_objet = $exceptions('objet');
	if ($exception_objet[$objet]) {
		$objet = $exception_objet[$objet];
	}
	$data = array();
	if (is_array($d)) {
		foreach ($d as $r) {
			//déterminer le titre
			if (!$r['titre'])
				$r['titre'] = titre_objet_sel($objet, $r);
			if (!$filtrer)
				$data[$r['id_' . $objet]] = $r;
			elseif (is_array($filtrer)) {
				$donnees = array();
				foreach ($filtrer as $c) {
					if ($r[$c])
						$donnees[$c] = $r[$c];
				}
				if ($array_donnes)
					$data[$r['id_' . $objet]] = $donnees;
				else
					$data[$r['id_' . $objet]] = implode(',', $donnees);
			}
		}
	}
	return $data;
}

/* Assemble les données entre un objet sélectioné et son objet d'origine pour injection dans un modele choisit*/
function generer_modele($id_objet, $objet = 'article', $fichier = 'modeles_selection_objet/defaut', $env = array(), $where = '') {
	include_spip('inc/pipelines_ecrire');
	include_spip('inc/utils');

	//Quelques objets ne sont pas conforme, on adapte
	$exceptions = charger_fonction('exceptions', 'inc');
	$exception_objet = $exceptions();

	if ($objet) {
		$ancien_objet = $objet;
		$e = trouver_objet_exec($objet);
		$objet = $e['type'];
		$id_table_objet = $e['id_table_objet'];
		// Pour les récalcitrants
		if (!$objet) {
			$objet = $ancien_objet;
			$id_table_objet = 'id_' . $objet;
		}
		$table = table_objet_sql($objet);

		if (!$where)
			$where = $id_table_objet . '=' . $id_objet;
		if (!$contexte = sql_fetsel('*', $table, $where))
			$contexte = array();

	}
	else
		$contexte = array();

	//Filtrer les champs vides
	foreach ($env as $k => $v) {
		if (!$v)
			unset($env[$k]);
	}

	if (!$cont = calculer_contexte())
		$cont = array();
	if (is_array($env))
		$contexte = array_merge($cont, $contexte, $env);

	$contexte['objet'] = $objet;
	$contexte['id_objet'] = $id_objet;

	//déterminer le titre
	if (!$contexte['titre'])
		$contexte['titre'] = titre_objet_sel($objet, $contexte);

	//Les exceptions du titre
	if (!$exception_titre[$objet]) {
		$contexte['champ_titre'] = 'titre';
	}
	else {
		$contexte['champ_titre'] = $exception_objet['titre'][$objet];
	}

	//Chercher le logo correpsondant
	//Si il y a un logo Selection Objet
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$logo = $chercher_logo($contexte['id_selection_objet'], 'id_selection_objet', 'on');
	$contexte['logo_objet'] = $logo[0];
	//sinon le logo de l'objet sélectionné
	if (!$contexte['logo_objet']) {
		$_id_objet = id_table_objet($objet);
		$logo = $chercher_logo($id_objet, $_id_objet, 'on');
		$contexte['logo_objet'] = $logo[0];
	}
	$fond = recuperer_fond($fichier, $contexte);

	return $fond;
}

//donnele nom du type de lien
function nom_type($type, $objet) {
	include_spip('inc/config');
	if (!$types = lire_config('selection_objet/type_liens_' . $objet, array()))
		$types = lire_config('selection_objet/type_liens', array());

	if (!$nom = _T($types[$type]))
		$nom = $type;

	return $nom;
}
