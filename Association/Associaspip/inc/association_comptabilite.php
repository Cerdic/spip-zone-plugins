<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

// recupere dans la table de comptes et celle des destinations la liste des destinations associees a une operation
// le parametre correspond a l'id_compte de l'operation dans spip_asso_compte (et spip_asso_destination)
function association_liste_destinations_associees($id_compte)
{
    if (!$id_compte)
	return '';
    if ($destination_query = sql_select('spip_asso_destination_op.id_destination, spip_asso_destination_op.recette, spip_asso_destination_op.depense, spip_asso_destination.intitule', 'spip_asso_destination_op RIGHT JOIN spip_asso_destination ON spip_asso_destination.id_destination=spip_asso_destination_op.id_destination', "id_compte=$id_compte", '', 'spip_asso_destination.intitule')) {
	$destination = array();
	while ($destination_op = sql_fetch($destination_query))	{
	    /* soit recette soit depense est egal a 0, donc pour l'affichage du montant on se contente les additionner */
	    $destination[$destination_op[id_destination]] = $destination_op[recette]+$destination_op[depense];
	}
	if (count($destination)==0)
	    $destination = '';
    } else {
	$destination = '';
    }
    return $destination;
}

// retourne une liste d'option HTML de l'ensemble des destinations de la base, ordonee par intitule
function association_toutes_destination_option_list()
{
    $liste_destination = '';
    $sql = sql_select('id_destination,intitule', 'spip_asso_destination', '', '', 'intitule');
    while ($destination_info = sql_fetch($sql)) {
	$liste_destination .= '<option value="'. $destination_info['id_destination'] .'">'.$destination_info['intitule'].'</option>';
    }
    return $liste_destination;
}

// retourne dans un <div> le code HTML/javascript correspondant au selecteur de destinations dynamique
// le premier parametre permet de donner un tableau de destinations deja selectionnees(ou '' si on ajoute une operation)
// le second parametre (optionnel) permet de specifier si on veut associer une destination unique, par default on peut ventiler sur
// plusieurs destinations
// le troisieme parametre permet de regler une destination par defaut[contient l'id de la destination] - quand $destination est vide
function association_editeur_destinations($destination, $unique='', $defaut='')
{
    // recupere la liste de toutes les destination dans un code HTML <option value="destination_id">destination</option>
    $liste_destination = association_toutes_destination_option_list();
    $res = '';
    if ($liste_destination) {
	$res = '<script type="text/javascript" src="'.find_in_path('javascript/jquery.destinations_form.js').'"></script>';
	$res .= '<label for="destination">'
	    . _T('asso:destination') .'</label>'
	    . '<div id="divTxtDestination" class="formulaire_edition_destinations">';
	$idIndex = 1;
//	spip_log("liste de destinations : \n".print_r($destination,true)."\n---------",'associaspip');
	if ($destination!='') { /* si on a une liste de destinations (on edite une operation) */
	    foreach ($destination as $destId => $destMontant) {
		$liste_destination_selected = preg_replace('/(value="'.$destId.'")/', '$1 selected="selected"', $liste_destination);
		$res .= '<div class="formo" id="row'.$idIndex.'"><ul>';
		$res .= '<li class="editer_id_dest['.$idIndex.']">'
		    . '<select name="id_dest['.$idIndex.']" id="id_dest['.$idIndex.']" >'
		    . $liste_destination_selected
		    . '</select></li>';
		if ($unique==false) {
		    $res .= '<li class="editer_montant_dest['.$idIndex.']"><input name="montant_dest['.$idIndex.']" value="'
			. association_nbrefr(association_recupere_montant($destMontant))
			. '" type="text" id="montant_dest['.$idIndex.']" /></li>'
			. '<button class="destButton" type="button" onClick="addFormField(); return false;">+</button>';
		    if ($idIndex>1) {
			$res .= '<button class="destButton" type="button" onClick="removeFormField(\'#row'.$idIndex.'\'); return false;">-</button>';
		    }
		}
		$res .= '<ul></div>';
		$idIndex++;
	    }
	} else {/* pas de destination deja definies pour cette operation */
	    if ($defaut!='') {
		$liste_destination = preg_replace('/(value="'.$defaut.'")/', '$1 selected="selected"', $liste_destination);
	    }
	    $res .= '<div id="row1" class="formo"><ul><li class="editer_id_dest[1]"><select name="id_dest[1]" id="id_dest[1]" >'
		. $liste_destination . '</select></li>';
	    if (!$unique) {
		$res .= '<li class="editer_montant_dest[1]"><input name="montant_dest[1]" value="'
		    .'" type="text" id="montant_dest[1]"/></li>'
		    . '</ul><button class="destButton" type="button" onClick="addFormField(); return false;">+</button>';
	    }
	    $res .= '</div>';
	}
	if ($unique==false)
	    $res .= '<input type="hidden" id="idNextDestination" value="'.($idIndex+1).'">';
	$res .= '</div>';
    }
    return $res;
}

/* Ajouter une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op */
function association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal)
{
    include_spip('base/association');
    /* on passe par modifier_contenu pour que la modification soit envoyee aux plugins et que Champs Extras 2 la recupere */
    include_spip('inc/modifier');
    $id_compte = sql_insertq('spip_asso_comptes', array(
	'date' => $date,
	'imputation' => $imputation,
	'recette' => $recette,
	'depense' => $depense,
	'journal' => $journal,
	'id_journal' => $id_journal,
	'justification' => $justification
    ));
    modifier_contenu('asso_compte', $id_compte, '', array());
    // on laisse passer ce qui est peut-etre une erreur,
    // pour ceux qui ne definisse pas de plan comptable.
    // Mais ce serait bien d'envoyer un message d'erreur au navigateur
    // plutot que de le signaler seulement dans les log
    if (!$imputation) {
	spip_log("imputation manquante : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, journal=$journal, id_journal=$id_journal, justification=$justification",'associaspip');
    }
    /* Si on doit gerer les destinations */
    if ($GLOBALS['association_metas']['destinations']=='on') {
	association_ajouter_destinations_comptables($id_compte, $recette, $depense);
    }
    return $id_compte;

}

/* modifier une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op */
function association_modifier_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal, $id_compte)
{
    include_spip('base/association');
    if ( sql_countsel('spip_asso_comptes', "id_compte=$id_compte AND vu ") ) { // il ne faut pas modifier une operation verouillee !!!
	spip_log("modification d'operation comptable : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal, justification=$justification",'associaspip');
	return $err = _T('asso:operation_non_modifiable');
    }
    /* Si on doit gerer les destinations */
    if ($GLOBALS['association_metas']['destinations']=='on') {
	$err = association_ajouter_destinations_comptables($id_compte, $recette, $depense);
    }
    /* on passe par modifier_contenu (et non sql_updateq) pour que la modification soit envoyee aux plugins et que Champs Extras 2 la recupere */
    include_spip('inc/modifier');
    // tester $id_journal, si il est null, ne pas le modifier afin de ne pas endommager l'entree dans la base en editant directement depuis le libre de comptes
    if ($id_journal) {
	modifier_contenu('asso_compte', $id_compte, '', array(
	    'date' => $date,
	    'imputation' => $imputation,
	    'recette' => $recette,
	    'depense' => $depense,
	    'journal' => $journal,
	    'id_journal' => $id_journal,
	    'justification' => $justification)//,
	);
    } else {
	modifier_contenu('asso_compte', $id_compte, '', array(
	    'date' => $date,
	    'imputation' => $imputation,
	    'recette' => $recette,
	    'depense' => $depense,
	    'journal' => $journal,
	    'justification' => $justification)//,
	);
    }
    return $err;
}

/* Supprimer une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op ; cas 1 : usage direct de id_compte */
function association_supprimer_operation_comptable1($id_compte, $securite=FALSE)
{
    include_spip('base/association');
    /* recuperer les informations sur l'operation pour le fichier de log */
    list($date, $recette, $depense, $imputation, $journal, $id_journal, $verrou) = sql_fetsel('date, recette, depense, imputation, journal, id_journal, vu', 'spip_asso_comptes', "id_compte=$id_compte");
    if ( ($securite AND !$verrou) || !$securite ) { // operation non verouillee ou controle explicitement desactive...
	/* on efface de la table destination_op toutes les entrees correspondant a cette operation  si on en trouve */
	sql_delete('spip_asso_destination_op', "id_compte=$id_compte");
	/* on logue quand meme */
	spip_log("suppression d'operation comptable : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal, justification=...",'associaspip');
    } else { // on ne supprime pas les ecritures validees/verouillees ; il faut annuler l'operation par une operation comptable inverse...
	/*on cree l'operation opposee a celle a annuler ; mais ce n'est pas une annulation correcte au regard des numeros de comptes (imputation/journal)... */
	$annulation = sql_insertq('spip_asso_comptes', array(
	    'date' => date('Y-m-d'),
	    'depense' => $recette,
	    'recette' => $depense,
	    'imputation' => _T('asso:compte_annulation_operation', array('numero'=>$id_compte,'date'=>$date) ),
	    'imputation' => $imputation, // pas forcement vrai, mais on fait au plus simples...
	    'journal' => $journal, // pas forcement vrai, mais on fait au plus simples...
	    'id_journal' => -$id_journal, // on garde la trace par rapport au module ayant cree l'operation
	    'vu' => 1, // cette operation n'est pas moifiable non plus...
	) );
	/* on logue quand meme */
	spip_log("annulation d'operation comptable : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal, justification=annule_par_op$annulation",'associaspip');
    }
    /* on efface enfin de la table comptes l'entree correspondant a cette operation */
    sql_delete('spip_asso_comptes', "id_compte=$id_compte");
}

/* Supprimer une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op ; cas 2 : usage par les modules du couple imputation&id_journal */
function association_supprimer_operation_comptable2($id_journal,$imputation)
{
    /* old-way: avant, on pouvait ne pas avoir d'imputation... du coup on prend le premier id_journal correspondant a n'importe quelle imputation!!! (avec cette methode il n'est pas surprenant de perdre des enregistrements...) */
#    $association_imputation = charger_fonction('association_imputation', 'inc');
#    $critere = (($critere_imputation = $association_imputation($pc_imputation))?' AND ':'') ."id_journal='$id_journal'";
    /* new-way: maintenant on exige l'imputation ; et s'il n'y en a pas on prend le premier id_journal sans imputation ! c'est deja beaucoup moins problematique... */
    $critere = "imputation='$imputation' AND id_journal='$id_journal'";
    $id_compte = sql_getfetsel('id_compte', 'spip_asso_comptes', $critere);
    association_supprimer_operation_comptable1($id_compte);
    return $id_compte; // indique quelle operation a ete supprimee (0 si aucune --donc erreur dans les parametres ?)
}

/* Supprimer en masse des operations dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op */
function association_supprimer_operations_comptables($critere)
{
    include_spip('base/association');
    /* on recupere les id_comptes a supprimer */
    $where = sql_in_select('id_compte', 'id_compte', 'spip_asso_comptes', $critere);
    /* on efface de la table destination_op toutes les entrees correspondant a ces operations  si on en trouve */
    sql_delete('spip_asso_destination_op', $where);
    /* on logue quand meme */
    $query_log = sql_select('id_compte, date, recette, depense, imputation, journal, id_journal', 'spip_asso_comptes', $where);
    while ( list($id_compte, $date, $recette, $depense, $imputation, $journal, $id_journal) = fetch($query_log) ) {
	spip_log("suppression d'operation comptable : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal ",'associaspip');
    }
    /* on efface enfin de la table comptes les entrees correspondant a ces operations */
    sql_delete('spip_asso_comptes', $where); // $where ou $critere
}

/* fonction de verification des montants de destinations entres */
/* le parametre d'entree est le montant total attendu, les montants des destinations sont recuperes */
/* directement dans $_POST */
function association_verifier_montant_destinations($montant_attendu)
{
    $err = '';
    $toutesDestinations = _request('id_dest');
    $toutesDestinationsMontants = _request('montant_dest');
    /* on verifie que le montant des destinations correspond au montant global et qu'il n'y a pas deux fois la meme destination (uniquement si on a plusieurs destinations) */
    $total_destination = 0;
    $id_inserted = array();
    if (count($toutesDestinations)>1) {
	foreach ($toutesDestinations as $id => $id_destination) {
	    /* on verifie qu'on n'a pas deja insere une destination avec cette id */
	    if (!array_key_exists($id_destination,$id_inserted)) {
		$id_inserted[$id_destination] = 0;
	    } else {
		$err = _T('asso:erreur_destination_dupliquee');
	    }
	    $total_destination += association_recupere_montant($toutesDestinationsMontants[$id]); /* les montants sont dans un autre tableau aux meme cles */
	}
	/* on verifie que la somme des montants des destinations correspond au montant attendu */
	if ($montant_attendu!=$total_destination) {
	    $err .= _T('asso:erreur_montant_destination');
	}
    } else { /* une seule destination, le montant peut ne pas avoir ete precise, dans ce cas pas de verif, c'est le montant attendu qui sera entre dans la base */
	/* quand on a une seule destination, l'id dans les tableaux est forcement 1 par contruction de l'editeur */
	if ($toutesDestinationsMontants[1]) {
	    $montant = association_recupere_montant($toutesDestinationsMontants[1]);
	    /* on verifie que le montant indique correspond au montant attendu */
	    if ($montant_attendu!=$montant) {
		$err = _T('asso:erreur_montant_destination');
	    }
	}
    }
    return $err;
}

/* fonction permettant d'ajouter/modifier les destinations comptables (presente dans $_POST) a une operation comptable */
function association_ajouter_destinations_comptables($id_compte, $recette, $depense)
{
    include_spip('base/association');
    /* on efface de la table destination_op toutes les entrees correspondant a cette operation  si on en trouve*/
    sql_delete('spip_asso_destination_op', "id_compte=$id_compte");
//    spip_log("DEL spip_asso_destination_op.id_compte=$id_compte",'associaspip');
    if ($recette>0) {
	$attribution_montant = 'recette';
    } else {
	$attribution_montant = 'depense';
    }
    $toutesDestinations = _request('id_dest');
    $toutesDestinationsMontants = _request('montant_dest');
//    spip_log("id_dest : \n".print_r($toutesDestinations, true), 'associaspip');
//    spip_log("id_dest : \n".print_r($toutesDestinationsMontants, true), 'associaspip');
    if (count($toutesDestinations)>1) {
	foreach ($toutesDestinations as $id => $id_destination)	{
	    $montant = association_recupere_montant($toutesDestinationsMontants[$id]);	/* le tableau des montants a des cles indentique a celui des id */
	    $id_dest_op = sql_insertq('spip_asso_destination_op', array(
		'id_compte' => $id_compte,
		'id_destination' => $id_destination,
		$attribution_montant => $montant
	    ));
//	    spip_log("spip_asso_destination_op(id_dest_op,id_compte,id_destination,montant,attribution)=($id_dest_op,$id_compte,$id_destination,$montant,$attribution_montant)",'associaspip');
	}
    } else { /* une seule destination, le montant peut ne pas avoir ete precise, on entre directement le total recette+depense */
	$id_dest_op = sql_insertq('spip_asso_destination_op', array(
	    'id_compte' => $id_compte,
	    'id_destination' => $toutesDestinations[1],
	    $attribution_montant => $depense+$recette
	));
//	spip_log("spip_asso_destination_op(id_dest_op,id_compte,id_destination,recette,depense,attribution)=($id_dest_op,$id_compte,1,$recette,$depense,$attribution_montant)",'associaspip');
    }
}

function inc_association_imputation_dist($nom, $table='')
{
    $champ = ($table ? ($table . '.') : '') . 'imputation';
    return $champ . '=' . sql_quote($GLOBALS['association_metas'][$nom]);
}

/* valide le plan comptable: on doit avoir au moins deux classes de comptes differentes */
/* le code du compte doit etre unique */
/* le code du compte doit commencer par un chiffre egal a sa classe */
function association_valider_plan_comptable()
{
    $classes = array();
    $codes = array();
    /* recupere le code et la classe de tous les comptes du plan comptable */
    $query = sql_select('code, classe', 'spip_asso_plan');
    while ($data = sql_fetch($query)) {
	$classe = $data['classe'];
	$code = $data['code'];
	$classes[$classe] = 0; /* on comptes les classes differentes */
	if(array_key_exists($code, $codes)) {
	    return false; /* on a deux fois le meme code */
	} else {
	    $codes[$code] = 0;
	}
	/* on verifie que le code est bien de la forme chiffre-chiffre-caracteres alphanumeriques et que le premier digit correspond a la classe */
	if ((!preg_match("/^[0-9]{2}\w*$/", $code)) || ($code[0]!=$classe))
	    return false;
    }
    if (count($classes)<2)
	return false; /* on doit avoir au moins deux classes differentes */
    return true;
}

/* retourne un tableau $code => $intitule trie sur $code et de classe $val */
function association_liste_plan_comptable($val,$actives='') {
    $res = array();
    /* recupere le code et l'intitule de tous les comptes de classe $val */
    $query = sql_select('code, intitule', 'spip_asso_plan', "classe='$val'".($actives?" AND active=$actives":''), '', 'code');
    while ($data = sql_fetch($query)) {
	$code = $data['code'];
	$intitule = $data['intitule'];
	$res[$code] = $intitule;
    }
    return $res;
}

/* si il existe un compte 58x on le retourne sinon on cree le compte 581 et on le retourne */
function association_creer_compte_virement_interne() {
    if ($GLOBALS['association_metas']['pc_intravirements']) // un code de virement interne est deja defini !
	return $GLOBALS['association_metas']['pc_intravirements'];
    $res = association_liste_plan_comptable($GLOBALS['association_metas']['classe_banques']); // on recupere tous les comptes de la classe "financier" (classe 5)
    foreach($res as $code => $libelle) {
	/* existe-t-il le compte 58x ? (nota : c'est la compta francaise...) */
	if (substr($code,1,1)=='8') // il existe un code qui commence par 58...
	    return $code;
    }
    /* j'ai rien trouve, je cree le compte 581 */
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
    return $code;
}

/* on recupere les parametres de requete a passer aux fonctions */
function association_passe_parametres_comptables($classes=array()) {
    $params = array(); // initialisation de la liste
    $params['exercice'] = intval(_request('exercice'));
    if( !$params['exercice'] ) { // pas de "id_exercice" en parametre
	$params['exercice'] = intval(sql_getfetsel('id_exercice', 'spip_asso_exercices', '', '', 'fin DESC')); // on recupere l'id_exercice dont la "date de fin" est "la plus grande", c'est a dire l'id de l'exercice le plus recent
    }
    $params['annee'] = intval(_request('annee'));
    if( !$params['annee'] ) { // pas d'annee en parametre
	$params['annee'] = date('Y'); // on prende l'annee actuelle
    }
    $params['destination'] = intval(_request('destination'));
#    if( !$params['destination'] ) { // pas de destination
#    }
    $params['classes'] = $classes;
    $params['url'] = serialize($params); //!\ les cles numeriques peuvent poser probleme... <http://www.mail-archive.com/php-bugs@lists.php.net/msg100262.html> mais il semble qu'ici le souci vient de l'absence d'encodage lorsqu'on passe $var par URL...
    return $params;
}

/* on recupere les totaux (recettes et depenses) d'un exercice des differents comptes de la classe specifiee */
function association_calcul_totaux_comptes_classe($classe, $exercice=0, $destination=0, $direction='-1') {
    $c_group = (($classe==$GLOBALS['association_metas']['classe_banques'])?'journal':'imputation');
    $valeurs = (($direction) ? ( ($direction<0)?'SUM('.(($destination)?'a_d':'a_c').'.depense) AS valeurs' : 'SUM('.(($destination)?'a_d':'a_c').'.recette) AS valeurs') : 'SUM('.(($destination)?'a_d':'a_c').'.recette) AS recettes, SUM('.(($destination)?'a_d':'a_c').'.depense) as depenses, SUM('.(($destination)?'a_d':'a_c').'.recette-'.(($destination)?'a_d':'a_c').'.depense) AS soldes' );
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
    if ( $exercice ) { // exercice budgetaire personnalise
	$exercice_data = sql_asso1ligne('exercice', $exercice);
	$c_where = "a_c.date>='$exercice_data[debut]' AND a_c.date<='$exercice_data[fin]' ";
    } elseif ( $annee ) { // exercice budgetaire par annee civile
	$c_where = "DATE_FORMAT(a_c.date, '%Y')=$annee ";
#    } elseif ( $classe==$GLOBALS['association_metas']['classe_banques'] ) { // encaisse
#	$c_where = 'LEFT(a_c.imputation,1)<>'. sql_quote($GLOBALS['association_metas']['classe_contributions_volontaires']) .' AND a_c.date>=a_p.date_anterieure AND a_c.date<=NOW() ';
    } else { // tout depuis le debut ?!?
	$c_where = 'a_c.date<=NOW()'; // il faut mettre un test valide car la chaine peut etre precedee de "AND "...  limiter alors a aujourd'hui ?
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

/* on affiche les totaux (recettes et depenses) d'un exercice des differents comptes de la classe specifiee */
function association_liste_totaux_comptes_classes($classes, $prefixe='', $direction='-1', $exercice=0, $destination=0) {
    if( !is_array($classes) ) { // a priori une chaine ou un entier d'une unique classe
	$liste_classes = array( $classes ) ; // transformer en tableau (puisqu'on va operer sur des tableaux);
    } else { // c'est un tableau de plusieurs classes
	$liste_classes = $classes;
    }
    $titre = $prefixe.'_'. ( ($direction) ? (($direction<0)?'depenses':'recettes') : 'soldes' );
    echo "<table width='100%' class='asso_tablo' id='asso_tablo_$titre'>\n";
    echo "<thead>\n<tr>";
    echo '<th width="10">&nbsp;</td>';
    echo '<th width="30">&nbsp;</td>';
    echo '<th>'. _T("asso:$titre") .'</th>';
    if ($direction) { // mode liste comptable : charge, produit, actifs, passifs
	echo '<th width="80">&nbsp;</th>';
    } else { // mode liste standard : contributions volontaires et autres
	echo '<th width="80">'. _T("asso:$prefixe".'_recettes') .'</th>';
	echo '<th width="80">'. _T("asso:$prefixe".'_depenses') .'</th>';
	// echo '<th width="80">'. _T("asso:$prefixe".'_solde') .'</th>';
    }
    echo "</tr>\n</thead><tbody>";
    $total_valeurs = $total_recettes = $total_depenses = 0;
    $chapitre = '';
    $i = 0;
    foreach ( $liste_classes as $rang => $classe ) {
	$query = association_calcul_totaux_comptes_classe($classe, $exercice, $destination, $direction );
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
		    echo '<td class="decimal">'. association_nbrefr($data['valeurs']) .'</td>';
		    $total_valeurs += $data['valeurs'];
		} else { // mode liste standard
		    echo '<td class="decimal">'. association_nbrefr($data['recettes']) .'</td>';
		    $total_recettes += $data['recettes'];
		    echo '<td class="decimal">'. association_nbrefr($data['depenses']) .'</td>';
		    $total_depenses += $data['depenses'];
		    //echo '<td class="decimal">'. association_nbrefr($data['soldes']) .'</td>';
		    $total_valeurs += $data['soldes'];
		}
		echo "</tr>\n";
#	    }
	}
    }
    echo "</tbody><tfoot>\n<tr>";
    echo '<th colspan="2">&nbsp;</th>';
    echo '<th class="text">'. _T("asso:$prefixe".'_total') .'</th>';
    if ($direction) { // mode liste comptable
	echo '<th class="decimal">'. association_nbrefr($total_valeurs) . '</th>';
    } else { // mode liste standard
	echo '<th class="decimal">'. association_nbrefr($total_recettes) . '</th>';
	echo '<th class="decimal">'. association_nbrefr($total_depenses) . '</th>';
	// echo '<th class="decimal">'. association_nbrefr($total_valeurs) . '</th>';
    }
    echo "</tr>\n</tfoot>\n</table>\n";
    return $total_valeurs;
}

/* on affiche la difference entre les recettes et les depenses (passees en parametre) pour les classes d'un exercice */
function association_liste_resultat_net($recettes, $depenses) {
    echo "<table width='100%' class='asso_tablo' id='asso_tablo_bilan_solde'>\n";
    echo "<thead>\n<tr>";
    echo '<th width="10">&nbsp;</td>';
    echo '<th width="30">&nbsp;</td>';
    echo '<th>'. _T('asso:cpte_resultat_titre_resultat') .'</th>';
    echo '<th width="80">&nbsp;</th>';
    echo "</tr>\n</thead>";
    echo "<tfoot>\n<tr>";
    echo '<th colspan="2">&nbsp;</th>';
    $res = $recettes-$depenses;
    echo '<th class="text">'. (($res<0) ? _T('asso:cpte_resultat_perte') : _T('asso:cpte_resultat_benefice')) .'</th>';
    echo '<th class="decimal">'. association_nbrefr(abs($res)) .'</th>';
    echo "</tr></tfoot></table>";
}

// Brique commune aux classes d'exportation des etats comptables
class ExportComptes {

    var $exercice;
    var $destination;
    var $annee;
    var $classes;
    var $out;

    function  __construct($var) {
	$tableau = unserialize(rawurldecode($var));
	$this->exercice = $tableau['exercice'];
	$this->destination = $tableau['estination'];
	$this->annee = $tableau['annee'];
	$this->classes = $tableau['classes'];
	$this->out = '';
    }

    // de type CSV,INI,TSV, etc.
    function LignesSimplesEntete($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='') {
	$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_code')))) .$champFin.$champsSeparateur;
	$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_intitule')))) .$champFin.$champsSeparateur;
	$this->out .= $champDebut. str_replace(array_keys($echappements), array_values($echappements), utf8_decode(html_entity_decode(_T('asso:entete_montant')))) .$champFin.$lignesSeparateur;
    }

    // de type CSV,INI,TSV, etc.
    function LignesSimplesCorps($key, $champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='') {
	switch ($key) {
	    case 'charges' :
		$dir = -1;
		break;
	    case 'produits' :
		$dir = +1;
		break;
	    case 'contributions_volontaires' :
		$dir = 0;
		break;
	}
	$query = association_calcul_totaux_comptes_classe($GLOBALS['association_metas']['classe_'.$key], $this->exercice, $this->destination, $dir);
	$chapitre = '';
	$i = 0;
	while ($data = sql_fetch($query)) {
	    if ($key==='contributions_volontaires') {
		if ($data['depenses']>0) {
		    $valeurs = $data['depenses'];
		} else {
		    $valeurs = $data['recettes'];
		}
	    } else {
		$valeurs = $data['valeurs'];
	    }
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
	    $this->out .= $champDebut.$valeurs.$champFin.$lignesSeparateur;
	}
    }

    // export texte de type tableau (lignes*colonnes) simple : CSV,CTX,HTML*SPIP,INI*,TSV,etc.
    // de par la simplicite recherchee il n'y a pas de types ou autres : CSV et CTX dans une certaine mesure pouvant distinguer "nombres", "chaines alphanumeriques" et "chaine binaires encodees"
    function exportLignesUniques($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='', $entete=true) {
	if ($entete) {
	    $this->LignesSimplesEntete($champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='');
	}
	foreach (array('charges', 'produits', 'contributions_volontaires') as $nomClasse) {
	    $this->LignesSimplesCorps($nomClasse, $champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='');
	}
    }

    // export texte de type s-expression / properties-list / balisage (conteneurs*conteneurs*donnees) simple : JSON, XML (utilisable avec ASN.1), YAML, etc.
    // de par la simplicite recherchee il n'y a pas de types ou d'attributs : BSON, Bencode, JSON, pList, XML, etc.
    function exportLignesMultiples($balises, $echappements=array(), $champDebut='', $champFin='', $indent="\t", $entetesPerso='') {
	$this->out .= "$balises[compteresultat1]\n";
	if (!$entetesPerso) {
	    $this->out .= "$indent$balises[entete1]\n";
	    $this->out .= "$indent$indent$balises[titre1] $champDebut". utf8_decode(html_entity_decode(_T('asso:cpte_resultat_titre_general'))) ."$champFin $balises[titre0]\n";
	    $this->out .= "$indent$indent$balises[nom1] $champDebut". $GLOBALS['association_metas']['nom'] ."$champFin $balises[nom0]\n";
	    $this->out .= "$indent$indent$balises[exercice1] $champDebut". sql_asso1champ('exercice', $this->exercice, 'intitule') ."$champFin $balises[exercice0]\n";
	    $this->out .= "$indent$balises[entete0]\n";
	}
	foreach (array('charges', 'produits', 'contributions_volontaires') as $nomClasse) {
	    switch ($nomClasse) {
		case 'charges' :
		    $dir = '-1';
		    break;
		case 'produits' :
		    $dir = '+1';
		    break;
		case 'contributions_volontaires' :
		    $dir = 0;
		    break;
	    }
	    $baliseClasse = $nomClasse.'1';
	    $this->out .= "$indent$balises[$baliseClasse]\n";
	    $query = association_calcul_totaux_comptes_classe($GLOBALS['association_metas']['classe_'.$nomClasse], $this->exercice, $this->destination, $dir);
	    $chapitre = '';
	    $i = 0;
	    while ($data = sql_fetch($query)) {
		if ($key==='contributions_volontaires') {
		    if ($data['depenses']>0) {
			$valeurs = $data['depenses'];
		    } else {
			$valeurs = $data['recettes'];
		    }
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
		$this->out .= "$indent$indent$indent$indent$balises[montant1] $champDebut".$valeurs."$champFin $balises[montant0]\n";
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

    // fichier texte final a afficher/telecharger
    function leFichier($ext, $subtype) {
	$fichier = _DIR_RACINE.'/'._NOM_TEMPORAIRES_ACCESSIBLES.'compte_'.$subtype.'_'.$this->exercice.'_'.$this->destination.".$ext"; // on essaye de creer le fichier dans le cache local/ http://www.spip.net/fr_article4637.html
	$f = fopen($fichier, 'w');
	fputs($f, $this->out);
		fclose($f);
	header('Content-type: application/'.$ext);
	header('Content-Disposition: attachment; filename="'.$fichier.'"');
	readfile($fichier);
    }

}


?>