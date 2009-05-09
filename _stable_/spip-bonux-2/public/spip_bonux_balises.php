<?php
/**
 * Plugin Spip-Bonux
 * Le plugin qui lave plus SPIP que SPIP
 * (c) 2008 Mathieu Marcillaud, Cedric Morin, Romy Tetue
 * Licence GPL
 * 
 */

//
// #URL_ACTION_AUTEUR{converser,arg,redirect} -> ecrire/?action=converser&arg=arg&hash=xxx&redirect=redirect
//
// http://doc.spip.org/@balise_URL_ACTION_AUTEUR_dist
function balise_URL_ACTION_AUTEUR($p) {
	$p->descr['session'] = true;

	if (isset($p->boucles[$p->id_boucle])
	AND $p->boucles[$p->id_boucle]->sql_serveur
	AND $p->boucles[$p->id_boucle]->sql_serveur!='pour'
	AND $p->boucles[$p->id_boucle]->sql_serveur!='condition') {
		$p->code = 'generer_url_public("404")';
		return $p;
	}

	$p->code = interprete_argument_balise(1,$p);
	$args = interprete_argument_balise(2,$p);
	if (!$args)
		$args = "''";
	$p->code .= ",".$args;
	$redirect = interprete_argument_balise(3,$p);
	if ($redirect != "''" && $redirect!==NULL)
		$p->code .= ",".$redirect;

	$p->code = "generer_action_auteur(" . $p->code . ")";
	$p->interdire_scripts = false;
	return $p;
}
//
// #URL_ECRIRE{naviguer} -> ecrire/?exec=naviguer
//
// http://doc.spip.org/@balise_URL_ECRIRE_dist
function balise_URL_ECRIRE($p) {

	if (isset($p->boucles[$p->id_boucle])
	AND $p->boucles[$p->id_boucle]->sql_serveur
	AND $p->boucles[$p->id_boucle]->sql_serveur!='pour'
	AND $p->boucles[$p->id_boucle]->sql_serveur!='condition') {
		$p->code = 'generer_url_public("404")';
		return $p;
	}

	$p->code = interprete_argument_balise(1,$p);
	$args = interprete_argument_balise(2,$p);
	if ($args != "''" && $args!==NULL)
		$p->code .= ','.$args;

	// autres filtres (???)
	array_shift($p->param);

	$p->code = 'generer_url_ecrire(' . $p->code .')';

	#$p->interdire_scripts = true;
	return $p;
}



/**
 * #SET
 * Affecte une variable locale au squelette
 * #SET{nom,valeur}
 * 
 * SURCHARGE DU CORE : 
 * 		Affecte un filtre a une variable locale au squelette
 * 		#SET{nom,filtre,param1,param2,...,paramN}
 *
 * @param object $p : objet balise
 * @return ""
**/
/*
function balise_SET($p){
	$_code = array();	
	
	$n=1;
	while ($_v = interprete_argument_balise($n++,$p))
		$_code[] = $_v;

	$_nom = array_shift($_code);
	$_valeur = array_shift($_code);
	if ($_nom AND $_valeur AND count($_code)) {
		$filtre = str_replace("'", "", strtolower($_valeur));
		$f = chercher_filtre($filtre);
		$p->code = "vide(\$Pile['vars'][$_nom]=$f(". join(', ',$_code)."))";
	} elseif ($_nom AND $_valeur)
		$p->code = "vide(\$Pile['vars'][$_nom] = $_valeur)";
	else
		$p->code = "''";

	$p->interdire_scripts = false; // la balise ne renvoie rien
	return $p;
}
*/


/**
 * Empile un element dans un tableau declare par #SET{tableau,#ARRAY}
 * #SET_PUSH{tableau,valeur}
 *
 * @param object $p : objet balise
 * @return ""
**/
function balise_SET_PUSH_dist($p){
	$_nom = interprete_argument_balise(1,$p);
	$_valeur = interprete_argument_balise(2,$p);

	if ($_nom AND $_valeur)
		// si le tableau n'existe pas encore, on le cree
		// on ajoute la valeur ensuite (sans passer par array_push)
		$p->code = "vide((\$cle=$_nom) 
			. (is_array(\$Pile['vars'][\$cle])?'':\$Pile['vars'][\$cle]=array())
			. (\$Pile['vars'][\$cle][]=$_valeur))";
	else
		$p->code = "''";

	$p->interdire_scripts = false; // la balise ne renvoie rien
	return $p;
}

/**
 * Si 3 arguments : Cree un tableau nom_tableau de t1 + t2
 * #SET_MERGE{nom_tableau,t1,t2}
 * #SET_MERGE{nom_tableau,#GET{tableau},#ARRAY{cle,valeur}}
 * 
 * Si 2 arguments : Merge t1 dans nom_tableau
 * #SET_MERGE{nom_tableau,t1}
 * #SET_MERGE{nom_tableau,#GET{tableau}}
 * 
 * @param object $p : objet balise
 * @return ""
**/
function balise_SET_MERGE_dist($p){
	$_nom = interprete_argument_balise(1,$p);
	$_t1 = interprete_argument_balise(2,$p);
	$_t2 = interprete_argument_balise(3,$p);

	if ($_nom AND $_t1 AND !$_t2)
		// 2 arguments : merge de $_nom et $_t1 dans $_nom
		// si le tableau n'existe pas encore, on le cree
		$p->code = "vide((\$cle=$_nom) 
			. (is_array(\$Pile['vars'][\$cle])?'':\$Pile['vars'][\$cle]=array())
			. (is_array(\$new=$_t1)?'':\$new=array(\$new))
			. (\$Pile['vars'][\$cle] = array_merge(\$Pile['vars'][\$cle],\$new)))";
	elseif ($_nom AND $_t1 AND $_t2)
		// 3 arguments : merge de $_t1 et $_t2 dans $_nom
		// si le tableau n'existe pas encore, on le cree
		$p->code = "vide((\$cle=$_nom) 
			. (is_array(\$Pile['vars'][\$cle])?'':\$Pile['vars'][\$cle]=array())
			. (is_array(\$new1=$_t1)?'':\$new1=array(\$new1))
			. (is_array(\$new2=$_t2)?'':\$new2=array(\$new2))
			. (\$Pile['vars'][\$cle] = array_merge(\$new1,\$new2)))";	
	else
		$p->code = "''";

	$p->interdire_scripts = false; // la balise ne renvoie rien
	return $p;
}

/**
 * Balise #COMPTEUR associee au critere compteur
 *
 * @param unknown_type $p
 * @return unknown
 */
function balise_COMPTEUR_dist($p) {
	return calculer_balise_criteres('compteur', $p);
}

/** Balise #SOMME associee au critere somme */
function balise_SOMME_dist($p) {
	return calculer_balise_criteres('somme', $p);
}

/** Balise #COMPTE associee au critere compte */
function balise_COMPTE_dist($p) {
	return calculer_balise_criteres('compte', $p);
}

/** Balise #MOYENNE associee au critere moyenne */
function balise_MOYENNE_dist($p) {
	return calculer_balise_criteres('moyenne', $p);
}

/** Balise #MINIMUM associee au critere moyenne */
function balise_MINIMUM_dist($p) {
	return calculer_balise_criteres('minimum', $p);
}

/** Balise #MAXIMUM associee au critere moyenne */
function balise_MAXIMUM_dist($p) {
	return calculer_balise_criteres('maximum', $p);
}

/** Balise #STATS associee au critere stats
 * #STATS{id_article,moyenne}
 */
function balise_STATS_dist($p) {
	if (isset($p->param[0][2][0])
	AND $nom = ($p->param[0][2][0]->texte)) {
		return calculer_balise_criteres($nom, $p, 'stats');
	}
	return $p;
}

function calculer_balise_criteres($nom, $p, $balise="") {
	$p->code = '';
	$balise = $balise ? $balise : $nom;
	if (isset($p->param[0][1][0])
	AND $champ = ($p->param[0][1][0]->texte)) {
		return rindex_pile($p, $nom."_$champ", $balise);
	}
  return $p;
}



/**
 * #TRI{champ[,libelle]}
 * champ prend < ou > pour afficher le lien de changement de sens
 * croissant ou decroissant
 *
 * @param unknown_type $p
 * @param unknown_type $liste
 * @return unknown
 */
function balise_TRI_dist($p, $liste='true') {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];

	// s'il n'y a pas de nom de boucle, on ne peut pas trier
	if ($b === '') {
		erreur_squelette(
			_T('zbug_champ_hors_boucle',
				array('champ' => '#TRI')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}
	$boucle = $p->boucles[$b];

	// s'il n'y a pas de tri_champ, c'est qu'on se trouve
	// dans un boucle recursive ou qu'on a oublie le critere {tri}
	if (!isset($boucle->modificateur['tri_champ'])) {
		erreur_squelette(
			_T('zbug_tri_sans_critere',
				array('champ' => '#TRI')
			), $p->id_boucle);
		$p->code = "''";
		return $p;
	}

	$_champ = interprete_argument_balise(1,$p);
	// si pas de champ, rien a faire !
	if (!$_champ){
		$p->code = "''";
		return $p;
	}
	
	$_libelle = interprete_argument_balise(2,$p);
	$_libelle = $_libelle?$_libelle:$_champ;
	// si champ = "<" c'est un lien vers le tri croissant : 1<2<3<4 ... ==> 1
	// si champ = ">" c'est un lien vers le tri decroissant :.. 4>3>2>1 == -1
	$_issens = "in_array($_champ,array('<','>'))";
	$_sens = "(strpos('> <',$_champ)-1)";
	
	$_variable = "((\$s=$_issens)?'sens':'tri').".$boucle->modificateur['tri_nom'];
	$_url = "parametre_url(self(),$_variable,\$s?$_sens:$_champ)";
	$_on = "\$s?(".$boucle->modificateur['tri_sens']."==$_sens".'):('.$boucle->modificateur['tri_champ']."==$_champ)";
	
	$p->code = "aoustrong($_url,$_libelle,$_on)";
	//$p->code = "''";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Generer un bouton d'action en post, ajaxable
 * a utiliser a la place des liens action_auteur, sous la forme
 * #BOUTON_ACTION{libelle,url}
 * ou
 * #BOUTON_ACTION{libelle,url,ajax} pour que l'action soit ajax comme un lien class='ajax'
 *
 * @param unknown_type $p
 * @return unknown
 */
function balise_BOUTON_ACTION($p){

	$_label = interprete_argument_balise(1,$p);
	if (!$_label) $_label="''";
	$_url = interprete_argument_balise(2,$p);
	if (!$_url) $_url="''";

	$_class = interprete_argument_balise(3,$p);
	if (!$_class) $_class="''";

	$p->code = "'<form class=\'bouton_action_post '.$_class.'\' method=\'post\' action=\''.(\$u=$_url).'\'><span>'.form_hidden(\$u).'<input type=\'submit\' class=\'submit\' value=\''.attribut_html($_label).'\' /></span></form>'";
	$p->interdire_scripts = false;
	return $p;
}
?>
