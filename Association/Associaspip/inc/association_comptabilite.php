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
    if ($GLOBALS['association_metas']['pc_intravirements'])
	return $GLOBALS['association_metas']['pc_intravirements'];
    /* on recupere tous les comptes de la classe "financier" (classe 5) */
    $res = association_liste_plan_comptable($GLOBALS['association_metas']['classe_banques']);
    /* existe-t-il le compte 58x */
    foreach($res as $code => $libelle) {
	if (substr($code,1,1)=='8') {
	    $trouve = TRUE; /* j'ai trouve un code qui commence par 58 */
	    return $code;
	}
    }
    if (!$trouve) { /* j'ai rien trouve, je cree le compte 581 */
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
    }
    return $code;
}

?>