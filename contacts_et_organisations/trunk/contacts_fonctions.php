<?php

/**
 * Définition des critères, balises, filtres et fonctions
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Fonctions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;



/**
 * Calcul de la balise `#LESORGANISATIONS`
 *
 * Affiche la liste des organisations d'un contact.
 * 
 * - Soit le champs `lesauteurs` existe dans la table et à ce moment là,
 *   on retourne son contenu
 * - Soit la balise appelle le modele `lesorganisations.html` en lui passant
 *   le `id_contact` dans son environnement
 * 
 * @balise
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
 * Calcul de la balise `#ORGANISATIONS`
 *
 * @deprecated Utiliser `#LESORGANISATIONS`
 * @balise
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


/**
 * Calcul du critère `compteur_contacts`
 * 
 * Compter les contacts liés à une organisation, dans une boucle organisations
 * pour la vue `prive/liste/organisations.html`
 *
 * @example
 *   ```
 *   <BOUCLE_o(ORGANISATIONS){compteur_contacts}>
 *     [(#COMPTEUR_CONTACTS|singulier_ou_pluriel{contacts:nb_contact,contacts:nb_contacts})]
 *   ```
 *
 * @note
 *   Fonctionnement inspiré du critère `compteur_articles` dans SPIP
 * 
 * @critere
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
 * Calcul de la balise `#COMPTEUR_CONTACTS`
 * 
 * Compter les contacts publiés liés à une organisation, dans une boucle organisations
 * pour la vue `prive/liste/organisations.html`
 *
 * Cette balise nécessite le critère `compteur_contacts`.
 *
 * @balise
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_COMPTEUR_CONTACTS_dist($p) {
	return rindex_pile($p, 'compteur_contacts', 'compteur_contacts');
}



// Correction de jointures et champs spéciaux
// ------------------------------------------


/**
 * Calcul du critère `contacts_auteurs`
 * 
 * Crée une jointure correcte entre auteurs et contacts et définit quelques
 * champs spéciaux (nom_contact, prenom_contact, ...)
 *
 * @example
 *     ```
 *     <BOUCLE_(CONTACTS){contacts_auteurs} />
 *     <BOUCLE_(AUTEURS){contacts_auteurs} />
 *     ```
 *
 * @critere
 * 
 * @param string $idb
 *     Identifiant de la boucle
 * @param array $boucles
 *     AST du squelette
 * @param Critere $crit
 *     Paramètres du critère dans cette boucle
 * @return
 *     AST complété de la jointure correcte et des champs spéciaux
**/
function critere_contacts_auteurs_dist($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];

	if ($boucle->id_table == 'auteurs') {
		$cle = trouver_jointure_champ('id_contact', $boucle);

		// Il faut déclarer la jointure explicite pour que les balises
		// puissent chercher dans la table jointe.
		// Ainsi #PRENOM sera retrouvé dans contacts, comme si
		// on avait fait (AUTEURS contacts)
		// cf. index_tables_en_pile() de public/references
		$boucle->jointures_explicites = ltrim($boucle->jointures_explicites . ' contacts');

		// On ajoute cependant en plus des champs calculés, potentiellement homonymes
		$boucle->select[] = "$cle.nom AS nom_contact";
		$boucle->select[] = "$cle.prenom AS prenom_contact";
		$boucle->select[] = "$cle.civilite AS civilite_contact";

	} elseif ($boucle->id_table == 'contacts') {
		$cle = trouver_jointure_champ('id_auteur', $boucle);
		$boucle->jointures_explicites = ltrim($boucle->jointures_explicites . ' auteurs');
		$boucle->select[] = "$cle.nom AS nom_auteur";
	} else {
		// si le critère n'est pas sur une table articles ou contacts, c'est un problème.
		return (array('zbug_critere_inconnu', array('critere' => $crit->op." ?")));
	}
}


/**
 * Calcul du critère `organisations_auteurs`
 * 
 * Crée une jointure correcte entre auteurs et organisations et définit quelques
 * champs spéciaux (nom_organisation, ...)
 *
 * @example
 *     ```
 *     <BOUCLE_(ORGANISATIONS){organisations_auteurs} />
 *     <BOUCLE_(AUTEURS){organisations_auteurs} />
 *     ```
 *
 * @critere
 * 
 * @param string $idb
 *     Identifiant de la boucle
 * @param array $boucles
 *     AST du squelette
 * @param Critere $crit
 *     Paramètres du critère dans cette boucle
 * @return
 *     AST complété de la jointure correcte et des champs spéciaux
**/
function critere_organisations_auteurs_dist($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];

	if ($boucle->id_table == 'auteurs') {
		$cle = trouver_jointure_champ('id_organisation', $boucle);

		// cf critere contacts_auteurs pour explication
		$boucle->jointures_explicites = ltrim($boucle->jointures_explicites . ' organisations');

		// On ajoute cependant en plus des champs calculés, potentiellement homonymes
		$boucle->select[] = "$cle.nom AS nom_organisation";

	} elseif ($boucle->id_table == 'organisations') {
		$cle = trouver_jointure_champ('id_auteur', $boucle);
		$boucle->jointures_explicites = ltrim($boucle->jointures_explicites . ' auteurs');
		$boucle->select[] = "$cle.nom AS nom_auteur";
	} else {
		// si le critère n'est pas sur une table articles ou contacts, c'est un problème.
		return (array('zbug_critere_inconnu', array('critere' => $crit->op." ?")));
	}
}


/**
 * Calcul de la balise `#NOM_AUTEUR`
 *
 * Cette balise s'emploie dans une boucle `(CONTACTS){contacts_auteurs}`
 * Elle nécessite le critère `contacts_auteurs` et retourne le champ
 * `#NOM` de la table auteurs liée au contact.
 *
 * @balise
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_NOM_AUTEUR_dist($p) {
	$p = rindex_pile($p, 'nom_auteur', 'contacts_auteurs');
	if ($p->code == "''") {
		$p = rindex_pile($p, 'nom_auteur', 'organisations_auteurs');
	}
	return $p;
}


/**
 * Calcul de la balise `#PRENOM_CONTACT`
 *
 * Cette balise s'emploie dans une boucle `(AUTEURS){contacts_auteurs}`
 * Elle nécessite le critère `contacts_auteurs` et retourne le champ
 * `#PRENOM` de la table contacts liée à l'auteur.
 *
 * @note
 *   Avec simplement le critère `contacts_auteurs`, la balise `#PRENOM`
 *   fonctionne aussi (pour peu que la table articles n'ait pas ce champ
 *   également).
 *
 * @balise
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_PRENOM_CONTACT_dist($p) {
	return rindex_pile($p, 'prenom_contact', 'contacts_auteurs');
}

/**
 * Calcul de la balise `#NOM_CONTACT`
 *
 * Cette balise s'emploie dans une boucle `(AUTEURS){contacts_auteurs}`
 * Elle nécessite le critère `contacts_auteurs` et retourne le champ
 * `#NOM` de la table contacts liée à l'auteur.
 *
 * @balise
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_NOM_CONTACT_dist($p) {
	return rindex_pile($p, 'nom_contact', 'contacts_auteurs');
}


/**
 * Calcul de la balise `#CIVILITE_CONTACT`
 *
 * Cette balise s'emploie dans une boucle `(AUTEURS){contacts_auteurs}`
 * Elle nécessite le critère `contacts_auteurs` et retourne le champ
 * `#CIVILITE` de la table contacts liée à l'auteur.
 *
 * @note
 *   Avec simplement le critère `contacts_auteurs`, la balise `#CIVILITE`
 *   fonctionne aussi (pour peu que la table articles n'ait pas ce champ
 *   également).
 * 
 * @balise
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_CIVILITE_CONTACT_dist($p) {
	return rindex_pile($p, 'civilite_contact', 'contacts_auteurs');
}

/**
 * Calcul de la balise `#NOM_ORGANISATION`
 *
 * Cette balise s'emploie dans une boucle `(AUTEURS){organisations_auteurs}`
 * Elle nécessite le critère `organisations_auteurs` et retourne le champ
 * `#NOM` de la table organisations liée à l'auteur.
 * 
 * @balise
 * 
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_NOM_ORGANISATION_dist($p) {
	return rindex_pile($p, 'nom_organisation', 'organisations_auteurs');
}


// Gestion des branches d'organisation
// -----------------------------------


/**
 * Calcul de la balise `#IDS_ORGANISATION_BRANCHE`
 * 
 * Cette balise retourne un tableau listant toutes les `id_organisation` d'une branche.
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
 *   Pour ce dernier cas, préférer le critère branche_organisation :
 *   <BOUCLE_contacts(CONTACTS){branche_organisation}>
 *   ```
 *
 * @balise
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
 * Liste des `id_organisation` contenues dans une organisation donnée
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
 * Compile le critère `branche_organisation`. Il sélectionne dans une
 * boucle les éléments appartenant à une branche d'une organisation.
 * 
 * Calcule une branche d'une organisation et conditionne la boucle avec.
 * Cherche l'identifiant de l'organisation en premier paramètre du critère
 * `{branche_organisation XX}` sinon dans les boucles parentes ou par jointure.
 *
 * @example
 *   ```
 *   <BOUCLE_contacts(CONTACTS){branche_organisation}>
 *   ```
 * 
 * @internal
 *     Copie quasi identique de `critere_branche_dist()`
 *
 * @critere
 * 
 * @param string $idb
 *     Identifiant de la boucle
 * @param array $boucles
 *     AST du squelette
 * @param Critere $crit
 *     Paramètres du critère dans cette boucle
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
