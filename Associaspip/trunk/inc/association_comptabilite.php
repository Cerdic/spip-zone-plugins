<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('base/association');
include_spip('inc/association_plan_comptable');

/*****************************************
 * @defgroup comptabilite_
 * dedie au module (futur plugin) Comptabilite (ComptaSPIP)
 *
** @{ */

/**
 * Recupere dans les tables la liste des destinations associees a une operation
 *
 * @param int $id_operation
 *   id_compte de l'operation dans spip_asso_compte (et spip_asso_destination)
 * @return array $destinations
 *   Un tableau eventuellement vide de id_destination=>montant
 */
function association_liste_destinations_associees($id_operation) {
	$sql = sql_select('recette,depense,id_destination', 'spip_asso_destination_op', "id_compte=" . intval($id_operation));
	$destinations = array();
	while ( $r = sql_fetch($sql) ) {
	  // soit recette soit depense est egal a 0,
	  // on se contente les additionner
	    $destinations[$r['id_destination']] = $r['recette'] + $r['depense'];
	}
	return $destinations;
}

/**
 * Selecteur de destinations dHTML
 *
 * @param string $destinations
 *   Tableau de destinations deja selectionnees
 *   Ou '' si on ajoute une operation
 * @param bool $unique
 *   Permet de specifier si on veut associer une destination unique (vrai),
 *   par default on peut ventiler sur plusieurs destinations
 * @param int $defaut
 *   Permet de selectionner une destination par defaut (par id_destination)
 *   quand $destinations est vide
 * @return string $res
 *   un <div> le code HTML/javascript correspondant au selecteur de destinations
 */
function association_editeur_destinations($destinations, $defaut='') {
	$options = array();
        // Constuire les balises Options d'un Select
	// mais il faudrait arranger ca si une seule
	$q = sql_select('id_destination,intitule', 'spip_asso_destination', '', '', 'intitule');

	while ($v = sql_fetch($q)) {
	  // Ces deux variables sont utilisees plus bas si une seule iteration
		$id = $v['id_destination'];
		$texte = $v['intitule'];
		$options[$id] = "<option value='$id'>$texte</option>";
	}
	if (!$options) return '';
	$idIndex = 1;

	if (!$GLOBALS['association_metas']['unique_dest']) { // destinations multiples, on insere le js qui permet de les gerer
	      $script = '<script type="text/javascript" src="'
		. find_in_path('javascript/jquery.destinations_form.js')
		. '"></script>';
		$addDestinationButton = "\n<button class='destButton' type='button' onclick='addFormField(); return FALSE;'>+</button>";
	}

	if ( is_array($destinations) ) {
      // si on a une liste de destinations (on edite une operation)
	  $options = join("\n", $options) ;
	  $res = '';
	  foreach ($destinations as $destId => $destMontant) { // restitution des listes de selection HTML
		$res .= '<div id="row'.$idIndex.'" class="choix"><ul>'
		. '<li>'
		. '<select name="id_dest['.$idIndex.']" id="id_dest_'.$idIndex.'" >'
		. preg_replace("/(value='".$destId."')/", '$1 selected="selected"', $options)
		. '</select></li>';

		if (!$GLOBALS['association_metas']['unique_dest']) {
		    $res .= '<li><input name="montant_dest['.$idIndex.']" value="'
			. association_formater_nombre($destMontant)
			. '" type="text" id="montant_dest_'.$idIndex.'" class="number decimal price" />'
			. '<button class="destButton" type="button" onclick="addFormField(); return false;">+</button>';
		    if ($idIndex>1) {
			$res .= '<button class="destButton" type="button" onclick="removeFormField(\'#row'.$idIndex.'\'); return false;">-</button>';
			}
		}
		$res .= '</li></ul></div>';
		$idIndex++;
	  }
	} else { // pas de destination deja definies pour cette operation
	    if ($defaut!='') {
	      $options[$defaut] = str_replace('<option ', '<option selected="selected" ', $options[$defaut]);
	    }
	    $n = " name='id_dest[1]' id='id_dest_1'";
	    if ((count($options) == 1)) { // on a une seule destination possible, pas de selecteur
	      $res = "<input$n readonly='readonly' value='$id' /> $texte";
	    } else  {
	      $res = "<ul>\n<li>"
		. "<select$n>" . join("\n", $options) . '</select>'
		. "\n</li><li><input name='montant_dest[1]' id='montant_dest_1'/>"
		. $addDestinationButton.'</li></ul>';
	    }

	}
    return $script
      . '<div id="divTxtDestination" class="formulaire_edition_destinations">'
      . '<label>'
      . _T('asso:destination')
      . '</label>'
      . $res
      . ($GLOBALS['association_metas']['unique_dest'] ? '' :
	('<input type="hidden" id="idNextDestination" value="'.($idIndex+1).'" />'))
      . '</div>';

}

/**
 * Ajouter une operation comptable ainsi que ses ventilations si necessaire
 *
 * @param string $date
 *   Date de l'operation au format ISO
 * @param float $recette
 *   Montant encaisse
 * @param float $depense
 *   Montant decaisse
 * @param string $justification
 *   Libelle de l'operation
 * @param string $imputation
 *   Compte d'imputation (reference du plan comptable)
 * @param string $journal
 *   Compte financier impacte (reference du plan comptable)
 * @param int $id_journal
 *   ID de l'enregistrement associe dans le module  (chaque imputation etant gere par un seul module)
 * @return int $id_operation
 *   ID de l'operation dans spip_asso_comptes et spip_asso_destination_op
 */
function association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal) {
    $modifs = array(
	'date_operation' => $date,
	'imputation' => $imputation,
	'recette' => $recette,
	'depense' => $depense,
	'journal' => $journal,
	'id_journal' => $id_journal,
	'justification' => $justification
    );
    $id_operation = sql_insertq('spip_asso_comptes', $modifs);
    // on passe par modifier_contenu afin que l'enregistrement soit envoye aux plugins et que Champs Extras 2 la recupere
    include_spip('inc/modifier');
    modifier_contenu('asso_compte', $id_operation, '', $modifs);
    if (!$imputation) { // On laisse passer ce qui est peut-etre une erreur, pour ceux qui ne definisse pas de plan comptable. Mais ce serait bien d'envoyer un message d'erreur au navigateur plutot que de le signaler seulement dans les log
	spip_log("imputation manquante : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, journal=$journal, id_journal=$id_journal, justification=$justification",'associaspip');
    }
    if ($GLOBALS['association_metas']['destinations']) { // Si on doit gerer les destinations
	association_ajouter_destinations_comptables($id_operation, $recette, $depense);
    }
    return $id_operation;

}

/** Modifier une operation comptable ainsi que ses ventilations si necessaire
 *
 * @param string $date
 *   Date de l'operation au format ISO
 * @param float $recette
 *   Montant encaisse
 * @param float $depense
 *   Montant decaisse
 * @param string $justification
 *   Libelle de l'operation
 * @param string $imputation
 *   Compte d'imputation (reference du plan comptable)
 * @param string $journal
 *   Compte financier impacte (reference du plan comptable)
 * @param int $id_journal
 *   ID de l'enregistrement associe dans le module  (chaque imputation etant gere par un seul module)
 * @param int $id_operation
 *   ID de l'operation dans spip_asso_comptes et spip_asso_destination_op
 * @return string $err
 *   Message d'erreur (vide en cas de succes)
 */
function association_modifier_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal, $id_operation) {
    $err = '';
    $id_operation = intval($id_operation);
    if ( sql_countsel('spip_asso_comptes', "id_compte=$id_operation AND vu ") ) { // il ne faut pas modifier une operation verouillee !!!
	spip_log("modification d'operation comptable : id_compte=$id_operation, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal, justification=$justification",'associaspip');
	return $err = _T('asso:operation_non_modifiable');
    }
    if ($GLOBALS['association_metas']['destinations']) { // Si on doit gerer les destinations
	$err = association_ajouter_destinations_comptables($id_operation, $recette, $depense);
    }
    $modifs = array(
	'date_operation' => $date,
	'imputation' => $imputation,
	'recette' => $recette,
	'depense' => $depense,
	'journal' => $journal,
	'justification' => $justification,
    );
    if ($id_journal) { // si id_journal est nul, ne pas le modifier afin de ne pas endommager l'entree dans la base en editant directement depuis le livre de comptes
	$modifs['id_journal'] = $id_journal;
    }
    // on passe par modifier_contenu (et non sql_updateq) pour que la modification soit envoyee aux plugins et que Champs Extras 2 la recupere
    include_spip('inc/modifier');
    modifier_contenu('asso_compte', $id_operation, '', $modifs);
    return $err;
}

/**
 * Supprimer une operation dans spip_asso_comptes ainsi que si necessaire sa ventilation dans spip_asso_destination_op ;
 * cas 1 : usage direct de id_compte
 *
 * @param int $id_operation
 *   ID de l'operation a supprimer
 * @param bool $securite
 *   Mettre a TRUE pour supprimer quand meme une operation verouillee
 * @return int $annulation
 *   ID de l'enregistrement d'ecriture inverse : indique donc une annulation
 *   comptable quand different de 0, et une suppression pure et simple sinon
 */
function association_supprimer_operation_comptable1($id_operation, $securite=FALSE) {
    list($date, $recette, $depense, $imputation, $journal, $id_journal, $verrou) = sql_fetsel('date_operation, recette, depense, imputation, journal, id_journal, vu', 'spip_asso_comptes', "id_compte=$id_operation"); // recuperer les informations sur l'operation pour le fichier de log
    if ( ($securite AND !$verrou) || !$securite ) { // operation non verouillee ou controle explicitement desactive...
	$annulation = 0;
	sql_delete('spip_asso_destination_op', "id_compte=$id_operation"); // on efface de la table destination_op toutes les entrees correspondant a cette operation  si on en trouve
	spip_log("suppression d'operation comptable : id_compte=$id_operation, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal, justification=...",'associaspip'); // on logue quand meme
	sql_delete('spip_asso_comptes', "id_compte=$id_operation"); // on efface enfin de la table comptes l'entree correspondant a cette operation
    } else { // on ne supprime pas les ecritures validees/verouillees ; il faut annuler l'operation par une operation comptable inverse...
	$annulation = sql_insertq('spip_asso_comptes', array(
	    'date_operation' => date('Y-m-d'),
	    'depense' => $recette,
	    'recette' => $depense,
	    'imputation' => _T('asso:compte_annulation_operation', array('numero'=>$id_compte,'date_operation'=>$date) ),
	    'imputation' => $imputation, // pas forcement vrai, mais on fait au plus simples...
	    'journal' => $journal, // pas forcement vrai, mais on fait au plus simples...
	    'id_journal' => -$id_journal, // on garde la trace par rapport au module ayant cree l'operation
	    'vu' => 1, // cette operation n'est pas moifiable non plus...
	) ); // on cree l'operation opposee a celle a annuler ; mais ce n'est pas une annulation correcte au regard des numeros de comptes (imputation/journal)...
#	spip_log("annulation d'operation comptable : id_compte=$id_operation, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal, justification=annule_par_op$annulation",'associaspip'); // on logue quand meme ?
    }
    return $annulation;
}

/**
 * Supprimer une operation dans spip_asso_comptes ainsi que si necessaire sa ventilation dans spip_asso_destination_op ;
 * cas 2 : usage par les modules du couple imputation&id_journal
 *
 * @param int $id_journal
 *   ID de l'enregistrement associe dans le module  (chaque imputation etant gere par un seul module)
 * @param string $pc_journal
 *   Nom de la meta associe au module (renverra le code comptable gere uniquement par ce module)
 * @return int $id_operation
 *   ID de l'enregistrement supprime ou annule
 *   (vaut donc 0 si aucun enregistrement touche)
 */
function association_supprimer_operation_comptable2($id_journal, $pc_journal) {
    $association_imputation = charger_fonction('association_imputation', 'inc');
    if ( $id_operation = sql_getfetsel('id_compte', 'spip_asso_comptes', $association_imputation($pc_journal, $id_journal)) )
	association_supprimer_operation_comptable1($id_operation);
    return $id_operation; // indique quelle operation a ete supprimee (0 si aucune --donc erreur dans les parametres ?)
}

/**
 * Suppression en masse d'operations compatebles avec leur ventilations
 *
 * @param string $critere
 *   Critere de selection SQL des operations a supprimer
 * @retur void
 * @warning
 *   Cette fonction etant sans garde-four est a manipulur avec precaution !
 *   Les suppression sont irreversibles...
 */
function association_supprimer_operations_comptables($critere) {
    $where = sql_in_select('id_compte', 'id_compte', 'spip_asso_comptes', $critere); // on recupere la liste des a supprimer
    sql_delete('spip_asso_destination_op', $where); // on efface les ventilations de ces operations  si on en trouve
    $query_log = sql_select('id_compte, date, recette, depense, imputation, journal, id_journal', 'spip_asso_comptes', $where);
    while ( list($id_compte, $date, $recette, $depense, $imputation, $journal, $id_journal) = fetch($query_log) ) { // on logue les enregistrements a supprimer du livre comptable
	spip_log("suppression d'operation comptable : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal ",'associaspip');
    }
    sql_delete('spip_asso_comptes', $where); // on efface enfin de la table comptes les entrees correspondant a ces operations ($where ou $critere)
}

/**
 * Fonction permettant d'ajouter/modifier les destinations comptables (presente dans $_POST) a une operation comptable
 *
 * @param int $id_compte
 *   ID de l'operation comptable a ventiller
 * @param float $recette
 *   Montant total des recettes a ventiller
 * @param float $depense
 *   Montant total des depenses a ventiller
 * @return void
 */
function association_ajouter_destinations_comptables($id_compte, $recette, $depense) {
    sql_delete('spip_asso_destination_op', "id_compte=$id_compte"); // on efface de la table destination_op toutes les entrees correspondant a cette operation  si on en trouve
    if ($recette>0) {
	$attribution_montant = 'recette';
    } else {
	$attribution_montant = 'depense';
    }
    $toutesDestinationsIds = association_recuperer_liste('id_dest', TRUE);
	$toutesDestinationsMontants = association_recuperer_liste('montant_dest', TRUE);
    if ( count($toutesDestinationsIds)>1 ) { // plusieurs destinations
	foreach ($toutesDestinationsIds as $id => $id_destination) { // ventilation des montants. le tableau des montants a des cles indentique a celui des id
	    $id_dest_op = sql_insertq('spip_asso_destination_op', array(
		'id_compte' => $id_compte,
		'id_destination' => $id_destination,
		$attribution_montant => association_recuperer_montant($toutesDestinationsMontants[$id], FALSE),
	    ));
	}
    } elseif ( count($toutesDestinationsIds)==1 ) { // une seule destination : le montant peut ne pas avoir ete precise, on entre directement le total recette+depense
	$id_dest_op = sql_insertq('spip_asso_destination_op', array(
	    'id_compte' => $id_compte,
	    'id_destination' => $toutesDestinationsIds[1],
	    $attribution_montant => $depense+$recette
	));
    }
}

/**
 * Prepare le critere sur une imputation comptable
 *
 * @param string $nom
 *   Nom de la meta contenant le code d'imputation
 * @param int $id
 *   ID de l'enregistrement associe
 * @param string $table
 *   Nom ou alias de la table a interroger
 * @return string $champ
 *   sous-requete SQL de selection/restriction a une imputation comptable
 */
function inc_association_imputation_dist($nom, $id='', $table='') {
	if ($GLOBALS['association_metas'][$nom])
		$w = ($table ? ($table . '.') : '') . 'imputation='. sql_quote($GLOBALS['association_metas'][$nom]);
	else $w = '';
	$w2 = $id ? ("id_journal=".intval($id)) : '';
	return ($w AND $w2) ? "$w AND $w2" : "$w$w2";
}

/**
 * Valide le plan comptable :
 *- on doit avoir au moins deux classes de comptes differentes
 *- le code de chaque compte doit etre unique
 *- le code du compte doit commencer par un chiffre egal a sa classe
 *
 * @return bool
 *   TRUE si le plan comptable est valide
 *   FALSE si le plan comptable est invalide
 */
function association_valider_plan_comptable() {
    $classes = array();
    $codes = array();
    $query = sql_select('code, classe', 'spip_asso_plan'); // recupere le code et la classe de tous les comptes du plan comptabl
    while ($data = sql_fetch($query)) {
	$classe = $data['classe'];
	$code = $data['code'];
	$classes[$classe] = 0; // on comptes les classes differentes
	if(array_key_exists($code, $codes)) {
	    return FALSE; // on a deux fois le meme code
	} else {
	    $codes[$code] = 0;
	}
	if ((!preg_match("/^[0-9]{2}\w*$/", $code)) || ($code[0]!=$classe)) // on verifie que le code est bien de la forme chiffre-chiffre-caracteres alphanumeriques et que le premier digit correspond a la classe
	    return FALSE;
    }
    if (count($classes)<2)
	return FALSE; // on doit avoir au moins deux classes differentes
    return TRUE;
}

/**
 * Tableau des comptes d'une classe du plan comptable
 *
 * @param int $val
 *   Classe dont on veut recuprer les comptes
 * @param int active
 *   Ce parametre facultatif permet de se restreindre aux comptes actifs (1) ou inactifs (0)
 * @return array $res
 *   retourne un tableau $code=>$intitule trie par code
 */
function association_liste_plan_comptable($val, $actives='') {
    $res = array();
    $query = sql_select('code, intitule', 'spip_asso_plan', "classe='$val'".($actives!=''?" AND active=$actives":''), '', 'code'); // recupere le code et l'intitule de tous les comptes de classe $val
    while ($data = sql_fetch($query)) {
	$res[$data['code']] = $data['intitule'];
    }
    return $res;
}

/**
 * Recupere le code du compte des virements internes
 *
 * @return string $res
 *   C'est le code normalement defini dans la configuration du plugin.
 *   S'il n'existe pas, on prend le premier compte 58x existant,
 *   sinon on cree le compte 581 !
 */
function association_creer_compte_virement_interne() {
    if ($GLOBALS['association_metas']['pc_intravirements']) // un code de virement interne est deja defini !
	return $GLOBALS['association_metas']['pc_intravirements'];
    $res = association_liste_plan_comptable($GLOBALS['association_metas']['classe_banques']); // on recupere tous les comptes de la classe "financier" (classe 5)
    foreach($res as $code => $libelle) { // existe-t-il le compte 58x ? (nota : c'est la compta francaise...)
	if (substr($code,1,1)=='8') // il existe un code qui commence par 58...
	    return $code;
    }
    // j'ai rien trouve, je cree le compte 581
    $code = $GLOBALS['association_metas']['classe_banques'].'81';
    $id_plan = sql_insertq('spip_asso_plan', array(
	'code' => $code,
	'intitule' => _T('asso:virement_interne'),
	'classe' => $GLOBALS['association_metas']['classe_banques'],
	'type_op' => 'multi',
	'solde_anterieur' => '0',
	'date_anterieure' => date('Y-m-d'),
	'commentaire' => _T('asso:compte_cree_automatiquement'),
	'active' => '0',
	'maj' => date('Y-m-d')
    ));
    if ($id_plan)
	sql_insertq('spip_association_metas', array(
	    'nom' => 'pc_intravirements',
	    'valeur' => $code,
	));
    return $code;
}

/* on recupere les parametres de requete a passer aux fonctions */
function association_passeparam_compta($classes=array()) {
    $params = array(); // initialisation de la liste
//    list($params['id_periode'], $params['sql_periode']) = association_passeparam_periode('operation', 'comptes', $id_compte);
    $params['id_periode'] = association_passeparam_periode();
    if ($GLOBALS['association_metas']['exercices']) {
#    	$params['id_periode'] = association_passeparam_exercice();
	$params['type_periode'] = 'exercice';
	$exercice = sql_fetsel('date_debut, date_fin, intitule', 'spip_asso_exercices', "id_exercice=$params[id_periode]");
	$params['debut_periode'] = $exercice['date_debut'];
	$params['fin_periode'] = $exercice['date_fin'];
	$params['titre_periode'] = $exercice['intitule'];
    } else {
#    	$params['id_periode'] = association_passeparam_annee();
	$params['type_periode'] = 'annee';
	$params['debut_periode'] = "$params[id_periode]-01-01";
	$params['fin_periode'] = "$params[id_periode]-12-31";
	$params['titre_periode'] = $params['id_periode'];
    }
    $params['destination'] = association_recuperer_entier('destination');
    $params['type'] = _request('type');
    if ( !$classes ) { // pas en parametre, on prend dans la requete
//	$params['classes'] = array_flip( explode(',', association_recuperer_liste('classes')) );
	$keys = association_recuperer_liste('classes');
	if ( count($keys) ) {
	    $vals = array_fill(0, count($keys) ,0);
	    $params['classes'] = array_combine($keys, $vals);
	} else {
	    $params['classes'] = array();
	}
    } elseif ( is_array($classes) ) { // c'est a priori bon
	$params['classes'] = $classes;
    } else { // c'est un tableau de classe_comptable=>type_operations qui est requis !
	$params['classes'] = $classes ? array( $classes=>0 ) : array() ;
    }
    $params['url'] = serialize($params); //!\ les cles numeriques peuvent poser probleme... <http://www.mail-archive.com/php-bugs@lists.php.net/msg100262.html> mais il semble qu'ici le souci vient de l'absence d'encodage lorsqu'on passe $var par URL...
    return $params;
}

/**
 * On recupere les soldes des differents comptes de la classe specifiee pour la periode specifiee
 *
 * @param int $classe
 *   Classe dont on veut recuperer les soldes des differents comptes
 * @param int $periode
 *   ID exercice ou annee (selon configuration)
 * @param int $destination
 *   ID destination
 * @param float $direction
 *   Le signe de ce parametre indique le type de compte (et donc le sens de calcul du solde)
 *   positif : comptes de credit (solde=recettes-depenses)
 *   negatif : comptes de debit (solde=depenses-recettes)
 * @return ressource $query
 *   Resultat de la requete donnant les soldes de chaque compte de la classe indiquee
 * @note
 *   d'apres http://www.lacompta.ch/MITIC/theorie.php?ID=26 c'est le solde qui est recherche, et il corresponde bien a :
 *  recettes-depenses=recettes pour les classes 6
 *  depenses-recettes=depenses pour les classes 7
 */
function association_calcul_soldes_comptes_classe($classe, $periode=0, $destination=0, $direction='-1') {
    $c_group = (($classe==$GLOBALS['association_metas']['classe_banques'])?'journal':'imputation');
    $valeurs = (($direction)
	?
	( ($direction<0)
	    ?'SUM('.(($destination)?'a_d':'a_c').'.depense-'.(($destination)?'a_d':'a_c').'.recette) AS valeurs'
	    : 'SUM('.(($destination)?'a_d':'a_c').'.recette-'.(($destination)?'a_d':'a_c').'.depense) AS valeurs'
	)
	:
	'SUM('.(($destination)?'a_d':'a_c').'.recette) AS recettes, SUM('.(($destination)?'a_d':'a_c').'.depense) as depenses, SUM('.(($destination)?'a_d':'a_c').'.recette-'.(($destination)?'a_d':'a_c').'.depense) AS soldes' );
    $c_having = ($direction) ? 'valeurs>0' : ''; // on ne retiendra que les totaux non nuls...
    if ( sql_countsel('spip_asso_plan','active=1') ) { // existence de comptes actifs
	$p_join = " RIGHT JOIN spip_asso_plan AS a_p ON a_c.$c_group=a_p.code";
	$p_select = ', a_p.code, a_p.intitule, a_p.classe';
	$p_order = 'a_p.code'; // imputation ou journal
#	$p_where = 'a_p.classe='.sql_quote($classe);
	$p_having = 'a_p.classe='.sql_quote($classe); // ok : on agrege par code (indirectement) associe a une classe unique selectionnee ...
    } else { // pas de comptes actifs ?!?
	$p_join = $p_select = $p_where = $p_having = '';
	$p_order = $c_group; // imputation ou journal
    }
    if ( $periode ) { // restriction sur une periode donnee
	if ($GLOBALS['association_metas']['exercices']) { // exercice budgetaire personnalise
	    $exercice = sql_fetsel('date_debut, date_fin', 'spip_asso_exercices', "id_exercice=".intval($periode));
	    $c_where = "a_c.date_operation>='$exercice[date_debut]' AND a_c.date_operation<='$exercice[date_fin]' ";
	} else { // exercice budgetaire par annee civile
	    $c_where = "DATE_FORMAT(a_c.date_operation, '%Y')=".intval($periode);
	}
#    } elseif ( $classe==$GLOBALS['association_metas']['classe_banques'] ) { // encaisse
#	$c_where = 'LEFT(a_c.imputation,1)<>'. sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']) .' AND a_c.date>=a_p.date_anterieure AND a_c.date<=NOW() ';
    } else { // tout depuis le debut ?!?
	$c_where = 'a_c.date_operation<=NOW()'; // il faut mettre un test valide car la chaine peut etre precedee de "AND "...  limiter alors a aujourd'hui ?
    }
    $query = sql_select(
	"$c_group, $valeurs ". ($destination ? ', a_d.id_destination' : '') .$p_select, // select
	'spip_asso_comptes AS a_c '. ($destination ? 'LEFT JOIN spip_asso_destination_op AS a_d ON a_d.id_compte=a_c.id_compte ' : '') .$p_join, // from
	($destination ? "a_d.id_destination=$destination AND " : '') . ($p_where?"$p_where AND ":'')  .$c_where, // where
	$c_group, // group by
	$p_order, // order by
	'', // limit
	$c_having. (($c_having && $p_having)?' AND ':'') .$p_having // having
    );
    return $query;
}

/**
 * On affiche les totaux (recettes et depenses) des differents comptes de la classe specifiee pour une periode donnee
 *
 * @param array $classes
 *   Liste des classes dont on veut afficher les soldes des differents comptes
 * @param string $prefixe
 *   Prefixe a applique aux termes qualifiant la direction pour former le titre du tableau
 * @param float $direction
 *   Le signe de ce parametre indique le type de compte (et donc le sens de calcul du solde)
 *   positif : comptes de credit (solde=recettes-depenses)
 *   negatif : comptes de debit (solde=depenses-recettes)
 * @param int $periode
 *   ID exercice ou annee (selon configuration)
 * @param int $destination
 *   ID destination
 * @return void
 *   HTML-Table listant les differents soldes ordonnes par classes puis par numeros de compte
 */
function association_liste_totaux_comptes_classes($classes, $prefixe='', $direction='-1', $periode=0, $destination=0) {
    if( !is_array($classes) ) { // a priori une chaine ou un entier d'une unique classe
	$liste_classes = array( $classes ) ; // transformer en tableau (puisqu'on va operer sur des tableaux);
    } else { // c'est un tableau de plusieurs classes
	$liste_classes = $classes;
    }
    $titre = $prefixe.'_'. ( ($direction) ? (($direction<0)?'depenses':'recettes') : 'soldes' );
    echo "<table width='100%' class='asso_tablo' id='asso_tablo_$titre'>\n";
    echo "\n<tr>";
    echo '<th scope="col" style="width:10px">&nbsp;</th>';
    echo '<th scope="col" style="width:30px">&nbsp;</th>';
    echo '<th scope="col">'. _T("asso:$titre") .'</th>';
    if ($direction) { // mode liste comptable : charge, produit, actifs, passifs
	echo '<th scope="col" style="width:80px">&nbsp;</th>';
    } else { // mode liste standard : contributions volontaires et autres
	echo '<th scope="col" style="width:80px">'. _T("asso:$prefixe".'_recettes') .'</th>';
	echo '<th scope="col" style="width:80px">'. _T("asso:$prefixe".'_depenses') .'</th>';
	// echo '<th scope="col" width="80">'. _T("asso:$prefixe".'_solde') .'</th>';
    }
    echo "</tr>\n";
    $total_valeurs = $total_recettes = $total_depenses = 0;
    $chapitre = '';
    $i = 0;
    foreach ( $liste_classes as $rang => $classe ) {
	$query = association_calcul_soldes_comptes_classe($classe, $periode, $destination, $direction );
	while ($data = sql_fetch($query)) {
	    echo '<tr>';
	    $new_chapitre = substr($data['code'], 0, 2);
	    if ($chapitre!=$new_chapitre) {
		echo '<td class="text">'. $new_chapitre . '</td>';
		echo '<td colspan="3" class="text">'. ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'")) .'</td>';
		$chapitre = $new_chapitre;
		echo "</tr>\n<tr>";
	    }
#	    if ( floatval($data['valeurs']) || floatval($data['recettes']) || floatval($data['depenses']) ) { // non-zero...
		echo "<td>&nbsp;</td>";
		echo '<td class="text">'. $data['code'] .'</td>';
		echo '<td class="text">'. $data['intitule'] .'</td>';
		if ($direction) { // mode liste comptable
		    echo '<td class="decimal">'. association_formater_nombre($data['valeurs']) .'</td>';
		    $total_valeurs += $data['valeurs'];
		} else { // mode liste standard
		    echo '<td class="decimal">'. association_formater_nombre($data['recettes']) .'</td>';
		    $total_recettes += $data['recettes'];
		    echo '<td class="decimal">'. association_formater_nombre($data['depenses']) .'</td>';
		    $total_depenses += $data['depenses'];
		    //echo '<td class="decimal">'. association_formater_nombre($data['soldes']) .'</td>';
		    $total_valeurs += $data['soldes'];
		}
		echo "</tr>\n";
#	    }
	}
    }
    echo "\n<tr class='row_first'>";
    echo '<th colspan="2">&nbsp;</th>';
    echo '<th scope="row" class="text solde">'. _T("asso:$prefixe".'_total') .'</th>';
    if ($direction) { // mode liste comptable
	echo '<th class="solde decimal">'. association_formater_nombre($total_valeurs) . '</th>';
    } else { // mode liste standard
	echo '<th class="entree decimal">'. association_formater_nombre($total_recettes) . '</th>';
	echo '<th class="sortie decimal">'. association_formater_nombre($total_depenses) . '</th>';
	// echo '<th class="solde decimal">'. association_formater_nombre($total_valeurs) . '</th>';
    }
    echo "</tr>\n</table>\n";
    return $total_valeurs;
}

/**
 * On affiche la difference entre les recettes et les depenses (passees en parametre) pour les classes d'un exercice
 * @param float $recettes
 *   Total des recettes
 * @param float $depenses
 *   Total des depenses
 * @return void
 *   Table-HTML presentant le solde comptable (deficit ou benefice)
 */
function association_liste_resultat_net($recettes, $depenses) {
    echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_solde'>\n";
    echo "<tr>";
    echo '<th style="width: 10px">&nbsp;</th>';
    echo '<th style="width: 30px">&nbsp;</th>';
    echo '<th scope="row">'. _T('asso:cpte_resultat_titre_resultat') .'</th>';
    echo '<th style="width: 80px">&nbsp;</th>';
    echo "</tr>";
    echo "\n<tr>";
    echo '<th colspan="2">&nbsp;</th>';
    $res = $recettes-$depenses;
    echo '<th class="solde text">'. (($res<0) ? _T('asso:cpte_resultat_perte') : _T('asso:cpte_resultat_benefice')) .'</th>';
    echo '<th class="solde decimal">'. association_formater_nombre(abs($res)) .'</th>';
    echo "</tr></table>";
}

function export_compte($ids, $mode, $icone = true)
{
	// exports connus (a completer au besoin)
	foreach(array('csv','ctx','dbk','json','tex','tsv','xml','yaml') as $t){
			$args = $ids['id_periode'] . "-$mode-"
			. $ids['type_periode']
			.($ids['destination']? ('-' . $ids['destination']) :'');

		$s = ($t == 'tex') ? 'latex' : $t;
		$script = "export_soldescomptes_$s";
		include_spip('inc/actions');
		$url = generer_action_auteur($script, $args);
		$t = strtoupper($t);
		if ($icone)
		  echo association_navigation_raccourci1($t, $url, 'export-24.png');
		else echo "<a href='$url'>$t</a> ";
	}
}


// Brique commune aux classes d'exportation des etats comptables
class ExportComptes_TXT {

    var $periode; // id_exercice || annee
    var $destination; // id_destination
    var $type; // type d'export : bilan|resultat
    var $classes; // liste des classes a exporter
    var $titre; // intitule de l'exercice
    var $out; // contenu du fichier

    /**
     * Constructeur (fonction d'initialisatio de la classe)
     *
     * @param array|string $var
     *   Tableau des parametres (les cles sont : id_periode, id_destination, titre_periode, classes, titre)
     *   Ce tableau peut etre serialise et c'est la chaine de caracteres resultante qui est passee
     *   Enfin, quand il n'y a rien, on recupere les differents elements dans l'environnement
     * @return $this->
     *   Les proprietes de la classe sont initialisees
     */
    function __construct($var='') {
	if ( !$var ) // non transmis
	    $tableau = association_passeparam_compta(); // recuperer dans l'environnement (parametres d'URL)
	elseif ( is_string($var) ) // transmis comme lien serialise
	    $tableau = unserialize(rawurldecode($var));
	elseif ( is_array($var) ) // transmis comme tableau PHP
	    $tableau = $var;
	else
	    $tableau = array($var=>0);
	$this->periode = intval($tableau['id_periode']);
	$this->destination = intval($tableau['destination']);
	$this->type = $tableau['type'];
	$this->titre = ($tableau['titre_periode']);
	if ( count($tableau['classes']) ) { // on a la liste des classes qui est fournie
	    $this->classes = $tableau['classes'];
	} else { // on sait retrouver la liste des tables en se basant sur le type d'exportation
	    switch ($tableau['type']) {
		case 'bilan' :
		    $query = sql_select(
			'classe', // select
			'spip_asso_plan', // from
			sql_in('classe', array($GLOBALS['association_metas']['classe_charges'],$GLOBALS['association_metas']['classe_produits'],$GLOBALS['association_metas']['classe_contributions_volontaires']), 'NOT'), // where  not in
			'classe', // group by
			'classe' // order by
		    );
		    while ($data = sql_fetch($query)) {
			$this->classes[$data['classe']] = 0;
		    }
		    break;
		case 'resultat' :
		    $this->classes = array($GLOBALS['association_metas']['classe_charges']=>'-1', $GLOBALS['association_metas']['classe_produits']=>'+1', $GLOBALS['association_metas']['classe_contributions_volontaires']=>0);
		    break;
	    }
	}
	$this->out = '';
    }

    /**
     * Export texte de type tableau (lignes*colonnes) simple : CSV,CTX,HTML*SPIP,INI*,TSV,etc.
     *
     * de par la simplicite recherchee il n'y a pas de types ou autres : CSV et CTX dans une certaine mesure pouvant distinguer "nombres", "chaines alphanumeriques" et "chaine binaires encodees"
     *
     * @param string $champsSeparateur
     *   Caractere separant deux champs/colonnes.
     *   (par exemple : la virgule)
     * @param string $lignesSeparateur
     *   Caractere separant deux lignes/enregistrements.
     *   (par exemple : le saut de ligne)
     * @param array $echappements
     *   Tableaux des remplacemens simples a effectuer : "des ceci"=>"par cela"
     *   Il faut, en effet, souvent proteger la presence de caracteres speciaux
     *   qui sont utilises comme parametres ici.
     * @param string $champDebut
     *   Caracter place au debut de chaque champ/colonne
     * @param string $champFin
     *   Caracter place a la fin de chaque champ/enregistrement
     * @param bool $entete
     *   Indique si en plus des donnees il faut rajouter (vrai --par defaut) ou pas (faux) une ligne de titre au debut
     * @param bool $multi
     *   Indique si on recupere directement le solde (faux --par defaut) ou si on recupere separement les totaux des recettes et des depenses
     * @return string $this->out
     *   Contenu de l'export
     */
    function exportLignesUniques($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='', $entete=TRUE, $multi=FALSE) {
	if ($entete) {
	    $this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_code')))) .$champFin.$champsSeparateur;
	    $this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_intitule')))) .$champFin.$champsSeparateur;
	    if (!$multi) {
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_montant')))) .$champFin.$lignesSeparateur;
	    } else {
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_recette')))) .$champFin.$champsSeparateur;
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_depense')))) .$champFin.$lignesSeparateur;
	    }
	}
	foreach ($this->classes as $laClasse=>$laDirection) {
	    $query = association_calcul_soldes_comptes_classe($laClasse, $this->periode, $this->destination, $multi?0:$laDirection);
	    $chapitre = '';
	    $i = 0;
	    while ($data = sql_fetch($query)) {
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
		    $this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $new_chapitre) .$champFin.$champsSeparateur;
		    $this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))) .$champFin.$champsSeparateur;
		    $this->out .= $champsSeparateur.' '.$champsSeparateur;
		    $this->out .= $lignesSeparateur;
		    $chapitre = $new_chapitre;
		}
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $data['code']) .$champFin.$champsSeparateur;
		$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), $data['intitule']) .$champFin.$champsSeparateur;
		if (!$multi) {
		    $this->out .= $champDebut. ($laDirection?$data['valeurs']:$data['recettes']-$data['depenses']) .$champFin.$lignesSeparateur;
		} else {
		    $this->out .= $champDebut.$data['recettes'].$champFin.$champsSeparateur;
		    $this->out .= $champDebut.$data['depenses'].$champFin.$lignesSeparateur;
		}
	    }
	}
    }

    /**
     * Export texte de type s-expression / properties-list / balisage (conteneurs*conteneurs*donnees) simple : JSON, XML (utilisable avec ASN.1), YAML, etc.
     *
     * de par la simplicite recherchee il n'y a pas de types ou d'attributs : BSON, Bencode, JSON, pList, XML, etc.
     *
     * @param array $balises
     *   Tableau des balises d'ouverture (...1) et de fermeture (...0) a appliquer.
     *   Elles sont indexees par des cles (...N) convenues ainsi :
     * - titre : pour l'intitule de la synthese exportee
     * - nom : pour le nom de l'association
     * - exercice : pour l'intitule de l'exercice
     * - categorie : pour ?
     * - chapitre : pour ?
     * - libelle : pour ?
     * - code : pour la reference comptable d'un compte
     * - intitule : pour l'intitule renseigne pour un compte
     * - credit : pour la somme des recettes d'un compte
     * - debit : pour la somme des depenses d'un compte
     * - montant : pour le sode d'un compte
     * @param array $echappements
     *   Tableaux des remplacemens simples a effectuer : "des ceci"=>"par cela"
     *   Il faut, en effet, souvent proteger la presence de caracteres speciaux
     *   qui sont utilises comme parametres ici.
     * @param string $champDebut
     *   Caracter place au debut de chaque champ/colonne
     * @param string $champFin
     *   Caracter place a la fin de chaque champ/enregistrement
     * @param string $ident
     *   Caractere d'indentation des blocs
     * @param bool $entetePerso
     *   Indique si en plus des donnees il faut rajouter (vrai --par defaut) ou pas (faux) une ligne de titre au debut
     * @param bool $multi
     *   Indique si on recupere directement le solde (faux --par defaut) ou si on recupere separement les totaux des recettes et des depenses
     * @return string $this->out
     *   Contenu de l'export
     */
    function exportLignesMultiples($balises, $echappements=array(), $champDebut='', $champFin='', $indent="\t", $entetesPerso='', $multi=FALSE) {
	$this->out .= "$balises[compteresultat1]\n";
	if (!$entetesPerso) {
	    $this->out .= "$indent$balises[entete1]\n";
	    $this->out .= "$indent$indent$balises[titre1] $champDebut". utf8_decode(html_entity_decode(_T('asso:cpte_resultat_titre_general'))) ."$champFin $balises[titre0]\n";
	    $this->out .= "$indent$indent$balises[nom1] $champDebut". $GLOBALS['association_metas']['nom'] ."$champFin $balises[nom0]\n";
	    $this->out .= "$indent$indent$balises[exercice1] $champDebut". $this->titre ."$champFin $balises[exercice0]\n";
	    $this->out .= "$indent$balises[entete0]\n";
	}
	foreach ($this->classes as $laClasse=>$laDirection) {
	    $baliseClasse = $nomClasse.'1';
	    $this->out .= "$indent$balises[$baliseClasse]\n";
	    $query = association_calcul_soldes_comptes_classe($laClasse, $this->periode, $this->destination, $laDirection);
	    $chapitre = '';
	    $i = 0;
	    while ($data = sql_fetch($query)) {
		if ( !$laDirection ) {
		    $valeurs = ($data['depenses']>0)?$data['depenses']:$data['recettes'];
		} else {
		    $valeurs = $data['valeurs'];
		}
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) {
		    if ($chapitre!='') {
			$this->out .= "$indent$indent$balises[chapitre0]\n";
		    }
		    $this->out .= "$indent$indent$balises[chapitre1]\n";
		    $this->out .= "$indent$indent$indent$balises[code1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), $new_chapitre) ."$champFin $balises[code0]\n";;
		    $this->out .= "$indent$indent$indent$balises[libelle1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), ($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))) ."$champFin $balises[libelle0]\n";
		    $chapitre = $new_chapitre;
		}
		$this->out .= "$indent$indent$indent$balises[categorie1]\n";
		$this->out .= "$indent$indent$indent$indent$balises[code1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), $data['code']) ."$champFin $balises[code0]\n";
		$this->out .= "$indent$indent$indent$indent$balises[intitule1] $champDebut". str_replace(array_keys($echappements), array_values($echappements), $data['intitule']) ."$champFin $balises[intitule0]\n";
		if ( !$multi ) {
		    $this->out .= "$indent$indent$indent$indent$balises[montant1] $champDebut".$valeurs."$champFin $balises[montant0]\n";
		} else {
		    $this->out .= "$indent$indent$indent$indent$balises[credit1] $champDebut".$data['recettes']."$champFin $balises[credit0]\n";
		    $this->out .= "$indent$indent$indent$indent$balises[debit1] $champDebut".$data['depenses']."$champFin $balises[debit0]\n";
		}
		$this->out .= "$indent$indent$indent$balises[categorie0]\n";
	    }
	    if ($chapitre!='') {
		$this->out .= "$indent$indent$balises[chapitre0]\n";
	    }
	    $baliseClasse = $nomClasse.'0';
	    $this->out .= "$indent$balises[$baliseClasse]\n";
	}
	$this->out .= "$balises[compteresultat0]\n";
    }

    /**
     * Fichier texte final a afficher/telecharger
     *
     * @param string $ext
     *   Extension a donner au fichier
     * @param string $subtype
     *   Sous-type a inclure dans le nom du fichier
     *   Par defaut, c'est le type d'export (bilon ou resultat).
     * @return
     */
    function leFichier($ext, $subtype='') {
	$fichier = 'compte_'. ($subtype?$subtype:$this->type) .'_'.$this->periode.'_'.$this->destination.".$ext";

	header('Content-type: application/'.$ext);
	header('Content-Disposition: attachment; filename="'.$fichier.'"');
	echo  $this->out;
    }

}

if (test_plugin_actif('FPDF')) {

    define('FPDF_FONTPATH', 'font/');
    include_spip('fpdf');
    include_spip('inc/charsets');
    include_spip('inc/association_plan_comptable');

class ExportComptes_PDF extends FPDF {

    // variables de parametres de mise en page
    var $icone_h = 20;
    var $icone_v = 20;

    // variables de mise en page calculees
    var $largeur_utile = 190; // largeur sans les marges droites et gauches
    var $cell_padding = 2; // espacement entre les bords des cellules et leur contenu

    // position du curseur
    var $xx = 0; // abscisse 1ere boite
    var $yy = 0; // ordonnee 1ere boite

    // variables de fonctionnement passees en parametre
    var $periode; // id_exercice ou annee
    var $destination; // id_destination
    var $titre; // intitule de l'exercice

    /**
     * Initialisations
     * @param array $ids
     *   Tableau des parametres (les cles sont : id_periode, id_destination, titre_periode, classes, titre)
     *   Quand il n'y a rien, on recupere les differents elements dans l'environnement
     * @return $this->
     *   Les proprietes de la classe sont initialisees
     */
    function init($ids='') {
	if ( !$ids ) // tableau de parametres non transmis
	    $ids = association_passeparam_compta(); // recuperer dans l'environnemet (parametres d'URL)
	// passer les parametres transmis aux variables de la classe
	$this->periode = $ids['id_periode'];
	$this->destination = $ids['destination'];
	$this->titre = $ids['titre_periode'];
	// calculer les dimensions de mise en page
	$this->largeur_utile = ($GLOBALS['association_metas']['fpdf_widht']?$GLOBALS['association_metas']['fpdf_widht']:210)-2*($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:10);
	$this->cell_padding = ($GLOBALS['association_metas']['fpdf_marginc']?$GLOBALS['association_metas']['fpdf_marginc']:2);
	// initialiser les variables de mise en page
	$this->xx = ($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:10); // marge gauche
	$this->yy = ($GLOBALS['association_metas']['fpdf_margint']?$GLOBALS['association_metas']['fpdf_margint']:10); // marge haute
	// meta pour le fichier PDF
	$this->SetAuthor('Marcel BOLLA');
	$this->SetCreator('Associaspip & Fpdf');
	$this->SetTitle('Module Comptabilite');
	$this->SetSubject('Etats comptables');
	// typo par defaut
	$this->underline = '';
	$this->FontStyle = '';
	$this->FontSizePy = 12;
	$this->FontFamily = ($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial');
	// engager la page
	// http://fpdf.org/en/doc/addpage.htm
	$this->AddPage($GLOBALS['association_metas']['fpdf_orientation'],
		       $GLOBALS['association_metas']['fpdf_format']
		       ? $GLOBALS['association_metas']['fpdf_format']
		       : array(($GLOBALS['association_metas']['fpdf_widht']
				? $GLOBALS['association_metas']['fpdf_widht']
				: 210),
			       ($GLOBALS['association_metas']['fpdf_height']
				? $GLOBALS['association_metas']['fpdf_height']
				:297) ) );
    }

    /**
     * Pied de pages :
     * redefinition de FPDF::Footer() qui est automatiquement appele par FPDF::AddPage() et FPDF::Close() !
     *
     * @note
     *   http://www.id.uzh.ch/cl/zinfo/fpdf/doc/footer.htm
     *   Adapter la marge basse (et la hauteur utile) des pages en consequence
     */
    function Footer() {
	// Positionnement a 2 fois la marge du bas
	$this->SetY(-2*($GLOBALS['association_metas']['fpdf_margint']?$GLOBALS['association_metas']['fpdf_margint']:10));
	// typo
	$this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), 'I', 8); // police: italique 8px
	$this->SetTextColor(128); // Couleur du texte : gris-50.2% (fond blanc)
	// Date et Numéro de page
	include_spip('inc/filtres');
	$this->Cell(0, 10, html_entity_decode(_T('asso:cpte_export_pied_notice') .' -- '. affdate(date('Y-m-d')) .' -- '. _T('asso:cpte_export_page', array('numero'=>$this->PageNo()) )), 0, 0, 'C');
    }

    /**
     * Haut de pages :
     * redefinition de FPDF qui est directement appele par FPDF::AddPage()
     * @note
     *   http://www.id.uzh.ch/cl/zinfo/fpdf/doc/header.htm
     *   Adapter la marge haute (et la hauteur utile) des pages en consequence
    */
    function Header() {
	// nop
    }

    /**
     * Cartouche au debut de la 1ere page
     *
     * @param string $titre
     *   Nom de l'export : place au dessous le nom de l'association et au dessus de l'intitule de l'exercice
     * @return void
     *   Le contenu du PDF
     * @note
     *   Contrairement au Header ceci fait partir du contenu/flux et n'est pas repete sur toutes les pages, et peut accepter des parametres
     */
    function association_cartouche_pdf($titre='') {
	// Les coordonnees courantes
	$xc = $this->xx+$this->cell_padding;
	$yc = $this->yy+$this->cell_padding;
	$this->SetDrawColor(128); // La couleur du trace : gris 50.2% (sur fond blanc)
	// Le logo du site
#	$chercher_logo = charger_fonction('chercher_logo', 'inc');
#	$logo = $chercher_logo(0, 'id_site');
	$logo = find_in_path('IMG/siteon0.jpg'); // Probleme FPDF et images non JPEG :-/ http://forum.virtuemart.net/index.php?topic=75616.0
	if ($logo) {
	    include_spip('/inc/filtres_images_mini');
	    $this->Image(extraire_attribut(image_reduire($logo, $this->icone_h, $this->icone_v), 'src'), $xc, $yc, $this->icone_h);
	}
	// typo
	$this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), 'B', 22); // police : gras 22px
	$this->SetFillColor(235); // Couleur du cadre, du fond du cadre : gris-92,2%
	$this->SetTextColor(0); // Couleur du texte : noir
	$largeur_pour_titre = $this->largeur_utile-$this->icone_h-3*$this->cell_padding;
	// Titre centre
	$xc += $this->cell_padding+($logo?$this->icone_h:0);
	$this->SetXY($xc, $yc);
	$this->Cell($logo?($largeur_pour_titre):($largeur_pour_titre+$this->icone_h-$this->cell_padding), 12, html_entity_decode(_T("asso:$titre")), 0, 0, 'C', TRUE);
	$yc += 12;
	$this->Ln($this->cell_padding); // Saut de ligne
	$yc += $this->cell_padding;
	// typo
	$this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), '', 12); // police : normal 12px
	$this->SetFillColor(235); // Couleur de remplissage : gris-92.2%
	// Sous titre Nom de l'association
	$this->SetXY($xc, $yc);
	$this->Cell($logo?$largeur_pour_titre:$largeur_pour_titre+$this->icone_h-$this->cell_padding, 6, utf8_decode(_T('asso:cpte_export_association', array('nom'=>$GLOBALS['association_metas']['nom']) )), 0, 0, 'C', TRUE);
	$yc += 6;
	$this->Ln($this->cell_padding/2); // Demi saut de ligne
	$yc += $this->cell_padding/2;
	// typo
	$this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), '', 12); // police : normal 12px
	$this->SetFillColor(235); // Couleur de fond : gris-92.2%
	//Sous titre Intitule de l'exercice
	$this->SetXY($xc, $yc);
	$this->Cell($logo?$largeur_pour_titre:$largeur_pour_titre+$this->icone_h-$this->cell_padding, 6, utf8_decode(_T('asso:cpte_export_exercice', array('titre'=>$this->titre) )), 0, 0, 'C', TRUE);
	$yc += 6;
	$this->Ln($this->cell_padding); // Saut de ligne
	$yc += $this->cell_padding;
	$this->Rect($this->xx, $this->yy, $this->largeur_utile, $yc-($GLOBALS['association_metas']['fpdf_margint']?$GLOBALS['association_metas']['fpdf_margint']:10)); // Rectangle tout autour de l'entete
	$this->yy = $yc; // on sauve la position du curseur dans la page
    }

    // Fichier final envoye
    function File($titre='etat_comptes') {
	$this->Output($titre.'_'.$this->periode.'_'.$this->destination.'.pdf', 'I');
    }

    // on affiche les totaux (recettes et depenses) d'un exercice des differents comptes de la classe specifiee
    function association_liste_totaux_comptes_classes($classes, $prefixe='', $direction='-1', $periode=0, $destination=0) {
	if( !is_array($classes) ) { // a priori une chaine ou un entier d'une unique classe
	    $liste_classes = array( $classes ) ; // transformer en tableau (puisqu'on va operer sur des tableaux);
	} else { // c'est un tableau de plusieurs classes
	    $liste_classes = $classes;
	}
	// Les coordonnees courantes
	$xc = $this->xx+$this->cell_padding;
	$y_orig = $this->yy+$this->cell_padding;
	$yc = $y_orig+$this->cell_padding;
	// typo
	$this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), 'B', 14); // police: gras 14px
	$this->SetFillColor(235); // Couleursdu fond du cadre de titre : gris-92.2%
	$this->SetTextColor(0); // Couleurs du texte du cadre de titre
	// Titre centre
	$titre = $prefixe.'_'. ( ($direction) ? (($direction<0)?'depenses':'recettes') : 'soldes' );
	$this->SetXY($xc, $yc);
	$this->Cell($this->largeur_utile, 10, html_entity_decode(_T("asso:$titre")), 0, 0, 'C');
	$yc += 10;
	$this->Ln($this->cell_padding); // Saut de ligne
	$yc += $this->cell_padding;
	// initialisation du calcul+affichage des comptes
	$total_valeurs = $total_recettes = $total_depenses = 0;
	$chapitre = '';
	$i = 0;
	foreach ( $liste_classes as $rang => $classe ) { // calcul+affichage par classe
	    $query = association_calcul_soldes_comptes_classe($classe, $this->periode, $this->destination, $direction );
	    $this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), '', 12); // police : normal 12px
	    while ($data = sql_fetch($query)) {
		$this->SetXY($xc, $yc); // positionne le curseur
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) { // debut de categorie
		    $this->SetFillColor(225); // Couleur de fond de la ligne : gris-92.2%
		    $this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', TRUE);
		    $this->Cell(($this->largeur_utile)-(2*$this->cell_padding+20), 6, utf8_decode(($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))), 0, 0, 'L', TRUE);
		    $chapitre = $new_chapitre;
		    $this->Ln(); // Saut de ligne
		    $yc += 6;
		}
		$this->SetFillColor(245); // Couleur de fond du total : gris-96.1%
		$this->SetXY($xc, $yc); // positionne le curseur
#	    	if ( floatval($data['valeurs']) || floatval($data['recettes']) || floatval($data['depenses']) ) { // non-zero...
		    $this->Cell(20, 6, utf8_decode($data['code']), 0, 0, 'R', TRUE);
		    $this->Cell(($this->largeur_utile)-(2*$this->cell_padding+50), 6, utf8_decode($data['intitule']), 0, 0, 'L', TRUE);
		    $this->Cell(30, 6, association_formater_nombre($data['valeurs']), 0, 0, 'R', TRUE);
		    if ($direction) { // mode liste comptable
			$this->Cell(30, 6, association_formater_nombre($data['valeurs']), 0, 0, 'R', TRUE);
			$total_valeurs += $data['valeurs'];
		    } else { // mode liste standard
			$this->Cell(30, 6, association_formater_nombre($data['depenses']>0?$data['depenses']:$data['recettes']), 0, 0, 'R', TRUE);
			$total_recettes += $data['recettes'];
			$total_depenses += $data['depenses'];
			$total_valeurs += $data['soldes'];
		    }
		    $this->Ln(); // Saut de ligne
		    $yc += 6;
#	    	}
	    }
	}
	$this->SetXY($xc, $yc); // positionne le curseur
	$this->SetFillColor(215); // Couleur de fond : 84.3%
	if ($direction) { // mode liste comptable : charge, produit, actifs, passifs
	    $this->Cell(($this->largeur_utile)-(2*$this->cell_padding+30), 6, html_entity_decode(_T("asso:$prefixe".'_total')), 1, 0, 'R', TRUE);
	    $this->Cell(30, 6, association_formater_nombre($total_valeurs), 1, 0, 'R', TRUE);
	} else { // mode liste standard : contributions volontaires et autres
	    $this->Cell(($this->largeur_utile)/2-(2*$this->cell_padding+30), 6, html_entity_decode(_T("asso:$prefixe".'_total_depenses')), 1, 0, 'R', TRUE);
	    $this->Cell(30, 6, association_formater_nombre($total_depenses), 1, 0, 'R', TRUE);
	    $xc += ( $this->largeur_utile)/2;
	    $this->SetXY($xc, $yc); // positionne le curseur sur l'autre demi page
	    $this->Cell(($this->largeur_utile)/2-(2*$this->cell_padding+30), 6, html_entity_decode(_T("asso:$prefixe".'_total_recettes')), 1, 0, 'R', TRUE);
	    $this->Cell(30, 6, association_formater_nombre($total_recettes), 1, 0, 'R', TRUE);
	}
	$yc += 6;
	$this->Ln($this->cell_padding); // Saut de ligne
	$yc += $this->cell_padding;
	$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig); // Rectangle tout autour
	$this->yy = $yc; // on sauve la position du curseur dans la page
	return $total_valeurs;
    }

    // on affiche le resultat comptable net : benefice ou deficit
    function association_liste_resultat_net($lesRecettes, $lesDepenses) {
	// Les coordonnees courantes
	$xc = $this->xx+$this->cell_padding;
	$y_orig = $this->yy+$this->cell_padding;
	$yc = $y_orig+$this->cell_padding;
	// typo
	$this->SetFont(($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial'), 'B', 14); // police : gras 14px
	$this->SetFillColor(235); // Couleur du fond : gris-92.2%
	$this->SetTextColor(0); // Couleur du texte : noir
	// Titre centre
	$this->SetXY($xc, $yc);
	$this->Cell($this->largeur_utile, 10, html_entity_decode(_T('asso:cpte_resultat_titre_resultat')), 0, 0, 'C');
	$yc += 10;
	$this->Ln($this->cell_padding); // Saut de ligne
	$yc += $this->cell_padding;
	$this->SetFillColor(215); // Couleur de fond : gris-84.3%
	$leSolde = $lesRecettes-$lesDepenses;
	$this->SetXY($xc, $yc);
	$this->Cell(($this->largeur_utile)-(2*$this->cell_padding+30), 6, html_entity_decode(_T('asso:cpte_resultat_'.($leSolde<0?'perte':'benefice'))), 1, 0, 'R', TRUE);
	$this->Cell(30, 6, association_formater_nombre($leSolde), 1, 0, 'R', TRUE);
	$yc += 6;
	$this->Ln($this->cell_padding); // Saut de ligne
	$yc += $this->cell_padding;
	$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig); // Rectangle tout autour
	$this->yy = $yc; // on sauve la position du curseur dans la page
    }

} // fin classe

} // fin if

?>