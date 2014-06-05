<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('public/iterateur');

// filtre table_valeur
// permet de recuperer la valeur d'un tableau pour une cle donnee
// prend en entree un tableau serialise ou non (ce qui permet d'enchainer le filtre)
// ou un objet
// Si la cle est de la forme a.b, on renvoie $table[a][b]
function Iterateurs_table_valeur($table,$cle,$defaut=''){
	foreach (explode('/', $cle) as $k) if ($k !== "") {
		$table= is_string($table) ? unserialize($table) : $table;

		if (is_object($table))
			$table = isset($table->$k) ? $table->$k : $default;
		else if (is_array($table))
			$table = isset($table[$k]) ? $table[$k] : $defaut;
		else
			$table = $default;
	}
	return $table;
}


// {source mode, "xxxxxx", arg, arg, arg}
function critere_source($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];

	$args = array();
	foreach ($crit->param as &$param)
		array_push($args,
		calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent));

	$boucle->hash .= '
	$command[\'sourcemode\'] = '. array_shift($args). ";\n";

	$boucle->hash .= '
	$command[\'source\'] = array('. join(', ', $args). ");\n";

}


// {datasource "xxxxxx", mode}  <= deprecated
function critere_datasource($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->hash .= '
	$command[\'source\'] = array('.calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent).');
	$command[\'sourcemode\'] = '.calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent).';';
}

function critere_datacache($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->hash .= '
	$command[\'datacache\'] = '.calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent).';';
}

// {tableau #XX} pour compatibilite ascendante boucle POUR
// ... preferer la notation {datasource #XX,table}
function critere_tableau($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->hash .= '
	$command[\'source\'] = array('.calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent).');
	$command[\'sourcemode\'] = \'table\';';
}


/*
 * Pour passer des arguments a un iterateur non-spip
 * (php:xxxIterator){args argument1, argument2, argument3}
 */
function critere_args($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->hash .= '$command[\'args\']=array();';
	foreach($crit->param as $param) {
		$boucle->hash .= '
			$command[\'args\'][] = '.calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent).';';
	}
}

/*
 * Passer une liste de donnees a l'iterateur DATA
 * (DATA){liste X1, X2, X3}
 */
function critere_liste($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->hash .= "\n\t".'$command[\'liste\'] = array();'."\n";
	foreach($crit->param as $param) {
		$boucle->hash .= "\t".'$command[\'liste\'][] = '.calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent).";\n";
	}
}

/*
 * Extraire un chemin d'un tableau de donnees
 * (DATA){datapath query.results}
 */
function critere_datapath($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	foreach($crit->param as $param) {
		$boucle->hash .= '
			$command[\'datapath\'][] = '.calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent).';';
	}
}

/* le critere {si ...} applicable a toutes les boucles
 * Doit passer par dessus spip-bonux-2 depuis r82600
 */
function critere_CONDITION_si($idb, &$boucles, $crit) {
	return critere_si($idb, $boucles, $crit);
}

/* le critere {si ...} applicable a toutes les boucles */
function critere_si($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// il faut initialiser 1 fois le tableau a chaque appel de la boucle
	// (par exemple lorsque notre boucle est appelee dans une autre boucle)
	// mais ne pas l'initialiser n fois si il y a n criteres {si } dans la boucle !
	$boucle->hash .= "\n\tif (!isset(\$si_init)) { \$command['si'] = array(); \$si_init = true; }\n";
	if ($crit->param) {
		foreach($crit->param as $param) {
			$boucle->hash .= "\t\$command['si'][] = "
					. calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent) . ";\n";
		}
	// interdire {si 0} aussi !
	} else {
			$boucle->hash .= '$command[\'si\'][] = 0;';
	}
}


// {pagination}
// {pagination 20}
// {pagination #ENV{pages,5}} etc
// {pagination 20 #ENV{truc,chose}} pour utiliser la variable debut_#ENV{truc,chose}
// http://www.spip.net/@pagination
// http://doc.spip.org/@critere_pagination
function critere_pagination($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	// definition de la taille de la page
	$pas = !isset($crit->param[0][0]) ? "''" : calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucle->id_parent);

	if (!preg_match(_CODE_QUOTE, $pas, $r)) {
		$pas = "((\$a = intval($pas)) ? \$a : 10)";
	} else {
		$r = intval($r[2]);
		$pas = strval($r ? $r : 10);
	}
	$type = !isset($crit->param[0][1]) ? "'$idb'" : calculer_liste(array($crit->param[0][1]), array(), $boucles, $boucle->id_parent);
	$debut = ($type[0]!=="'") ? "'debut'.$type"
	  : ("'debut" .substr($type,1));

	$boucle->modificateur['debut_nom'] = $type;
	$partie =
		 // tester si le numero de page demande est de la forme '@yyy'
		 'isset($Pile[0]['.$debut.']) ? $Pile[0]['.$debut.'] : _request('.$debut.");\n"
		."\tif(substr(\$debut_boucle,0,1)=='@'){\n"
		."\t\t".'$debut_boucle = $Pile[0]['. $debut.'] = Iterateurs_quete_debut_pagination(\''.$boucle->primary.'\',$Pile[0][\'@'.$boucle->primary.'\'] = substr($debut_boucle,1),'.$pas.',$iter);'."\n"
		."\t\t".'$iter->seek(0);'."\n"
		."\t}\n"
		."\t".'$debut_boucle = intval($debut_boucle)';


	$boucle->total_parties = $pas;
	calculer_parties($boucles, $idb, $partie, 'p+');
	// ajouter la cle primaire dans le select pour pouvoir gerer la pagination referencee par @id
	// sauf si pas de primaire, ou si primaire composee
	// dans ce cas, on ne sait pas gerer une pagination indirecte
	$t = $boucle->id_table . '.' . $boucle->primary;
	if ($boucle->primary
		AND !preg_match('/[,\s]/',$boucle->primary)
		AND !in_array($t, $boucle->select))
	  $boucle->select[]= $t;
}



###### BALISES
/**
 * #LISTE{a,b,c,d,e} cree un #ARRAY avec les valeurs, sans preciser les cles
 *
 * @param <type> $p
 * @return <type>
 */
function balise_LISTE($p) {
	$_code = array();
	$n=1;
	while ($_val = interprete_argument_balise($n++,$p))
		$_code[] = $_val;
	$p->code = 'array(' . join(', ',$_code).')';
	$p->interdire_scripts = false;
	return $p;
}


/**
 * #SAUTER{n} permet de sauter en avant n resultats dans une boucle
 * La balise modifie le compteur courant de la boucle, mais pas les autres
 * champs qui restent les valeurs de la boucle avant le saut. Il est donc
 * preferable d'utiliser la balise juste avant la fermeture </BOUCLE>
 *
 * L'argument n doit etre superieur a zero sinon la balise ne fait rien
 *
 * @param <type> $p
 * @return <type>
 */
function balise_SAUTER($p){
	$id_boucle = $p->id_boucle;
	$boucle = $p->boucles[$id_boucle];

	if (!$boucle) {
		$msg = array('zbug_champ_hors_boucle', array('champ' => '#SAUTER'));
		erreur_squelette($msg, $p);
	}
	else {
		$_saut = interprete_argument_balise(1,$p);
		$_compteur = "\$Numrows['$id_boucle']['compteur_boucle']";
		$_total = "\$Numrows['$id_boucle']['total']";

		$p->code = "vide($_compteur=\$iter->skip($_saut,$_total))";
	}
	$p->interdire_scripts = false;
	return $p;
}

// #VALEUR renvoie le champ valeur
// #VALEUR{x} renvoie #VALEUR|Iterateurs_table_valeur{x}
// #VALEUR{a/b} renvoie #VALEUR|Iterateurs_table_valeur{a/b}
function balise_VALEUR($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$p->code = index_pile($p->id_boucle, 'valeur', $p->boucles, $b);;
	if (($v = interprete_argument_balise(1,$p))!==NULL){
		$p->code = 'Iterateurs_table_valeur('.$p->code.', '.$v.')';
	}
	$p->interdire_scripts = true;
	return $p;
}


function Iterateurs_quete_debut_pagination($primary,$valeur,$pas,$iter){
	// on ne devrait pas arriver ici si la cle primaire est inexistante
	// ou composee, mais verifions
	if (!$primary OR preg_match('/[,\s]/',$primary))
		return 0;

	$pos = 0;
	while ($row = $iter->fetch() AND $row[$primary]!=$valeur){
		$pos++;
	}
	// si on a pas trouve
	if ($row[$primary]!=$valeur)
		return 0;

	// sinon, calculer le bon numero de page
	return floor($pos/$pas)*$pas;
}

// afficher proprement n'importe quoi
// en cas de table profonde, l'option $join ne s'applique qu'au plus haut niveau
// c'est VOULU !  Exemple : [(#VALEUR|print{<hr />})] va afficher de gros blocs
// separes par des lignes, avec a l'interieur des trucs separes par des virgules
function filtre_print($u, $join=', ') {
	if (is_string($u))
		return typo($u);

	if (is_array($u))
		return join($join, array_map('filtre_print', $u));

	if (is_object($u))
		return join($join, array_map('filtre_print', (array) $u));

	return $u;
}

