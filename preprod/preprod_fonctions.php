<?php
/**
 * Plugin PreProd pour Spip 2.0
 * Licence GPL (c) 2011 - Ateliers CYM
 */


// Balise #NOM_AUTEUR
function balise_NOM_AUTEUR($p) {
	$id_auteur = interprete_argument_balise (1, $p);
	$p->code = "trouve_nom(".$id_auteur.")";
	$p->statut = 'php';
	return $p;
}
function trouve_nom($id_auteur) {
	$nom = sql_getfetsel("nom","spip_auteurs", "id_auteur=" . intval($id_auteur));
	if (!empty($nom))
		return $nom;
	return '';
}

// Extraites de Tickets
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
	spip_log($liste,'ticket');
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
			$page .= recuperer_fond('prive/contenu/inc_liste_detaillee',
				array_merge($_GET, array('titre' => _T('tickets:champ_jalon').' '.$_jalon, 'statut' => 'ouvert', 'jalon' => $_jalon, 'bloc' => "_bloc$i")),
				array('ajax'=>true));
		}
	}
	return $page;
}

// Affichage des blocs de liste depliables et ajaxes
function tickets_debut_block_depliable($deplie,$id=""){
	include_spip('inc/layer');
	return debut_block_depliable($deplie,$id);
}
function tickets_fin_block() {
	include_spip('inc/layer');
	return fin_block();
}
function tickets_bouton_block_depliable($texte,$deplie,$page="",$ids=""){
	include_spip('inc/layer');
	if ($page)
		return bouton_block_depliable(afficher_plus(generer_url_ecrire($page)).$texte,$deplie,$ids);
	else
		return bouton_block_depliable($texte,$deplie,$ids);
	
}

// creation des fonction de selection de texte
// encore en truc a reprendre !
foreach (array('severite', 'type', 'statut', 'navigateur') as $nom){
	eval("function tickets_texte_$nom(\$valeur) {
		\$type = tickets_liste_$nom();
		if (isset(\$type[\$valeur])) {
			return \$type[\$valeur];
		}
	}");
}

function tickets_icone_statut ($niveau) {
	$img = array(
		"redac" => "puce-blanche.gif",
		"ouvert" => "puce-orange.gif",
		"resolu" => "puce-verte.gif",
		"ferme" => "puce-poubelle.gif"
		);
	return $img[$niveau];
}

function tickets_icone_severite ($niveau) {
	$img = array(
		1 => "puce-rouge-breve.gif",
		2 => "puce-orange-breve.gif",
		3 => "puce-verte-breve.gif",
		4 => "puce-poubelle-breve.gif"
		);
	return $img[$niveau];
}

function tickets_liste_statut($connecte = true){
	$statuts = array(
		"redac" => _T("tickets:statut_redac"),
		"ouvert" => _T("tickets:statut_ouvert"),
		"resolu" => _T("tickets:statut_resolu"),
		"ferme" => _T("tickets:statut_ferme"),
	);
	if (!$connecte) {
		unset($statuts['redac']);
	}
	return $statuts;
}

function tickets_liste_type($id_ticket = null){
	$types = array(
		1 => _T("tickets:type_probleme"),
		2 => _T("tickets:type_amelioration"),
		3 => _T("tickets:type_tache"),
	);
	return $types;
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

if(!function_exists('barre_typo')){
	function barre_typo(){
		return;
	}
}

/**
 * 
 * Fonction de génération d'url privée de tickets
 * 
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @param string $statut
 * @param string $connect
 */
function generer_url_ecrire_ticket($id, $args='', $ancre='', $statut='', $connect='') {
	$a = "id_ticket=" . intval($id);
	if (!$statut) {
		$statut = sql_getfetsel('statut', 'spip_tickets', $a,'','','','',$connect);
	}
	$h = generer_url_ecrire('ticket_afficher', $a . ($args ? "&$args" : ''))
	. ($ancre ? "#$ancre" : '');
	return $h;
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