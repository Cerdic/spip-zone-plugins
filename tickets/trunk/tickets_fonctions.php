<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * @package SPIP\Tickets\Fonctions
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Crée la liste des options du select des champs :
 * -* jalon
 * -* version
 * -* projet
 * -* composant
 * 
 * @param string $nom
 * 		Le nom du champ
 * @return array|false
 * 		Le tableau des valeurs possibles ou false si aucune
 */
function tickets_champ_optionnel_actif($nom){
	$constante = '_TICKETS_LISTE_' . strtoupper($nom);
	if (!defined($constante) && !lire_config('tickets/general/'.$nom))
		return false;
	if(defined($constante))
		$liste = constant($constante);
	else
		$liste = lire_config('tickets/general/'.$nom,'');

	if ($liste == '') return false;

	return array_map('trim',explode(':', $liste));
}

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
	if($format=='array')
		return $liste_assignables;

	return $options;
}

// Affichage de la page des tickets classes par version
function tickets_classer_par_version($bidon) {
	$page = NULL;
	if (defined('_TICKETS_LISTE_VERSIONS') OR lire_config('tickets/general/versions')){
		if (defined('_TICKETS_LISTE_VERSIONS'))
			$liste = explode(":", _TICKETS_LISTE_VERSIONS);
		else
			$liste = explode(":", lire_config('tickets/general/versions'));
		
		$liste = array_map('trim',$liste);
		
		include_spip('inc/texte');
		$i = 0;
		foreach($liste as $_version) {
			$i += 1;
			$page .= recuperer_fond('prive/squelettes/inclure/inc_liste_detaillee',
				array_merge($_GET, array('titre' => _T('tickets:champ_version').' '.((strlen($_version) > 0) ? extraire_multi($_version) : _T('tickets:info_sans_version')), 'statut' => 'ouvert', 'version' => $_version, 'bloc' => "_bloc$i")),
				array('ajax'=>true));
		}
	}
	return $page;
}

// Affichage de la page des tickets classes par jalon
function tickets_classer_par_jalon($bidon) {
	$page = NULL;
	if (defined('_TICKETS_LISTE_JALONS') OR lire_config('tickets/general/jalons')) {
		if (defined('_TICKETS_LISTE_JALONS'))
			$liste = explode(":", _TICKETS_LISTE_JALONS);
		else
			$liste = explode(":", lire_config('tickets/general/jalons'));
			
		$liste = array_map('trim',$liste);
		
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

// Affichage de la page des tickets classes par composant
function tickets_classer_par_composant($bidon) {
	$page = NULL;
	if (defined('_TICKETS_LISTE_COMPOSANTS') OR lire_config('tickets/general/composants')) {
		if (defined('_TICKETS_LISTE_COMPOSANTS'))
			$liste = explode(":", _TICKETS_LISTE_COMPOSANTS);
		else
			$liste = explode(":", lire_config('tickets/general/composants'));
			
		$liste = array_map('trim',$liste);
		
		$i = 0;
		foreach($liste as $_composant) {
			$i += 1;
			$page .= recuperer_fond('prive/squelettes/inclure/inc_liste_detaillee',
				array_merge($_GET, array('titre' => _T('tickets:champ_composant').' '.((strlen($_composant) > 0) ? $_composant : _T('tickets:info_sans')), 'statut' => 'ouvert', 'composant' => $_composant, 'bloc' => "_bloc$i")),
				array('ajax'=>true));
		}
	}
	return $page;
}

// Affichage de la page des tickets classes par projet
function tickets_classer_par_projet($bidon) {
	$page = NULL;
	if (defined('_TICKETS_LISTE_PROJETS') OR lire_config('tickets/general/projets')) {
		if (defined('_TICKETS_LISTE_PROJETS'))
			$liste = explode(":", _TICKETS_LISTE_PROJETS);
		else
			$liste = explode(":", lire_config('tickets/general/projets'));
		
		$liste = array_map('trim',$liste);
		
		$i = 0;
		foreach($liste as $_projet) {
			$i += 1;
			$page .= recuperer_fond('prive/squelettes/inclure/inc_liste_detaillee',
				array_merge($_GET, array('titre' => _T('tickets:champ_projet').' '.$_projet, 'statut' => 'ouvert', 'projet' => $_projet, 'bloc' => "_bloc$i")),
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

function tickets_icone_severite ($niveau,$full=false,$alt=false) {
	$img = array(
		1 => "puce-rouge-breve.gif",
		2 => "puce-orange-breve.gif",
		3 => "puce-verte-breve.gif",
		4 => "puce-poubelle-breve.gif"
		);
	if($full){
		$balise_img = charger_filtre('balise_img');
		$img = $balise_img(find_in_path('prive/images/'.$img[$niveau]));
		if($alt)
			$img = inserer_attribut($img,'alt',tickets_texte_severite($niveau));
		return $img;
	}
	else
		return $img[$niveau];
}

function tickets_liste_statut($connecte = true){
	$statuts = array(
		"ouvert" => _T("tickets:statut_ouvert"),
		"resolu" => _T("tickets:statut_resolu"),
		"ferme" => _T("tickets:statut_ferme"),
		"poubelle" => _T("tickets:statut_poubelle")
	);
	return $statuts;
}


function tickets_liste_tracker($id_ticket = null){
	$trackers = array(
		1 => _T("tickets:type_probleme"),
		2 => _T("tickets:type_amelioration"),
		3 => _T("tickets:type_tache"),
	);
	$trackers = pipeline('tickets_liste_tracker',array('args'=>'nom_court','data'=>$trackers));
	return $trackers;
}

function tickets_liste_tracker_nom_long($id_ticket = null){
	$trackers = array(
		1 => _T("tickets:type_probleme_long"),
		3 => _T("tickets:type_tache_long"),
		2 => _T("tickets:type_amelioration_long"),
	);
	$trackers = pipeline('tickets_liste_tracker',array('args'=>'nom_long','data'=>$trackers));
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
		'android2' => 'Android 2.x',
		'android4' => 'Android 4.x',
		'firefox4' => 'Firefox <= 4',
		'firefox10' => 'Firefox <= 10',
		'firefox15' => 'Firefox <= 15',
		'firefox20' => 'Firefox <= 20',
		'chrome10' => 'Google <= 10',
		'chrome15' => 'Google Chrome <= 15',
		'chrome20' => 'Google Chrome <= 20',
		'chrome25' => 'Google Chrome <= 25',
		'chrome30' => 'Google Chrome > 25',
		'ie8' => 'Internet Explorer 8',
		'ie9' => 'Internet Explorer 9',
		'ie10' => 'Internet Explorer 10',
		'opera11' => 'Opera 11.x',
		'opera12' => 'Opera 12.x',
		'safari4' => 'Safari 4.x',
		'safari5' => 'Safari 5.x',
		'safariipad' => 'Safari Ipad',
		'autre' => _T('tickets:option_navigateur_autre')
	);
	return $navs;
}

?>
