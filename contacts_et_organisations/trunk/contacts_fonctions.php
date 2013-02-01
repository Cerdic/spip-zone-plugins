<?php
/***
 * Plugin Contacts & Organisations pour Spip 3.0
 * Licence GPL (c) 2009 - 2013 - Ateliers CYM
 */

/**
 * Définition des critères, balises, filtres et fonctions
 *
 * @package SPIP\Plugins\Contacts\Fonctions
**/
if (!defined("_ECRIRE_INC_VERSION")) return;




/**
 * Calcul de la balise #LESORGANISATIONS
 *
 * Affiche la liste des organisations d'un contact.
 * - Soit le champs lesauteurs existe dans la table et à ce moment là,
 *   on retourne son contenu
 * - Soit la balise appelle le modele lesorganisations.html en lui passant
 *   le id_contact dans son environnement
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_LESORGANISATIONS_dist ($p) {
	// Cherche le champ 'lesorganisations' dans la pile
	$_lesorganisations = champ_sql('lesorganisations', $p, false);

	// Si le champ n'existe pas (cas de spip_contacts), on applique
	// le modele lesorganisations.html en passant id_contact dans le contexte;
	// dans le cas contraire on prend le champ 'lesorganisations'
	if ($_lesorganisations
	AND $_lesorganisations != '@$Pile[0][\'lesorganisations\']') {
		$p->code = "safehtml($_lesorganisations)";
		// $p->interdire_scripts = true;
	} else {
		$connect = !$p->id_boucle ? ''
		  : $p->boucles[$p->id_boucle]->sql_serveur;

		$c = memoriser_contexte_compil($p);

		$p->code = sprintf(CODE_RECUPERER_FOND, "'modeles/lesorganisations'",
				   "array('id_contact' => ".champ_sql('id_contact', $p) .")",
				   "'trim'=>true, 'compil'=>array($c)",
				   _q($connect));
		$p->interdire_scripts = false; // securite apposee par recuperer_fond()
	}

	return $p;
}


/**
 * Calcul du critère compteur_contacts
 * 
 * Compter les contacts liés à une organisation, dans une boucle organisations
 * pour la vue prive/liste/organisations.html
 *
 * @example
 *   ```
 *   <BOUCLE_o(ORGANISATIONS){compteur_contacts}>
 *     [(#COMPTEUR_CONTACTS|singulier_ou_pluriel{contacts:nb_contact,contacts:nb_contacts})]
 *   ```
 *
 * @note
 *   Fonctionnement inspiré du critère 'compteur_articles' dans SPIP
 * 
 * @param string $idb     Identifiant de la boucle
 * @param array $boucles  AST du squelette
 * @param Critere $crit   Paramètres du critère dans cette boucle
 * @return void
 */
function critere_compteur_contacts_dist($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];

	$not="";
	if ($crit->not)
		$not=", 'NOT'";
	$boucle->from['LOC'] = 'spip_organisations_contacts';
	$boucle->from_type['LOC'] = 'left';
	$boucle->join['LOC'] = array("'organisations'","'id_organisation'","'id_organisation'");

	$boucle->select[]= "COUNT(LOC.id_contact) AS compteur_contacts";
	$boucle->group[] = 'organisations.id_organisation';
}


/**
 * Calcul de la balise #COMPTEUR_CONTACTS
 * 
 * Compter les contacts publies lies a une organisation, dans une boucle organisations
 * pour la vue prive/liste/organisations.html
 *
 * Cette balise nécessite le critère compteur_contacts.
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_COMPTEUR_CONTACTS_dist($p) {
	return rindex_pile($p, 'compteur_contacts', 'compteur_contacts');
}




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


/**
 * Calcul de la balise #ORGANISATIONS
 *
 * @deprecated Utiliser #LESORGANISATIONS
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_ORGANISATIONS_dist($p) {
	$f = charger_fonction('LESORGANISATIONS', 'balise');
	return $f($p);
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




// Gestion des branches d'organisation
// -----------------------------------


/**
 * Calcul de la balise IDS_ORGANISATION_BRANCHE
 * 
 * Cette balise retourne un tableau listant toutes les id_organisation d'une branche.
 * 
 * L'identifiant de la branche (id_organisation) est pris dans la boucle
 * la plus proche sinon dans l'environnement, sauf si l'on indique expressément
 * les identifiants désirés
 *
 * @example
 *   ```
 *   #IDS_ORGANISATION_BRANCHE
 *   #IDS_ORGANISATION_BRANCHE{4,10}
 *   <BOUCLE_contacts(CONTACTS){id_organisation IN #IDS_ORGANISATION_BRANCHE}>
 *   ```
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
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
 * Calcul d'une branche d'organisation
 * 
 * Liste des id_organisation contenues dans une organisation donnée
 *
 * @param string|int|array $id
 *   Identifiant(s) d'organisation(s) dont on veut les branches
 * @return string
 *   Liste des identifiants d'organisation de ou des branches, séparés par des virgules.
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



/**
 * Sélectionne dans une boucle les éléments appartenant à une branche d'une organisation
 * 
 * Calcule une branche d'une organisation et conditionne la boucle avec.
 * Cherche l'identifiant de l'organisation en premier paramètre du critère {branche_organisation XX}
 * sinon dans les boucles parentes ou par jointure.
 *
 * @internal
 * 		Copie quasi identique de critere_branche_dist()
 * 
 * @param string $idb
 * 		Identifiant de la boucle
 * @param array $boucles
 * 		AST du squelette
 * @param Critere $crit
 * 		Paramètres du critère dans cette boucle
 * @return void
**/
function critere_branche_organisation_dist($idb, &$boucles, $crit){

	$not = $crit->not;
	$boucle = &$boucles[$idb];
	// prendre en priorite un identifiant en parametre {branche_organisation XX}
	if (isset($crit->param[0])) {
		$arg = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	// sinon on le prend chez une boucle parente
	else {
		$arg = kwote(calculer_argument_precedent($idb, 'id_organisation', $boucles));
	}

	// Trouver une jointure
	$champ = "id_organisation";
	$desc = $boucle->show;
	//Seulement si necessaire
	if (!array_key_exists($champ, $desc['field'])){
		$cle = trouver_jointure_champ($champ, $boucle);
		$trouver_table = charger_fonction("trouver_table", "base");
		$desc = $trouver_table($boucle->from[$cle]);
		if (count(trouver_champs_decomposes($champ, $desc))>1){
			$decompose = decompose_champ_id_objet($champ);
			$champ = array_shift($decompose);
			$boucle->where[] = array("'='", _q($cle.".".reset($decompose)), '"'.sql_quote(end($decompose)).'"');
		}
	}
	else $cle = $boucle->id_table;

	$c = "sql_in('$cle".".$champ', calcul_organisation_branche_in($arg)"
	     .($not ? ", 'NOT'" : '').")";
	$boucle->where[] = !$crit->cond ? $c :
		("($arg ? $c : ".($not ? "'0=1'" : "'1=1'").')');
}


?>
