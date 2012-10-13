<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// recupere dans la table de comptes et celle des destinations la liste des destinations associees a une operation
// le parametre correspond a l'id_compte de l'operation dans spip_asso_compte (et spip_asso_destination)
function association_liste_destinations_associees($id_compte) {
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
function association_toutes_destination_option_list() {
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
function association_editeur_destinations($destination, $unique='', $defaut='') {
    // recupere la liste de toutes les destination dans un code HTML <option value="destination_id">destination</option>
    $liste_destination = association_toutes_destination_option_list();
    $res = '';
    if ($liste_destination) {
	$res = '<script type="text/javascript" src="'.find_in_path('javascript/jquery.destinations_form.js').'"></script>';
	$res .= '<label for="destination">'
	    . _T('asso:destination') .'</label>'
	    . '<div id="divTxtDestination" class="formulaire_edition_destinations">';
	$idIndex = 1;
//	spip_log("liste de destinations : \n".print_r($destination,TRUE)."\n---------",'associaspip');
	if ($destination!='') { /* si on a une liste de destinations (on edite une operation) */
	    foreach ($destination as $destId => $destMontant) {
		$liste_destination_selected = preg_replace('/(value="'.$destId.'")/', '$1 selected="selected"', $liste_destination);
		$res .= '<div class="formo" id="row'.$idIndex.'"><ul>';
		$res .= '<li class="editer_id_dest['.$idIndex.']">'
		    . '<select name="id_dest['.$idIndex.']" id="id_dest['.$idIndex.']" >'
		    . $liste_destination_selected
		    . '</select></li>';
		if ($unique==FALSE) {
		    $res .= '<li class="editer_montant_dest['.$idIndex.']"><input name="montant_dest['.$idIndex.']" value="'
			. association_formater_nombre($destMontant)
			. '" type="text" id="montant_dest['.$idIndex.']" /></li>'
			. '<button class="destButton" type="button" onClick="addFormField(); return FALSE;">+</button>';
		    if ($idIndex>1) {
			$res .= '<button class="destButton" type="button" onClick="removeFormField(\'#row'.$idIndex.'\'); return FALSE;">-</button>';
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
		    . '</ul><button class="destButton" type="button" onClick="addFormField(); return FALSE;">+</button>';
	    }
	    $res .= '</div>';
	}
	if ($unique==FALSE)
	    $res .= '<input type="hidden" id="idNextDestination" value="'.($idIndex+1).'">';
	$res .= '</div>';
    }
    return $res;
}

/* Ajouter une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op */
function association_ajouter_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal) {
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
    if (!$imputation) { // On laisse passer ce qui est peut-etre une erreur, pour ceux qui ne definisse pas de plan comptable. Mais ce serait bien d'envoyer un message d'erreur au navigateur plutot que de le signaler seulement dans les log
	spip_log("imputation manquante : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, journal=$journal, id_journal=$id_journal, justification=$justification",'associaspip');
    }
    if ($GLOBALS['association_metas']['destinations']=='on') { // Si on doit gerer les destinations
	association_ajouter_destinations_comptables($id_compte, $recette, $depense);
    }
    return $id_compte;

}

/* modifier une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op */
function association_modifier_operation_comptable($date, $recette, $depense, $justification, $imputation, $journal, $id_journal, $id_compte) {
    $err = '';
    include_spip('base/association');
    if ( sql_countsel('spip_asso_comptes', "id_compte=$id_compte AND vu ") ) { // il ne faut pas modifier une operation verouillee !!!
	spip_log("modification d'operation comptable : id_compte=$id_compte, date=$date, recette=$recette, depense=$depense, imputation=$imputation, journal=$journal, id_journal=$id_journal, justification=$justification",'associaspip');
	return $err = _T('asso:operation_non_modifiable');
    }
    if ($GLOBALS['association_metas']['destinations']=='on') { // Si on doit gerer les destinations
	$err = association_ajouter_destinations_comptables($id_compte, $recette, $depense);
    }
    $modifs = array(
	'date' => $date,
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
    modifier_contenu(
	'asso_compte',
	$id_compte,
	'',
	$modifs
    );
    return $err;
}

/* Supprimer une operation dans spip_asso_comptes ainsi que si necessaire dans spip_asso_destination_op ; cas 1 : usage direct de id_compte */
function association_supprimer_operation_comptable1($id_compte, $securite=FALSE) {
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
function association_supprimer_operation_comptable2($id_journal,$imputation) {
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
function association_supprimer_operations_comptables($critere) {
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

/* fonction permettant d'ajouter/modifier les destinations comptables (presente dans $_POST) a une operation comptable */
function association_ajouter_destinations_comptables($id_compte, $recette, $depense) {
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
//    spip_log("id_dest : \n".print_r($toutesDestinations, TRUE), 'associaspip');
//    spip_log("id_dest : \n".print_r($toutesDestinationsMontants, TRUE), 'associaspip');
    if (count($toutesDestinations)>1) {
	foreach ($toutesDestinations as $id => $id_destination)	{
	    $montant = association_recuperer_montant($toutesDestinationsMontants[$id], FALSE);	// le tableau des montants a des cles indentique a celui des id
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

function inc_association_imputation_dist($nom, $table='') {
    $champ = ($table ? ($table . '.') : '') . 'imputation';
    return $champ . '=' . sql_quote($GLOBALS['association_metas'][$nom]);
}

/* valide le plan comptable: on doit avoir au moins deux classes de comptes differentes */
/* le code du compte doit etre unique */
/* le code du compte doit commencer par un chiffre egal a sa classe */
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
	return FALSE; /* on doit avoir au moins deux classes differentes */
    return TRUE;
}

/* retourne un tableau $code => $intitule trie sur $code et de classe $val */
function association_liste_plan_comptable($val,$actives='') {
    $res = array();
    $query = sql_select('code, intitule', 'spip_asso_plan', "classe='$val'".($actives?" AND active=$actives":''), '', 'code'); // recupere le code et l'intitule de tous les comptes de classe $val
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
    return $code;
}

/* on recupere les parametres de requete a passer aux fonctions */
function association_passe_parametres_comptables($classes=array()) {
    $params = array(); // initialisation de la liste
    $params['exercice'] = association_passeparam_exercice();
    $params['annee'] = association_passeparam_annee();
    $params['destination'] = association_recuperer_entier('destination');
#    if( !$params['destination'] ) { // pas de destination
#    }
    $params['type'] = _request('type');
    if ( !$classes ) { // pas en parametre, on prend dans la requete
//	$params['classes'] = array_flip( explode(',', _request('classes')) );
	$keys = explode(',', _request('classes'));
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

/* on recupere les soldes des differents comptes de la classe specifiee pour l'exercice specifie
 * d'apres http://www.lacompta.ch/MITIC/theorie.php?ID=26 c'est le solde qui est recherche, et il corresponde bien a :
 *  recettes-depenses=recettes pour les classes 6
 *  depenses-recettes=depenses pour les classes 7
 * */
function association_calcul_soldes_comptes_classe($classe, $exercice=0, $destination=0, $direction='-1') {
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
	$query = association_calcul_soldes_comptes_classe($classe, $exercice, $destination, $direction );
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
    echo "</tbody><tfoot>\n<tr>";
    echo '<th colspan="2">&nbsp;</th>';
    echo '<th class="text">'. _T("asso:$prefixe".'_total') .'</th>';
    if ($direction) { // mode liste comptable
	echo '<th class="decimal">'. association_formater_nombre($total_valeurs) . '</th>';
    } else { // mode liste standard
	echo '<th class="decimal">'. association_formater_nombre($total_recettes) . '</th>';
	echo '<th class="decimal">'. association_formater_nombre($total_depenses) . '</th>';
	// echo '<th class="decimal">'. association_formater_nombre($total_valeurs) . '</th>';
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
    echo '<th class="decimal">'. association_formater_nombre(abs($res)) .'</th>';
    echo "</tr></tfoot></table>";
}

// Brique commune aux classes d'exportation des etats comptables
class ExportComptes_TXT {

    var $exercice;
    var $destination;
    var $annee;
    var $type;
    var $classes;
    var $out;

    // constructeur (fonction d'initialisatio de la classe)
    function __construct($var='') {
	if ( !$var )
	    $tableau = association_passe_parametres_comptables();
	elseif ( is_string($var) )
	    $tableau = unserialize(rawurldecode($var));
	elseif ( is_array($var) )
	    $tableau = $var;
	else
	    $tableau = array($var=>0);
	$this->exercice = intval($tableau['exercice']);
	$this->destination = intval($tableau['destination']);
	$this->annee = intval($tableau['annee']);
	$this->type = $tableau['type'];
	if ( count($tableau['classes']) ) {
	    $this->classes = $tableau['classes'];
	} else {
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

    // export texte de type tableau (lignes*colonnes) simple : CSV,CTX,HTML*SPIP,INI*,TSV,etc.
    // de par la simplicite recherchee il n'y a pas de types ou autres : CSV et CTX dans une certaine mesure pouvant distinguer "nombres", "chaines alphanumeriques" et "chaine binaires encodees"
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
	    $this->LignesSimplesCorps($nomClasse, $champsSeparateur, $lignesSeparateur, $echappements=array(), $champDebut='', $champFin='');
	    $query = association_calcul_soldes_comptes_classe($laClasse, $this->exercice, $this->destination, $multi?0:$laDirection);
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

    // export texte de type s-expression / properties-list / balisage (conteneurs*conteneurs*donnees) simple : JSON, XML (utilisable avec ASN.1), YAML, etc.
    // de par la simplicite recherchee il n'y a pas de types ou d'attributs : BSON, Bencode, JSON, pList, XML, etc.
    function exportLignesMultiples($balises, $echappements=array(), $champDebut='', $champFin='', $indent="\t", $entetesPerso='', $multi=FALSE) {
	$this->out .= "$balises[compteresultat1]\n";
	if (!$entetesPerso) {
	    $this->out .= "$indent$balises[entete1]\n";
	    $this->out .= "$indent$indent$balises[titre1] $champDebut". utf8_decode(html_entity_decode(_T('asso:cpte_resultat_titre_general'))) ."$champFin $balises[titre0]\n";
	    $this->out .= "$indent$indent$balises[nom1] $champDebut". $GLOBALS['association_metas']['nom'] ."$champFin $balises[nom0]\n";
	    $this->out .= "$indent$indent$balises[exercice1] $champDebut". sql_asso1champ('exercice', $this->exercice, 'intitule') ."$champFin $balises[exercice0]\n";
	    $this->out .= "$indent$balises[entete0]\n";
	}
	foreach ($this->classes as $laClasse=>$laDirection) {
	    $baliseClasse = $nomClasse.'1';
	    $this->out .= "$indent$balises[$baliseClasse]\n";
	    $query = association_calcul_soldes_comptes_classe($laClasse, $this->exercice, $this->destination, $laDirection);
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

    // fichier texte final a afficher/telecharger
    function leFichier($ext, $subtype='') {
	$fichier = _DIR_RACINE.'/'._NOM_TEMPORAIRES_ACCESSIBLES.'compte_'. ($subtype?$subtype:$this->type) .'_'.$this->exercice.'_'.$this->destination.".$ext"; // on essaye de creer le fichier dans le cache local/ http://www.spip.net/fr_article4637.html
	$f = fopen($fichier, 'w');
	fputs($f, $this->out);
		fclose($f);
	header('Content-type: application/'.$ext);
	header('Content-Disposition: attachment; filename="'.$fichier.'"');
	readfile($fichier);
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
    var $space_v = 2;
    var $space_h = 2;

    // variables de mise en page calculees
    var $largeur_utile = 0; // largeur sans les marges droites et gauches
    var $largeur_pour_titre = 0; // largeur utile sans icone

    // position du curseur
    var $xx = 0; // abscisse 1ere boite
    var $yy = 0; // ordonnee 1ere boite

    // variables de fonctionnement passees en parametre
    var $annee;
    var $exercice;
    var $destination;

    // Initialisations
    function init($ids='') {
	if ( !$ids )
	    $ids = association_passe_parametres_comptables();
	// passer les parametres transmis aux variables de la classe
	$this->annee = $ids['annee'];
	$this->exercice = $ids['exercice'];
	$this->destination = $ids['destination'];
	// calculer les dimensions de mise en page
	$this->largeur_utile = $GLOBALS['association_metas']['fpdf_widht']-2*$GLOBALS['association_metas']['fpdf_marginl'];
	$this->largeur_pour_titre = $this->largeur_utile-$this->icone_h-3*$this->space_h;
	// initialiser les variables de mise en page
	$this->xx = $GLOBALS['association_metas']['fpdf_marginl'];
	$this->yy = $GLOBALS['association_metas']['fpdf_margint'];
	// meta pour le fichier PDF
	$this->SetAuthor('Marcel BOLLA');
	$this->SetCreator('Associaspip & Fpdf');
	$this->SetTitle('Module Comptabilite');
	$this->SetSubject('Etats comptables');
	// typo par defaut
	$this->SetFont($GLOBALS['association_metas']['fpdf_font']?$GLOBALS['association_metas']['fpdf_font']:'Arial', '', 12);
	// engager la page
	// http://fpdf.org/en/doc/addpage.htm
	$this->AddPage($GLOBALS['association_metas']['fpdf_orientation'], $GLOBALS['association_metas']['fpdf_format']?$GLOBALS['association_metas']['fpdf_format']:array($GLOBALS['association_metas']['fpdf_widht'], $GLOBALS['association_metas']['fpdf_height']) );
    }

    // Pied de pages : redefinition de FPDF::Footer() qui est automatiquement appele par FPDF::AddPage() et FPDF::Close() !
    //@ http://www.id.uzh.ch/cl/zinfo/fpdf/doc/footer.htm
    //!\ Adapter la marge basse (et la hauteur utile) des pages en consequence
    function Footer() {
	// Positionnement a 2 fois la marge du bas
	$this->SetY(-2*$GLOBALS['association_metas']['fpdf_margint']);
	// typo
	$this->SetFont($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:'Arial', 'I', 8); // police: italique 8px
	$this->SetTextColor(128); // Couleur du texte : gris-50.2% (fond blanc)
	// Date et Numéro de page
	$this->Cell(0, 10, html_entity_decode(_T('asso:cpte_export_pied_notice') .' -- '. affdate(date('Y-m-d')) .' -- '. _T('asso:cpte_export_page', array('numero'=>$this->PageNo()) )), 0, 0, 'C');
    }

    // Haut de pages : redefinition de FPDF qui est directement appele par FPDF::AddPage()
    //@ http://www.id.uzh.ch/cl/zinfo/fpdf/doc/header.htm
    //!\ Adapter la marge haute (et la hauteur utile) des pages en consequence
    function Header() {
	// nop
    }

    // cartouche au debut de la 1ere page (contrairement au Header ceci fait partir du contenu/flux et n'est pas repete sur toutes les pages, et peut accepter des parametres)
    function association_cartouche_pdf($titre='') {
	// Les coordonnees courantes
	$xc = $this->xx+$this->space_h;
	$yc = $this->yy+$this->space_v;
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
	$this->SetFont($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:'Arial', 'B', 22); // police : gras 22px
	$this->SetFillColor(235); // Couleur du cadre, du fond du cadre : gris-92,2%
	$this->SetTextColor(0); // Couleur du texte : noir
	// Titre centre
	$xc += $this->space_h+($logo?$this->icone_h:0);
	$this->SetXY($xc, $yc);
	$this->Cell($logo?($this->largeur_pour_titre):($this->largeur_pour_titre+$this->icone_h-$this->space_h), 12, html_entity_decode(_T("asso:$titre")), 0, 0, 'C', TRUE);
	$yc += 12;
	$this->Ln($this->space_v); // Saut de ligne
	$yc += $this->space_v;
	// typo
	$this->SetFont($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:'Arial', '', 12); // police : normal 12px
	$this->SetFillColor(235); // Couleur de remplissage : gris-92.2%
	// Sous titre Nom de l'association
	$this->SetXY($xc, $yc);
	$this->Cell($logo?$this->largeur_pour_titre:$this->largeur_pour_titre+$this->icone_h-$this->space_h, 6, utf8_decode(_T('asso:cpte_export_association', array('nom'=>$GLOBALS['association_metas']['nom']) )), 0, 0, 'C', TRUE);
	$yc += 6;
	$this->Ln($this->space_v/2); // Saut de ligne
	$yc += $this->space_v/2;
	// typo
	$this->SetFont($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:'Arial', '', 12); // police : normal 12px
	$this->SetFillColor(235); // Couleur de fond : gris-92.2%
	//Sous titre Intitule de l'exercice
	$this->SetXY($xc, $yc);
	$this->Cell($logo?$this->largeur_pour_titre:$this->largeur_pour_titre+$this->icone_h-$this->space_h, 6, utf8_decode(_T('asso:cpte_export_exercice', array('titre'=>sql_getfetsel('intitule','spip_asso_exercices', 'id_exercice='.$this->exercice) ) )), 0, 0, 'C', TRUE);
	$yc += 6;
	$this->Ln($this->space_v); // Saut de ligne
	$yc += $this->space_v;
	$this->Rect($this->xx, $this->yy, $this->largeur_utile, $yc-$GLOBALS['association_metas']['fpdf_margint']); // Rectangle tout autour de l'entete
	$this->yy = $yc; // on sauve la position du curseur dans la page
    }

    // Fichier final envoye
    function File($titre='etat_comptes') {
	$this->Output($titre.'_'.($this->exercice?$this->exercice:$this->annee).'_'.$this->destination.'.pdf', 'I');
    }

    // on affiche les totaux (recettes et depenses) d'un exercice des differents comptes de la classe specifiee
    function association_liste_totaux_comptes_classes($classes, $prefixe='', $direction='-1', $exercice=0, $destination=0) {
	if( !is_array($classes) ) { // a priori une chaine ou un entier d'une unique classe
	    $liste_classes = array( $classes ) ; // transformer en tableau (puisqu'on va operer sur des tableaux);
	} else { // c'est un tableau de plusieurs classes
	    $liste_classes = $classes;
	}
	// Les coordonnees courantes
	$xc = $this->xx+$this->space_h;
	$y_orig = $this->yy+$this->space_v;
	$yc = $y_orig+$this->space_v;
	// typo
	$this->SetFont($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:'Arial', 'B', 14); // police: gras 14px
	$this->SetFillColor(235); // Couleursdu fond du cadre de titre : gris-92.2%
	$this->SetTextColor(0); // Couleurs du texte du cadre de titre
	// Titre centre
	$titre = $prefixe.'_'. ( ($direction) ? (($direction<0)?'depenses':'recettes') : 'soldes' );
	$this->SetXY($xc, $yc);
	$this->Cell($this->largeur_utile, 10, html_entity_decode(_T("asso:$titre")), 0, 0, 'C');
	$yc += 10;
	$this->Ln($this->space_v); // Saut de ligne
	$yc += $this->space_v;
	// initialisation du calcul+affichage des comptes
	$total_valeurs = $total_recettes = $total_depenses = 0;
	$chapitre = '';
	$i = 0;
	foreach ( $liste_classes as $rang => $classe ) { // calcul+affichage par classe
	    $query = association_calcul_soldes_comptes_classe($classe, $this->exercice, $this->destination, $direction );
	    $this->SetFont($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:'Arial', '', 12); // police : normal 12px
	    while ($data = sql_fetch($query)) {
		$this->SetXY($xc, $yc); // positionne le curseur
		$new_chapitre = substr($data['code'], 0, 2);
		if ($chapitre!=$new_chapitre) { // debut de categorie
		    $this->SetFillColor(225); // Couleur de fond de la ligne : gris-92.2%
		    $this->Cell(20, 6, utf8_decode($new_chapitre), 0, 0, 'L', TRUE);
		    $this->Cell(($this->largeur_utile)-(2*$this->space_h+20), 6, utf8_decode(($GLOBALS['association_metas']['plan_comptable_prerenseigne']?association_plan_comptable_complet($new_chapitre):sql_getfetsel('intitule','spip_asso_plan',"code='$new_chapitre'"))), 0, 0, 'L', TRUE);
		    $chapitre = $new_chapitre;
		    $this->Ln(); // Saut de ligne
		    $yc += 6;
		}
		$this->SetFillColor(245); // Couleur de fond du total : gris-96.1%
		$this->SetXY($xc, $yc); // positionne le curseur
#	    	if ( floatval($data['valeurs']) || floatval($data['recettes']) || floatval($data['depenses']) ) { // non-zero...
		    $this->Cell(20, 6, utf8_decode($data['code']), 0, 0, 'R', TRUE);
		    $this->Cell(($this->largeur_utile)-(2*$this->space_h+50), 6, utf8_decode($data['intitule']), 0, 0, 'L', TRUE);
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
	    $this->Cell(($this->largeur_utile)-(2*$this->space_h+30), 6, html_entity_decode(_T("asso:$prefixe".'_total')), 1, 0, 'R', TRUE);
	    $this->Cell(30, 6, association_formater_nombre($total_valeurs), 1, 0, 'R', TRUE);
	} else { // mode liste standard : contributions volontaires et autres
	    $this->Cell(($this->largeur_utile)/2-(2*$this->space_h+30), 6, html_entity_decode(_T("asso:$prefixe".'_total_depenses')), 1, 0, 'R', TRUE);
	    $this->Cell(30, 6, association_formater_nombre($total_depenses), 1, 0, 'R', TRUE);
	    $xc += ( $this->largeur_utile)/2;
	    $this->SetXY($xc, $yc); // positionne le curseur sur l'autre demi page
	    $this->Cell(($this->largeur_utile)/2-(2*$this->space_h+30), 6, html_entity_decode(_T("asso:$prefixe".'_total_recettes')), 1, 0, 'R', TRUE);
	    $this->Cell(30, 6, association_formater_nombre($total_recettes), 1, 0, 'R', TRUE);
	}
	$yc += 6;
	$this->Ln($this->space_v); // Saut de ligne
	$yc += $this->space_v;
	$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig); // Rectangle tout autour
	$this->yy = $yc; // on sauve la position du curseur dans la page
	return $total_valeurs;
    }

    // on affiche le resultat comptable net : benefice ou deficit
    function association_liste_resultat_net($lesRecettes, $lesDepenses) {
	// Les coordonnees courantes
	$xc = $this->xx+$this->space_h;
	$y_orig = $this->yy+$this->space_v;
	$yc = $y_orig+$this->space_v;
	// typo
	$this->SetFont($GLOBALS['association_metas']['fpdf_marginl']?$GLOBALS['association_metas']['fpdf_marginl']:'Arial', 'B', 14); // police : gras 14px
	$this->SetFillColor(235); // Couleur du fond : gris-92.2%
	$this->SetTextColor(0); // Couleur du texte : noir
	// Titre centre
	$this->SetXY($xc, $yc);
	$this->Cell($this->largeur_utile, 10, html_entity_decode(_T('asso:cpte_resultat_titre_resultat')), 0, 0, 'C');
	$yc += 10;
	$this->Ln($this->space_v); // Saut de ligne
	$yc += $this->space_v;
	$this->SetFillColor(215); // Couleur de fond : gris-84.3%
	$leSolde = $lesRecettes-$lesDepenses;
	$this->SetXY($xc, $yc);
	$this->Cell(($this->largeur_utile)-(2*$this->space_h+30), 6, html_entity_decode(_T('asso:cpte_resultat_'.($leSolde<0?'perte':'benefice'))), 1, 0, 'R', TRUE);
	$this->Cell(30, 6, association_formater_nombre($leSolde), 1, 0, 'R', TRUE);
	$yc += 6;
	$this->Ln($this->space_v); // Saut de ligne
	$yc += $this->space_v;
	$this->Rect($this->xx, $y_orig, $this->largeur_utile, $yc-$y_orig); // Rectangle tout autour
	$this->yy = $yc; // on sauve la position du curseur dans la page
    }

} // fin classe

} // fin if

?>