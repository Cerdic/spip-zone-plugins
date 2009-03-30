<?php
/**
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 * Ce fichier contient toutes les fonctions api permettant de manipuler tables et donnees
 * a un niveau comprehensible, sans avoir a gerer les requetes sql complexes liees
 * a la structure de donnee de F&T
 * 
 */

// on en aura besoin
include_spip('base/abstract_sql');

/** ------------------------------------------------------------------------------------------
 *  Operation sur les tables 
 * La convention de nomabe est
 * forms_verbe_objet(s)
 * 
 * les verbes sont parmis : inserer, lister, copier, purger, supprimer, modifier, instituer, ordonner
 */


/**
 * creation d'une table a partir de sa structure xml
 * le type contenu dans le xml est evnetuellement surcharge par $type si l'argument est fourni
 * $unique : ne pas creer la table si une table du meme type existe deja
 *
 * @param string $structure_xml : nom du fichier xml
 * @param string $type
 * @param unknown_type $unique
 * @param unknown_type $c
 * @return unknown
 */
function forms_inserer_table($structure_xml,$type=NULL, $unique = true, $c=NULL){
	include_spip('inc/xml');

	$xml = spip_xml_load($structure_xml);
	foreach($xml as $k1=>$forms)
		foreach($forms as $k2=>$formscont)
			foreach($formscont as $k3=>$form)
				foreach($form as $k4=>$formcont)
					foreach($formcont as $prop=>$datas)
					if ($prop=='type_form'){
						if ($type)
							$xml[$k1][$k2][$k3][$k4][$prop] = array($type);
						else 
							$type = trim(applatit_arbre($datas));
					}

	if (!$type) return;
	if ($unique
	  AND sql_countsel("spip_forms","type_form=".sql_quote($type)))
			return;
	
	// ok on peut creer la table
	$snippets_forms_importer = charger_fonction('importer','snippets/forms');
	$id_form = $snippets_forms_importer(0,$xml);
	if ($c!==NULL){
		include_spip('forms_crayons');
		form_revision($id_form,$c);
	}
	return $id_form;
}


/**
 * Lister les id_form des tables d'un type donne
 *
 * @param string $type
 * @return array
 */
function forms_lister_tables($type){
	static $liste = array();
	if (is_array($type) && count($type)) {
		$l = array();
		foreach($type as $t)
			$l = array_merge($l,forms_lister_tables($t));
		return $l;
	}
	if (!isset($liste[$type]))
		$liste[$type] = array_map('reset',sql_allfetsel("id_form","spip_forms","type_form=".sql_quote($type)));

	return $liste[$type];
}


/**
 * Copier une table (sa structure uniquement)
 *
 * @param int/string $type_ou_id
 * @return int/array
 */
function forms_copier_tables($type_ou_id){
	if (!$duplique = intval($type_ou_id) OR !is_numeric($type_ou_id)){
		$liste = forms_lister_tables($type_ou_id);
		$id_forms = array();
		foreach($liste as $id)
			$id_forms[] = forms_copier_tables($id);
		if (count($id_forms)==1) $id_forms = reset($id_forms);
		return $id_forms;
	}
	include_spip('base/abstract_sql');
	// creation
	if ($valeurs = sql_fetsel("*","spip_forms","id_form=".intval($duplique))) {
		$valeurs['titre'] = _T("forms:formulaires_copie",array('nom'=>$valeurs['titre']));
		unset($valeurs['id_form']);
		if ($id_form = sql_insertq('spip_forms',$valeurs)){
			$rows = sql_allfetsel("*","spip_forms_champs","id_form=".intval($duplique));
			foreach($rows as $valeurs) {
				$valeurs['id_form'] = $id_form;
				sql_insertq("spip_forms_champs",$valeurs);
			}
			$rows = sql_allfetsel("*","spip_forms_champs_choix","id_form=".intval($duplique));
			foreach($rows as $valeurs) {
				$valeurs['id_form'] = $id_form;
				sql_insertq("spip_forms_champs_choix",$valeurs);
			}
		}
	}
	return $id_form;
}

/**
 * Vider une ou des tables : passer toutes ses donnees en 'poubelle'
 *
 * @param int/string $type_ou_id
 */
function forms_purger_tables($type_ou_id){
	if (!$id_form = intval($type_ou_id) OR !is_numeric($type_ou_id)){
		$liste = forms_lister_tables($type_ou_id);
		foreach($liste as $id)
			forms_vider_tables($id);
		return;
	}
	forms_supprimer_donnee($id_form,'tout');
}

/**
 * Supprimer physiquement une ou des tables
 *
 * @param unknown_type $type_ou_id
 */
function forms_supprimer_tables($type_ou_id){
	if (!$id_form = intval($type_ou_id) OR !is_numeric($type_ou_id)){
		$liste = forms_lister_tables($type_ou_id);
		foreach($liste as $id)
			forms_supprimer_tables($id);
		return;
	}
	$rows = sql_allfetsel("id_donnee","spip_forms_donnees","id_form=".intval($id_form));
	foreach($rows as $row){
		sql_delete("spip_forms_donnees_champs","id_donnee=".intval($row['id_donnee']));
		sql_delete("spip_forms_donnees_articles","id_donnee=".intval($row['id_donnee']));
		sql_delete("spip_forms_donnees_rubriques","id_donnee=".intval($row['id_donnee']));
	}
	sql_delete("spip_forms_donnees","id_form=".intval($id_form));
	sql_delete("spip_forms_champs_choix","id_form=".intval($id_form));
	sql_delete("spip_forms_champs","id_form=".intval($id_form));
	sql_delete("spip_forms","id_form=".intval($id_form));
	sql_delete("spip_forms_articles","id_form=".intval($id_form));
}

/**
 * Inserer un champ
 *
 * @param int $id_form
 * @param string $type
 * @param string $titre
 * @param array $c
 * @param string $champ : nom eventuel du champ
 * @return string
 */
function forms_inserer_champ($id_form,$type,$titre,$c=NULL,$champ=""){
	include_spip('inc/forms_edit');
	$champ = forms_insere_nouveau_champ($id_form,$type,$titre,$champ);
	if ($c!==NULL){
		include_spip('forms_crayons');
		forms_champ_revision("$id_form-$champ",$c);
	}
	return $champ;
}

/* Operation sur les donnees -------------------------------*/

/**
 * Inserer une nouvelle donnee
 *
 * @param int $id_form
 * @param array $c
 * @param int $rang
 * @return array
 */
function forms_inserer_donnee($id_form,$c = NULL, $rang=NULL){
	include_spip('inc/autoriser');
	if (!autoriser('creer','donnee',0,NULL,array('id_form'=>$id_form)))
		return array(0,_L("droits insuffisants pour creer une donnee dans table $id_form"));
	include_spip('inc/forms');
	$new = 0;
	$erreur = array();
	forms_enregistrer_reponse_formulaire($id_form, $new, $erreur, $reponse, '', '' , $c, $rang);
	return array($new,$erreur);
}

/**
 * Supprimer une donnee (ie statut=poubelle)
 * 
 * @param int $id_form
 * @param int $id_donnee
 * @return unknown
 */
function forms_supprimer_donnee($id_form,$id_donnee){
	if (intval($id_donnee)==0 AND $id_donnee!=='tout') return false; // erreur
	$id_donnee = intval($id_donnee);
	include_spip('inc/autoriser');
	if (!autoriser('supprimer','donnee',$id_donnee,NULL,array('id_form'=>$id_form))){
		if ($id_donnee) return _L("droits insuffisants pour supprimer la donnee $id_donnee");
		$res = sql_select("id_donnee","spip_forms_donnees","id_form=".intval($id_form));
		while ($row = sql_fetch($res)){
			if (autoriser('supprimer','donnee',$row['id_donnee'],NULL,array('id_form'=>$id_form)))
				sql_updateq("spip_forms_donnees",array('statut'=>'poubelle','bgch'=>0,'bdte'=>0,'niveau'=>0),"id_form=".intval($id_form)." AND id_donnee=".intval($row['id_donnee']));
		}
		return true;
	}
	$where = intval($id_donnee) ? " AND id_donnee=".intval($id_donnee) : "";
	return sql_updateq("spip_forms_donnees",array('statut'=>'poubelle','bgch'=>0,'bdte'=>0,'niveau'=>0),"id_form=".intval($id_form).$where);
}

/**
 * Modifier une donnee
 *
 * @param int $id_donnee
 * @param array $c
 * @return array : les erreurs eventuelles
 */
function forms_modifier_donnee($id_donnee,$c = NULL){
	include_spip('action/forms_editer_donnee');
	return forms_revision_donnee($id_donnee,$c);
}

/**
 * Changer le statut d'une donnee
 *
 * @param int $id_donnee
 * @param string $statut
 */
function forms_instituer_donnee($id_donnee,$statut){
	sql_updateq("spip_forms_donnees",array("statut"=>$statut),"id_donnee=".intval($id_donnee));
}

/**
 * Changer le rang d'une donnee
 *
 * @param unknown_type $id_donnee
 * @param unknown_type $rang_nouv
 * @return unknown
 */
function forms_ordonner_donnee($id_donnee,$rang_nouv){
	$rang_min = $rang_max = 1;
	// recuperer le rang et l'id_form de la donnee modifiee
	if (!$row = sql_fetsel("id_form,rang","spip_forms_donnees","id_donnee=".intval($id_donnee))) return;
	$rang = $row['rang'];
	$id_form = $row['id_form'];

	// recuperer le min et le max des rangs en cours
	if ($row = sql_fetsel("min(rang) AS rang_min, max(rang) AS rang_max","spip_forms_donnees","id_form=".intval($id_form))){
		$rang_min = $row['rang_min'];
		$rang_max = $row['rang_max'];
	}

	// verifier si des donnees sont pas sans rang et les ramasser
	$rows = sql_allfetsel("id_donnee, rang","spip_forms_donnees","(rang=NULL OR rang=0) AND id_form=".intval($id_form),"","id_donnee");
	foreach($rows as $row){
		$rang_max++;
		sql_updateq("spip_forms_donnees",array('rang'=>$rang_max),"id_donnee=".intval($row['id_donnee']));
	}
	// borner le rang
	include_spip('action/forms_editer_donnee');
	if ($rang_nouv==0) $rang_nouv = forms_donnee_prochain_rang($id_form);
	$rang_nouv = min(max($rang_nouv,$rang_min),$rang_max);
	if ($rang_nouv>$rang) $rang_nouv++; // il faut se decaler d'un car on est devant actuellement
	$rang_nouv = min($rang_nouv,forms_donnee_prochain_rang($id_form));

	// incrementer tous ceux dont le rang est superieur a la cible pour faire une place
	$ok = sql_update("spip_forms_donnees",array('rang'=>'rang+1'),"id_form=".intval($id_form)." AND rang>=".intval($rang_nouv));
	if (!$ok) return $rang;
	// mettre a jour le rang de l'element demande
	$ok = sql_updateq("spip_forms_donnees",array('rang'=>$rang_nouv),"id_donnee=".intval($id_donnee));
	if (!$ok) return $rang;

	// decrementer tous ceux dont le rang est superieur a l'ancien pour recuperer la place
	sql_update("spip_forms_donnees",array("rang"=>"rang-1"),"id_form=".intval($id_form)." AND rang>".intval($rang));
	if (!$row = sql_fetsel("id_form,rang","spip_forms_donnees","id_donnee=".intval($id_donnee)))
		return $rang_nouv;
	else 
		return $row['rang'];
}

/**
 * Rechercher une donnee
 *
 * @param string $recherche
 * @param int $id_form
 * @param string $champ
 * @param array $sous_ensemble
 * @return array
 */
function forms_rechercher_donnee($recherche,$id_form=0,$champ=NULL,$sous_ensemble=NULL){
	$liste = array();
	$in = "";
	if (is_array($sous_ensemble))
		$in = sql_in('dc.id_donnee',implode(',',array_map('intval',$sous_ensemble)));

	return array_map('reset',sql_allfetsel("dc.id_donnee",
	  "spip_forms_donnees_champs AS dc" . ($id_form?" LEFT JOIN spip_forms_donnees AS d ON dc.id_donnee=d.id_donnee":""),
	  "dc.valeur LIKE ".sql_quote($recherche)
	    . ($id_form?" AND d.id_form=".intval($id_form):"")
	    . ($in?" AND $in":"")
	    . ($champ?" AND dc.champ=".sql_quote($champ):"")
	  ));
}

/**
 * Recuperer les informations importantes d'une donnee : id_form, titre du form, type du form,
 * et la premiere valeur de chaque champ (specifiant par defaut)
 * la fonction ne renvoie pas le contenu exhaustif d'une donnee, 
 * mais de quoi la resumer dans un affichage reduit
 *
 * @param int $id_donnee
 * @param bool $specifiant
 * @param bool $linkable
 * @return array
 */
function forms_informer_donnee($id_donnee,$specifiant=true,$linkable=false){
	include_spip('inc/forms');
	list($id_form,$titreform,$type_form,$t) = forms_liste_decrit_donnee($id_donnee,$specifiant,$linkable);
	if (!count($t) && $specifiant)
		list($id_form,$titreform,$type_form,$t) = forms_liste_decrit_donnee($id_donnee, false,$linkable);
	if (!count($t) && !$id_form) { 
		// verifier qu'une donnee vide n'existe pas suite a enregistrement errone..
		if ($row = sql_getfetsel(
		  "f.titre AS titreform,f.id_form,f.type_form",
		  "spip_forms_donnees AS d JOIN spip_forms AS f ON f.id_form=d.id_form",
		  "d.id_donnee=".intval($id_donnee))){
			
			$titreform = $row['titreform'];
			$id_form = $row['id_form'];
			$type_form = $row['type_form'];
		}
	}
	return array($id_form,$titreform,$type_form,$t);
}

/**
 * Lister les donnees liees a une donnee
 *
 * @param int $id_donnee
 * @param string $type_form_lie
 * @return array
 */
function forms_lister_donnees_liees($id_donnee,$type_form_lie=NULL){
	$in_liste = "";
	if ($type_form_lie)
		$in_liste = sql_in('id_form',forms_lister_tables($type_form_lie));
	$rows = sql_allfetsel("id_donnee,id_donnee_liee","spip_forms_donnees_donnees","id_donnee=".intval($id_donnee)." OR id_donnee_liee=".intval($id_donnee));
	$valeurs = array();
	foreach($rows as $row){
		$liee = $row['id_donnee']+$row['id_donnee_liee']-$id_donnee;
		if (!$in_liste
		  OR sql_getfetsel("id_donnee","spip_forms_donnees","id_donnee=".intval($liee)." AND $in_liste"))
			$valeurs[] = $liee;
	}
	return $valeurs;
}

/**
 * Supprimer le lien entre deux donnees
 *
 * @param int $id_donnee
 * @param int $id_donnee_liee
 * @param unknown_type $type_form_lie
 */
function forms_separer_donnees_liees($id_donnee_1,$id_donnee_2=0,$type_form_lie = ""){
	if ($id_donnee_2!=0){
		sql_delete("spip_forms_donnees_donnees",
		  "(id_donnee=".intval($id_donnee_1)." AND id_donnee_liee=".intval($id_donnee_2).")"
		  ."OR (id_donnee_liee=".intval($id_donnee_1)." AND id_donnee=".intval($id_donnee_2).")"
		  );
	}
	// sinon supprimer toutes les donnees liees d'un type donnee, en une seule operation
	elseif(count($liste = forms_lister_donnees_liees($id_donnee_1,$type_form_lie))) {
		sql_delete("spip_forms_donnees_donnees",
		  "(id_donnee=".intval($id_donnee_1)." AND ".sql_in("id_donnee_liee",$liste).")"
		  ."OR (id_donnee_liee=".intval($id_donnee_1)." AND ".sql_in("id_donnee",$liste).")"
		  );
	}
}

function forms_enumerer_les_valeurs_champs($id_form, $id_donnee, $champ, $separateur=",",$etoile=false, $traduit=true){
	include_spip('forms_fonctions');
	if (is_array($champ))
		foreach($champ as $k=>$ch)
			$champ[$k] = forms_calcule_les_valeurs('forms_donnees_champs', $id_donnee, $ch, $id_form, $separateur,$etoile,$traduit);
	else
		$champ = forms_calcule_les_valeurs('forms_donnees_champs', $id_donnee, $champ, $id_form, $separateur,$etoile,$traduit);
	return $champ;
}


/* Operation sur les donnees arborescentes intervallaires -------------------------------*/

/**
 * Inserer une donnee a une position dans l'arbre
 *
 * @param int $id_form
 * @param int $id_parent : la donnee 'parente'
 * @param string $position : la relation avec le parent, fils_cadet, fils_aine, grand_frere, petit_frere, pere
 * @param array $c
 * @return array($id_donnee,$erreur)
 */
function forms_arbre_inserer_donnee($id_form,$id_parent,$position="fils_cadet",$c=NULL){
	if (!$id_parent>0){
		if (is_null(sql_getfetsel("id_donnee","spip_forms_donnees","id_form=".intval($id_form)." AND statut!='poubelle'","","","0,1"))){
		  // pas d'elements existants, c'est la racine, on l'insere toujours
			if ($position=='fils_aine' OR $position=='fils_cadet'){
				spip_log("Insertion impossible dans un arbre pour un fils sans pere dans table $id_form");
				return array(0,_L("Insertion impossible dans un arbre pour un fils sans pere dans table $id_form"));
			}
			// premiere insertion
				return forms_creer_donnee($id_form,$c,array('niveau'=>0,'bgch'=>1,'bdte'=>2));
		}
		else {
			// Insertion d'un collateral : il faut preciser le 'parent' !
			spip_log("Insertion impossible dans un arbre pour un collat�ral sans precision du parent dans table $id_form");
			return array(0,_L("Insertion impossible dans un arbre pour un collat�ral sans precision du parent dans table $id_form"));
		}
	}
	// Le parent existe toujours ?
	if (!$rowp=sql_fetsel("*","spip_forms_donnees","id_form=".intval($id_form)." AND id_donnee=".intval($id_parent)." AND statut!='poubelle'")){
		spip_log("Insertion impossible, le parent $id_parent n'existe plus dans table $id_form");
		return array(0,_L("Insertion impossible, le parent $id_parent n'existe plus dans table $id_form"));
	}
	
	// insertion d'un pere
	if ($position == 'pere'){
		if (
		  // Decalage de l'ensemble colateral droit
		  sql_update("spip_forms_donnees",array("bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bdte>".intval($rowp['bdte'])." AND bgch<=".intval($rowp['bdte']))
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch+2","bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bgch>".intval($rowp['bdte']))
			// Decalalage ensemble vise vers le bas
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch+1","bdte"=>"bdte+1","niveau"=>"niveau+1"),"id_form=".intval($id_form)." AND bgch>=".intval($rowp['bgch'])." AND bdte<=".intval($rowp['bdte']))
		)
			// Insertion du nouveau pere
			return forms_creer_donnee($id_form,$c,array('niveau'=>$rowp['niveau'],'bgch'=>$rowp['bgch'],'bdte'=>$rowp['bdte']+2));
	}
	// Insertion d'un grand frere
	elseif ($position == 'grand_frere'){
		if (
		  // Decalage de l'ensemble colateral droit
		  sql_update("spip_forms_donnees",array("bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bdte>".intval($rowp['bgch'])." AND bgch<".intval($rowp['bgch']))
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch+2","bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bgch>=".intval($rowp['bgch']))
		  )
			return forms_creer_donnee($id_form,$c,array('niveau'=>$rowp['niveau'],'bgch'=>$rowp['bgch'],'bdte'=>$rowp['bgch']+1));
	}
	// Insertion d'un petit frere
	elseif ($position == 'petit_frere'){
		if (
		  // Decalage de l'ensemble colateral droit
		  sql_update("spip_forms_donnees",array("bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bdte>".intval($rowp['bdte'])." AND bgch<".intval($rowp['bdte']))
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch+2","bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bgch>=".intval($rowp['bdte']))
		  )
			return forms_creer_donnee($id_form,$c,array('niveau'=>$rowp['niveau'],'bgch'=>$rowp['bdte']+1,'bdte'=>$rowp['bdte']+2));
	}
	// Insertion d'un fils aine
	elseif ($position == 'fils_aine'){
		if (
		  // Decalage de l'ensemble colateral droit
		  sql_update("spip_forms_donnees",array("bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bdte>".intval($rowp['bgch'])." AND bgch<=".intval($rowp['bgch']))
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch+2","bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bgch>".intval($rowp['bgch']))
		  )
			return forms_creer_donnee($id_form,$c,array('niveau'=>$rowp['niveau']+1,'bgch'=>$rowp['bgch']+1,'bdte'=>$rowp['bgch']+2));
	}
	// Insertion d'un fils aine
	elseif ($position == 'fils_cadet'){
		if (
		  // Decalage de l'ensemble colateral droit
		  sql_update("spip_forms_donnees",array("bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bdte>=".intval($rowp['bdte'])." AND bgch<=".intval($rowp['bdte']))
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch+2","bdte"=>"bdte+2"),"id_form=".intval($id_form)." AND bgch>".intval($rowp['bdte']))
		  )
			return forms_creer_donnee($id_form,$c,array('niveau'=>$rowp['niveau']+1,'bgch'=>$rowp['bdte'],'bdte'=>$rowp['bdte']+1));
	}
	spip_log("Operation inconnue insertion en position $position dans table $id_form");
	return array(0,_L("Operation inconnue insertion en position $position dans table $id_form"));
}

/**
 * Supprimer une donnee de l'arbre
 *
 * @param int $id_form
 * @param int $id_donnee
 * @param bool $recursif : supprimer tout le sous arbre (true) ou juste la donnee (false)
 * @return bool
 */
function forms_arbre_supprimer_donnee($id_form,$id_donnee,$recursif=true){
	if (!($row = sql_fetsel("*","spip_forms_donnees","id_form=".intval($id_form)." AND id_donnee=".intval($id_donnee))))
		return false;
	if ($recursif){
		// OUI ! tout le sous arbre doit etre supprime
		$delta = $row['bdte']-$row['bgch']+1;
		$donnees = sql_allfetsel("id_donnee","spip_forms_donnees","id_form=".intval($id_form)." AND bgch>=".intval($row['bgch'])." AND bdte<=".intval($row['bdte']));
		$ok = true;
		foreach($donnees as $row2)
			$ok = $ok && forms_supprimer_donnee($id_form,$row2['id_donnee']);
		
		if (
			sql_update("spip_forms_donnees",array("bgch"=>"bgch-$delta","bdte"=>"bdte-$delta"),"id_form=".intval($id_form)." AND bgch>".intval($row['bdte']))
			AND sql_update("spip_forms_donnees",array("bdte"=>"bdte-$delta"),"id_form=".intval($id_form)." AND bdte>".intval($row['bdte'])." AND bgch<=".intval($row['bdte']))
			)
			return $ok;
		return false;
	}
	else {
		// NON ! on ne supprime que l'element
		if (
		  forms_supprimer_donnee($id_form,$id_donnee)
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch-1","bdte"=>"bdte-1","niveau"=>"niveau-1"),"id_form=".intval($id_form)." AND bdte<".intval($row['bdte'])." AND bgch>".intval($row['bgch']))
		  AND sql_update("spip_forms_donnees",array("bgch"=>"bgch-2","bdte"=>"bdte-2"),"id_form=".intval($id_form)." AND bgch>".intval($row['bdte']))
		  AND sql_update("spip_forms_donnees",array("bdte"=>"bdte-2"),"id_form=".intval($id_form)." AND bdte>".intval($row['bdte'])." AND bgch<=".intval($row['bdte']))
		  )
			return true;
		return false;
	}
}

/**
 * Lister les donnees en relation avec le parent
 *
 * @param int $id_form
 * @param int $id_parent
 * @param string $position : enfant, branche, grand_frere, petit_frere, parent, hierarchie
 * @return array
 */
function forms_arbre_lister_relations($id_form,$id_parent,$position="enfant"){
	$liste = array();
	if ($id_parent){
		if (!$row = sql_fetsel("id_donnee,niveau,bgch,bdte","spip_forms_donnees","id_donnee=".intval($id_parent)." AND id_form=".intval($id_form))) 
			return $liste;
		$niveau = $row['niveau'];
		$bgch = $row['bgch'];
		$bdte = $row['bdte'];
		
		$where = "";
		if ($position=='enfant' || $position=='branche') {
			$where = " AND bgch>".intval($bgch)." AND bdte<".intval($bdte)
			  . ($position=='enfant'?" AND niveau=".intval($niveau+1):"");
		}
		elseif ($position=='grand_frere') {
			$where = " AND bdte<".intval($bgch)
			  . " AND niveau=".intval($niveau);
		}
		elseif ($position=='petit_frere') {
			$where = " AND bgch>".intval($bdte)
			  . " AND niveau=".intval($niveau);
		}
		elseif ($position=='parent' || position=='hierarchie') {
			$where = " AND bgch<".intval($bgch)." AND bdte>".intval($bdte)
			  . ($position=='parent'?" AND niveau=".intval($niveau-1):"");
		}

	}
	else {
		if ($position!='enfant' AND $position!='branche')
			return $liste;
		$where = ($position=='enfant'?" AND niveau=1":"");
	}
	$liste = array_map('reset',sql_allfetsel("id_donnee","spip_forms_donnees","id_form=".intval($id_form) . $where,"","bgch"));
	return $liste;
}

?>