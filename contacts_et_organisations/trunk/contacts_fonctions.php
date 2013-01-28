<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Licence GPL (c) 2009 - 2010- Ateliers CYM
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/***
 * Cette balise s'emploie dans une boucle (CONTACTS).
 * Elle retourne le champ #NOM du spip_auteur d'un contact.
 *
 */
function balise_NOM_AUTEUR_dist($p) {
	$connect = !$p->id_boucle ? '' : $p->boucles[$p->id_boucle]->sql_serveur;

	$p->code = "recuperer_fond('modeles/nom_auteur',
		array('id_contact' => ".champ_sql('id_contact', $p)
		."), array('trim'=>true), "
		. _q($connect)
		.")";
	$p->interdire_scripts = false; // securite apposee par recuperer_fond()
	return $p;
}


/***
 * Cette balise s'emploie dans une boucle (CONTACTS).
 * Elle retourne les champs #NOM des spip_organisations liés au contact.
 *
 */
function balise_ORGANISATIONS_dist($p) {

	$connect = !$p->id_boucle ? '' : $p->boucles[$p->id_boucle]->sql_serveur;

	$p->code = "recuperer_fond('modeles/lesorganisations',
		array('id_contact' => ".champ_sql('id_contact', $p)
		."), array('trim'=>true), "
		. _q($connect)
		.")";
	$p->interdire_scripts = false; // securite apposee par recuperer_fond()

	return $p;
}

/***
 * Ces balises permettent de retrouver le nom de famille (#NOM) et le prénom (#PRENOM)
 * d'un enregistrement de la table spip_contacts à partir d'un id_auteur
 * ou le nom (#NOM) d'un enregistrement de la table spip_auteurs à partir d'un id_auteur
 */
// Balise #PRENOM_AUTEUR
// Retrouve le prénom d'un contact à partir de l'id_auteur
function balise_PRENOM_AUTEUR($p) {
	$id_auteur = champ_sql('id_auteur', $p);
	$p->code = "trouve_prenom(".$id_auteur.")";
	$p->statut = 'php';
	return $p;
}
// Balise #PRENOM_CONTACT
// Retrouve le prénom d'un contact à partir de l'id_auteur
function balise_PRENOM_CONTACT($p) {
	$id_auteur = champ_sql('id_auteur', $p);
	$p->code = "trouve_prenom(".$id_auteur.")";
	$p->statut = 'php';
	return $p;
}
// Balise #NOM_CONTACT
// Retrouve le nom de famille d'un contact à partir de l'id_auteur
function balise_NOM_CONTACT($p) {
	$id_auteur = champ_sql('id_auteur', $p);
	$p->code = "trouve_nom(".$id_auteur.")";
	$p->statut = 'php';
	return $p;
}
// a modifier pour les appeler "dist"
function trouve_prenom($id_auteur) {

    // $prenom = sql_getfetsel("prenom","spip_contacts LEFT JOIN spip_contacts_liens ON (spip_contacts.id_contact=spip_contacts_liens.id_contact AND objet='auteur')", "id_objet=" . intval($id_auteur));
    $prenom = sql_getfetsel("prenom","spip_contacts", "id_auteur=" . intval($id_auteur));

    if (!empty($prenom))
        return $prenom;

    return '';
}

function trouve_nom($id_auteur) {
    $nom = sql_getfetsel("nom","spip_contacts", "id_auteur=" . intval($id_auteur));
    if (!empty($nom))
        return $nom;
    return '';
}


// Balise #CIVILITE_AUTEUR
// a modifier pour les appeler "dist"
function trouve_civilite($id_auteur) {

	// $civilite = sql_getfetsel("civilite","spip_contacts LEFT JOIN spip_contacts_liens ON (spip_contacts.id_contact=spip_contacts_liens.id_contact AND objet='auteur')", "id_objet=" . intval($id_auteur));
	$civilite = sql_getfetsel("civilite","spip_contacts", "id_auteur=" . intval($id_auteur));

	if (!empty($civilite))
		return $civilite;

	return '';
}
function balise_CIVILITE_AUTEUR($p) {
	$id_auteur = champ_sql('id_auteur', $p);
	$p->code = "trouve_civilite(".$id_auteur.")";
	$p->statut = 'php';
	return $p;
}



// --------------

/**
 *
 * Cette balise retourne un tableau listant toutes les id_rubrique d'une branche.
 * L'identifiant de la branche (id_rubrique) est pris dans la boucle
 * la plus proche sinon dans l'environnement.
 *
 * On ne peut pas l'utiliser dans un {critere IN #IDS_BRANCHE} en 1.8.3 :(
 *
 */
function balise_IDS_ORGANISATION_BRANCHE_dist($p) {

	// parcours de tous les identifiants recus en parametre
	$n = 0;
	$ids = array();
	while ($id_org = interprete_argument_balise(++$n,$p)) {
		if ($id_org = trim(trim($id_org), "'")) { // vire les guillements pour accepter soit un terme soit un nombre
			$ids = array_merge($ids, array($id_org)); // ... les merge avec id
		}
	}

	// pas d'identifiant, on prend la boucle la plus proche
	if (!$ids) {
		$ids = champ_sql('id_organisation', $p);
		$p->code = "explode(',', calcul_organisation_branche_in($ids))"; // 200
	} else {
		$p->code = "explode(',', calcul_organisation_branche_in(" . var_export($ids, true) . "))"; // 200
	}

	return $p;
}



/**
 * Calcul d'une branche
 * (liste des id_organisation contenues dans une organisation donnee)
 *
 * @param string|int|array $id
 * @return string
 */
function calcul_organisation_branche_in($id) {
	static $b = array();

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = join(',', array_map('intval', $id));
	if (isset($b[$id]))
		return $b[$id];

	// Notre branche commence par l'organisation de depart
	$branche = $r = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement
	while ($filles = sql_allfetsel(
					'id_organisation',
					'spip_organisations',
					sql_in('id_parent', $r)." AND ". sql_in('id_organisation', $r, 'NOT')
					)) {
		$r = join(',', array_map('array_shift', $filles));
		$branche .= ',' . $r;
	}

	# securite pour ne pas plomber la conso memoire sur les sites prolifiques
	if (strlen($branche)<10000)
		$b[$id] = $branche;
	return $branche;
}



?>
