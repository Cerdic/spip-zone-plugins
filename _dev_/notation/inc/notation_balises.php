<?php
/**
* Plugin Notation 
* par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
**/

function notation_en_etoile($nb, $id, $clicable=false){
	include_spip('inc/notation');
	$ret = '';
	if ($nb>0 && $nb<=0.5) $nb=1;
	$nb = round($nb);
	
	$class = $clicable ? 'auto-submit-star' : 'star';
	$disabled = $clicable ? '' : "disabled='disabled'";
	for ($i=1; $i<=notation_get_nb_notes(); $i++){
		$checked = ($i==$nb) ? "checked='checked' " : "";
		$ret .= "<input name='notation-$id' type='radio' class='$class' value='$i' $checked $disabled/>\n";
	}
	return "<div class='notation_note'>$ret</div>";
}


/**
 * Notation Etoile permet d'afficher
 * une note transmise sous forme de petites et jolies etoiles
 * 
 * #NOTATION_ETOILE{#NOTE}
 * #NOTATION_ETOILE{#NOTATION_MOYENNE} avec critere {notation} dans une boucle
 * 
 * Un identifiant est calcule automatiquement, mais peut etre force 
 * #NOTATION_ETOILE{#NOTE,article#ID_ARTICLE}
 */
function balise_NOTATION_ETOILE($p){
	$nb = interprete_argument_balise(1,$p);
	if (!$id = interprete_argument_balise(2,$p)){
		$id = notation_calculer_id($p);
	}

	$p->code = "notation_en_etoile($nb,$id)";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Notation Etoile Click permet d'afficher
 * un formulaire en etoile pour noter l'objet
 * /!\ Cette balise ne rempace pas le #FORMULAIRE_NOTATION
 *     (celui ci l'utilise)
 * 
 * #NOTATION_ETOILE_CLICK{#NOTE}
 * #NOTATION_ETOILE_CLICK{#NOTATION_MOYENNE} avec critere {notation} dans une boucle
 * 
 * Un identifiant est calcule automatiquement, mais peut etre force 
 * #NOTATION_ETOILE_CLICK{#NOTE,article#ID_ARTICLE}
 */
function balise_NOTATION_ETOILE_CLICK($p){
	$nb = interprete_argument_balise(1,$p);
	if (!$id = interprete_argument_balise(2,$p)){
		$id = notation_calculer_id($p);
	}
	$p->code = "notation_en_etoile($nb,$id,true)";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * calcule un identifiant en fonction du contexte de boucle
 * cela sert a discriminer les differentes zones d'etoiles pour le js
 */
function notation_calculer_id($p){
	// on recupere le nom de la boucle 
	$table = $p->boucles[$p->id_boucle]->id_table;
	$objet = objet_type($table);
	// on recupere le nom de la cle primaire de l'objet
	$_id_objet = $p->boucles[$p->id_boucle]->primary;
	$id_objet = champ_sql($_id_objet,$p);
	// pondre un identifiant !
	return "'$objet'.$id_objet.uniqid('-')";
}

/**
 * Criteres pour les boucles
 * afin de calculer #NOTATION_MOYENNE,
 * #NOTATION_MOYENNE_PONDEREE et #NOTATION_NOMBRE_VOTES
 * 
 * S'utilise en ajoutant {notation} sur l'objet 
 * dont on veut calculer les stats.
 * 
 * On peut ajouter un selecteur dans le critere sur les 3 champs calcules :
 * {notation moyenne>3} ou {notation nombre_votes>0}
 */
 function critere_notation($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	$objet = objet_type($boucle->type_requete);
	$id_table = $boucle->id_table . '.' . $boucle->primary;

	$ponderation = lire_config('notation/ponderation',30);
	$boucle->select[]= 'COUNT(notations.note) AS nombre_votes';
	$boucle->select[]= 'ROUND(AVG(notations.note),2) AS moyenne';
	$boucle->select[]= 'ROUND(AVG(notations.note)*(1-EXP(-5*COUNT(notations.note)/'.$ponderation.')),2) AS moyenne_ponderee';
	$boucle->from[$boucle->id_table] .= " LEFT JOIN spip_notations AS notations 
		ON (notations.id_objet=$id_table AND notations.objet='.sql_quote($objet).')";
	$boucle->group[]=$id_table;
	
	// Cas d'un {notation moyenne>3}
	$op='';
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=]+)([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'notations.'.$type;
	$type_requete = $boucle->type_requete;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'".$type."'",$op_val);	
}

/**
 * Retourne le nombre de vote sur un objet de SPIP.
 * Necessite le critere {notation} sur la boucle
 * <BOUCLE_(ARTICLES){notation}>#NOTATION_NOMBRE_VOTES ...
 */
function balise_NOTATION_NOMBRE_VOTES_dist($p) {
	$p->code = '$Pile[$SP][\'nombre_votes\']';
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Retourne la moyenne des votes sur un objet de SPIP.
 * Necessite le critere {notation} sur la boucle
 * <BOUCLE_(ARTICLES){notation}>#NOTATION_NOMBRE_VOTES ...
 */
function balise_NOTATION_MOYENNE_dist($p) {
	$p->code = '$Pile[$SP][\'moyenne\']';
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Retourne la moyenne ponderee des votes sur un objet de SPIP.
 * Necessite le critere {notation} sur la boucle
 * <BOUCLE_(ARTICLES){notation}>#NOTATION_NOMBRE_VOTES ...
 */
function balise_NOTATION_MOYENNE_PONDEREE_dist($p) {
	$p->code = '$Pile[$SP][\'moyenne_ponderee\']';
	$p->interdire_scripts = false;
	return $p;
}


?>
