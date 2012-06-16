<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * <BOUCLE(TICKETS)>
 */
//function boucle_TICKETS_dist($id_boucle, &$boucles) {
//	if(!function_exists('lire_config'))
//		include_spip('inc/config');
//
//	if(function_exists('lire_config')){
//		$desactiver_public = lire_config('tickets/general/desactiver_public','off');
//		if (($desactiver_public == 'on') && !test_espace_prive()){
//			array_unshift($boucle->where,array("'='", "'0'", "'1'"));
//		}
//	}
//
//	return calculer_boucle($id_boucle, $boucles);
//}
/**
 * Crée la liste des options du select des champs :
 * -* jalon
 * -* version
 * -* projet
 * -* composant
 * 
 * @param string $nom
 */
function tickets_champ_optionnel_actif($nom){
	$constante = '_TICKETS_LISTE_' . strtoupper($nom);
	if (!defined($constante) && !lire_config('tickets/general/'.$nom)) {
		return false;
	}
	if(defined($constante))
		$liste = constant($constante);
	else 
		$liste = lire_config('tickets/general/'.$nom,'');

	if ($liste == '') return false;

	return explode(':', $liste);
}


// Creation de la liste des options du select d'assignation

/**
 * 
 * Crée la liste des options du champ select d'assignation de ticket
 * Prend deux arguments optionnels :
 * -* $en_cours qui est l'id_auteur que l'on souhaite préselectionner
 * -* $format qui permet de choisir le format que la fonction renvoit :
 * -** rien ou autre chose que 'array' renverra les options d'un select
 * -** 'array' renverra un array des auteurs possibles
 * 
 * @param int $en_cours
 * @param string $format
 * @return multitype:array|string
 */
function tickets_select_assignation($en_cours='0',$format='select'){
	$options = NULL;
	$liste_assignables=array();
	
	include_spip('inc/tickets_autoriser');
	$select = array('nom','id_auteur');
	$from = array('spip_auteurs AS t1');
	$autorises = definir_autorisations_tickets('assigner');
	if ($autorises['statut'])
		$where = array(sql_in('t1.statut', $autorises['statut']), 't1.email LIKE '.sql_quote('%@%'));
	else
		$where = array(sql_in('t1.id_auteur', $autorises['auteur']), 't1.email LIKE '.sql_quote('%@%'));
	
	$query_auteurs = sql_select($select, $from, $where);
	while ($row_auteur = sql_fetch($query_auteurs)) {
		$liste_assignables[$row_auteur["id_auteur"]] = $row_auteur["nom"];
		$selected = ($row_auteur["id_auteur"] == $en_cours) ? ' selected="selected"' : '';
		$options .= '<option value="' . $row_auteur["id_auteur"] . '"' . $selected . '>' . $row_auteur["nom"] . '</option>';
	}
	if($format=='array'){
		return $liste_assignables;
	}
	return $options;
}

// Affichage de la page des tickets classes par jalon
function tickets_classer_par_jalon($bidon) {
	$page = NULL;
	if (defined('_TICKETS_LISTE_JALONS') OR lire_config()) {
		$liste = explode(":", _TICKETS_LISTE_JALONS);
		$i = 0;
		foreach($liste as $_jalon) {
			$i += 1;
			$page .= recuperer_fond('prive/squelettes/inclure/inc_liste_detaillee',
				array_merge($_GET, array('titre' => _T('tickets:champ_jalon').' '.$_jalon, 'statut' => 'ouvert', 'jalon' => $_jalon, 'bloc' => "_bloc$i")),
				array('ajax'=>true));
		}
	}
	return $page;
}

// creation des fonction de selection de texte
// encore en truc a reprendre !
foreach (array('severite', 'tracker', 'statut', 'navigateur') as $nom){
	eval("function tickets_texte_$nom(\$valeur) {
		\$type = tickets_liste_$nom();
		if (isset(\$type[\$valeur])) {
			return \$type[\$valeur];
		}
	}");
}

function tickets_icone_statut ($niveau,$full=false) {
	$img = array(
		"redac" => "puce-blanche.gif",
		"ouvert" => "puce-orange.gif",
		"resolu" => "puce-verte.gif",
		"ferme" => "puce-poubelle.gif",
		"poubelle" => "puce-poubelle.gif"
		);
	if($full)
		return '<img src="'.find_in_path('prive/images/'.$img[$niveau]).'" alt="'.tickets_texte_statut($niveau).'" />';
	else
		return $img[$niveau];
}

function tickets_icone_severite ($niveau,$full=false) {
	$img = array(
		1 => "puce-rouge-breve.gif",
		2 => "puce-orange-breve.gif",
		3 => "puce-verte-breve.gif",
		4 => "puce-poubelle-breve.gif"
		);
	if($full)
		return '<img src="'.find_in_path('prive/images/'.$img[$niveau]).'" alt="'.tickets_texte_severite($niveau).'" />';
	else
		return $img[$niveau];
}

function tickets_liste_statut($connecte = true){
	$statuts = array(
		"redac" => _T("tickets:statut_redac"),
		"ouvert" => _T("tickets:statut_ouvert"),
		"resolu" => _T("tickets:statut_resolu"),
		"ferme" => _T("tickets:statut_ferme"),
		"poubelle" => _T("tickets:statut_poubelle")
	);
	if (!$connecte) {
		unset($statuts['redac']);
	}
	return $statuts;
}


function tickets_liste_tracker($id_ticket = null){
	$trackers = array(
		1 => _T("tickets:type_probleme"),
		2 => _T("tickets:type_amelioration"),
		3 => _T("tickets:type_tache"),
	);
	return $trackers;
}

function tickets_liste_severite($id_ticket = null){
	$severites = array(
		1 => _T("tickets:severite_bloquant"),
		2 => _T("tickets:severite_important"),
		3 => _T("tickets:severite_normal"),
		4 => _T("tickets:severite_peu_important"),
	);
	return $severites;
}

/**
 * Liste des navigateurs possibles
 */
function tickets_liste_navigateur($nav=false){
	$navs = array(
		'tous' => _T('tickets:option_navigateur_tous'),
		'android4' => 'Android 4.x',
		'firefox3' => 'Firefox 3.x',
		'firefox4' => 'Firefox 4.x',
		'firefox5' => 'Firefox 5.x',
		'firefox6' => 'Firefox 6.x',
		'chrome9' => 'Google Chrome 9.x',
		'chrome11' => 'Google Chrome 11.x',
		'chrome12' => 'Google Chrome 12.x',
		'chrome13' => 'Google Chrome 13.x',
		'chrome14' => 'Google Chrome 14.x',
		'ie6' => 'Internet Explorer 6',
		'ie7' => 'Internet Explorer 7',
		'ie8' => 'Internet Explorer 8',
		'ie9' => 'Internet Explorer 9',
		'opera11' => 'Opera 11.x',
		'opera12' => 'Opera 12.x',
		'safari4' => 'Safari 4.x',
		'safari5' => 'Safari 5.x',
		'autre' => _T('tickets:option_navigateur_autre')
	);
	return $navs;
}

?>
