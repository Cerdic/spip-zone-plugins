<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_NOTATION_AFFICHAGE_RAPIDE',1);

function notation_en_etoile($nb, $id, $clicable=false,$microdatas=false){
	include_spip('inc/notation');
	$ret = '';
	if ($nb>0 && $nb<=0.5) $nb=1;
	$needjs = "";
	$max_note = notation_get_nb_notes();
	$nb = round($nb);
	if($microdatas){
		$ret .= '<meta itemprop="ratingCount" class="best" content="'.$max_note.'" />';
		$ret .= '<meta itemprop="worstRating" class="worst" content="0" />';
		$ret .= '<meta itemprop="ratingValue" content="'.$nb.'" />';
	}
	if ($clicable OR !_NOTATION_AFFICHAGE_RAPIDE){
		$needjs = " notation_note_on_load";
		$class = $clicable ? 'auto-submit-star' : 'star';
		$disabled = $clicable ? '' : " disabled='disabled'";
		if (include_spip("inc/config")
		AND lire_config('notation/change_note')){
			$ret .= "<input name='notation-$id' type='radio' class='$class rating-cancel' value='-1'$checked$disabled />\n";
		}
		for ($i=1; $i<=$max_note; $i++){
			$checked = ($i==$nb) ? " checked='checked'" : "";
			$ret .= "<input name='notation-$id' type='radio' class='$class' value='$i'$checked$disabled />\n";
		}
	}
	else 
	// eviter de generer X boutons radio inactifs remplaces par le javascript au chargement
	{
		for ($i=1; $i<=$max_note; $i++){
			$checked = ($i<=$nb) ? " star-rating-on" : "";
			$ret .= "<div class='star-rating ratingstar_group_notation-$id star-rating-readonly$checked'><a>$nb</a></div>";
		}
	}
	return "<div class='notation_note$needjs' ".($microdatas ? 'itemprop="aggregateRating" itemscope itemtype="http://schema.org/aggregateRating"':'').">$ret</div>";
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
 * 
 * Si vous souhaitez que la balise retourne les microdatas Aggregaterating
 * (http://schema.org/aggregateRating), il faut mettre un troisième argument, par exemple :
 * #NOTATION_ETOILE{#NOTATION_MOYENNE,'',oui}
 */
function balise_NOTATION_ETOILE($p){
	$nb = interprete_argument_balise(1,$p);
	if (!$id = interprete_argument_balise(2,$p)){
		$id = notation_calculer_id($p);
	}
	$microdatas = false;
	if($microdatas = interprete_argument_balise(3,$p)){
		$p->code = "notation_en_etoile($nb,$id,false,true)";
	}else{
		$p->code = "notation_en_etoile($nb,$id)";
	}
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
 *
 * ou designer une table qui n'est pas la table principale de la boucle
 * <BOUCLE_a(AUTEURS articles){notation article}> 
 */
 function critere_notation($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];

	$table = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);

	if (!preg_match(",^'\w+'$,",$table)){
		$table = $boucle->type_requete;
		$id_table = $boucle->id_table;
		$objet = objet_type($table);
		$primary = id_table_objet($objet);
		$group = $id_table . '.' . $primary;

		include_spip('inc/notation');
		$ponderation = notation_get_ponderation();
		$boucle->select[]= 'COUNT(notations.note) AS nombre_votes';
		$boucle->select[]= 'ROUND(AVG(notations.note),2) AS moyenne';
		$boucle->select[]= 'ROUND(AVG(notations.note)*(1-EXP(-5*COUNT(notations.note)/'.$ponderation.')),2) AS moyenne_ponderee';
		# jointure sur spip_notations
		$boucle->from['notations'] = "spip_notations";
		$boucle->from_type['notations'] = "LEFT";
		# Ordre des choses :
		# $boucle->join["surnom (as) table de liaison"] = array("surnom de la table a lier", "cle primaire de la table de liaison", "identifiant a lier", "type d'objet de l'identifiant");
		# exemple : notations = spip_documents, id_objet, id_document, notations.objet=document
		$boucle->join["notations"]= array("'$id_table'","'id_objet'","'$primary'","'notations.objet='.sql_quote('$objet')");

		$boucle->group[]=$group;
	
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
		if ($op)
			$boucle->having[]= array("'".$op."'", "'".$type."'",$op_val);
	}
	// on utilise la notation sur une table jointe
	// donc evitons d'utiliser un group-by et preferons la table spip_notations_objets
  else {
	  $table = trim($table,"'");
	  $objet = objet_type($table);
	  $primary = id_table_objet($objet);

		//Trouver une jointure
		$desc = $boucle->show;
		//Seulement si necessaire
		if (!array_key_exists($primary, $desc['field'])) {
			if (!$cle = array_search(table_objet_sql($table),$boucle->from))
				$cle = trouver_jointure_champ($primary, $boucle);
		}
		else {
			$cle = $boucle->id_table;
		}

	  $boucle->select[]= 'notations.nombre_votes AS nombre_votes';
	  $boucle->select[]= 'notations.note AS moyenne';
	  $boucle->select[]= 'notations.note_ponderee AS moyenne_ponderee';
	  $boucle->from['notations'] = "spip_notations_objets";
	  $boucle->from_type['notations'] = "LEFT";
		# Ordre des choses :
		# $boucle->join["surnom (as) table de liaison"] = array("surnom de la table a lier", "cle primaire de la table de liaison", "identifiant a lier", "type d'objet de l'identifiant");
		# exemple : notations = spip_documents, id_objet, id_document, notations.objet=document
		$boucle->join["notations"]= array("'$cle'","'id_objet'","'$primary'","'notations.objet='.sql_quote('$objet')");
  }
}


/**
 * Retourne le nombre de vote sur un objet de SPIP.
 * Necessite le critere {notation} sur la boucle
 * <BOUCLE_(ARTICLES){notation}>#NOTATION_NOMBRE_VOTES ...
 */
function balise_NOTATION_NOMBRE_VOTES_dist($p) {
	if (($_objet = interprete_argument_balise(1,$p))!==NULL
		AND ($_id = interprete_argument_balise(2,$p))!==NULL) {
		$p->code = "notation_generer_info($_id,$_objet,'nombre_votes')";
		$p->interdire_scripts = false;
		return $p;
	}
	else
		return rindex_pile($p, 'nombre_votes', 'notation');
}

/**
 * Retourne la moyenne des votes sur un objet de SPIP.
 * Necessite le critere {notation} sur la boucle
 * <BOUCLE_(ARTICLES){notation}>#NOTATION_NOMBRE_VOTES ...
 */
function balise_NOTATION_MOYENNE_dist($p) {
	if (($_objet = interprete_argument_balise(1,$p))!==NULL
		AND ($_id = interprete_argument_balise(2,$p))!==NULL) {
		$p->code = "notation_generer_info($_id,$_objet,'moyenne')";
		$p->interdire_scripts = false;
		return $p;
	}
	else
		return rindex_pile($p, 'moyenne', 'notation');
}

/**
 * Retourne la moyenne ponderee des votes sur un objet de SPIP.
 * Necessite le critere {notation} sur la boucle
 * <BOUCLE_(ARTICLES){notation}>#NOTATION_NOMBRE_VOTES ...
 */
function balise_NOTATION_MOYENNE_PONDEREE_dist($p) {
	if (($_objet = interprete_argument_balise(1,$p))!==NULL
		AND ($_id = interprete_argument_balise(2,$p))!==NULL) {
		$p->code = "notation_generer_info($_id,$_objet,'moyenne_ponderee')";
		$p->interdire_scripts = false;
		return $p;
	}
	else
		return rindex_pile($p, 'moyenne_ponderee', 'notation');
}

function notation_generer_info($id_objet,$objet,$info){
	static $infos = array();
	if (!in_array($info,array('nombre_votes','moyenne','moyenne_ponderee')))
		return '';

  if (!isset($infos[$objet][$id_objet])){
	  include_spip('inc/notation');
	  $infos[$objet][$id_objet] = sql_fetsel(
		  'note as moyenne,note_ponderee as moyenne_ponderee,nombre_votes',
		  'spip_notations_objets',
		  "objet=".sql_quote($objet)." AND id_objet=".intval($id_objet)
	  );
  }
  return $infos[$objet][$id_objet][$info];
}


?>
