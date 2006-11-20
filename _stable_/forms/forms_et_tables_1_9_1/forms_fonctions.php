<?php

/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	// compatibilite 1.9.1
	if ($GLOBALS['spip_version_code']<1.92 && !function_exists('concat')){
		// Concatener des chaines
		// #TEXTE|concat{texte1,texte2,...}
		// http://doc.spip.org/@concat
		function concat(){
			$args = func_get_args();
			return join('', $args);
		}	
	}

	include_spip('base/forms');
	//
	// <BOUCLE(FORMS)>
	//
	/*function boucle_FORMS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms";
	
		if (!isset($boucle->modificateur['tout'])){
			$boucle->where[]= array("'='", "'$id_table.public'", "'oui'");
			$boucle->group[] = $boucle->id_table . '.champ'; // ?  
		}
		return calculer_boucle($id_boucle, $boucles); 
	}*/
	
	//
	// <BOUCLE(FORMS_DONNEES)>
	//
	function boucle_FORMS_DONNEES_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_donnees";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'$id_table.confirmation'", "'\"valide\"'");
		if (!$boucle->statut && !isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
	
		return calculer_boucle($id_boucle, $boucles); 
	}

	//
	// <BOUCLE(FORMS_CHAMPS)>
	//
	function boucle_FORMS_CHAMPS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_champs";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout){
			$boucle->where[]= array("'='", "'$id_table.public'", "'\"oui\"'");
		}
	
		return calculer_boucle($id_boucle, $boucles); 
	}
	
	//
	// <BOUCLE(FORMS_DONNEES_CHAMPS)>
	//
	function boucle_FORMS_DONNEES_CHAMPS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_donnees_champs";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout){
			$boucle->from["champs"] =  "spip_forms_champs";
			$boucle->from["donnees"] =  "spip_forms_donnees";
			$boucle->where[]= array("'='", "'$id_table.id_donnee'", "'donnees.id_donnee'");
			$boucle->where[]= array("'='", "'$id_table.champ'", "'champs.champ'");
			$boucle->where[]= array("'='", "'donnees.id_form'", "'champs.id_form'");
			$boucle->where[]= array("'='", "'champs.public'", "'\"oui\"'");
			$boucle->group[] = $boucle->id_table . '.champ'; // ?  
		}
		if (!$boucle->statut && !isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'donnees.statut'", "'\"publie\"'");

		return calculer_boucle($id_boucle, $boucles); 
	}
	
	//
	// Afficher le diagramme de resultats d'un sondage
	//

	function Forms_afficher_reponses_sondage($id_form) {
		$r = '';
		$id_form = intval($id_form);
	
		$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
		if (!$row = spip_fetch_array($result)) return '';
		$type_form = $row['type_form'];
	
		$r .= "<div class='spip_sondage'>\n";
		
		$res2 = spip_query("SELECT * FROM spip_forms_champs AS champs
		WHERE id_form="._q($id_form)." AND type IN ('select','multiple','mot') ORDER BY cle");
		while ($row2 = spip_fetch_array($res2)) {
			// On recompte le nombre total de reponses reelles 
			// car les champs ne sont pas forcement obligatoires
			$row3=spip_fetch_array(spip_query("SELECT COUNT(DISTINCT c.id_donnee) AS num ".
				"FROM spip_forms_donnees AS r LEFT JOIN spip_forms_donnees_champs AS c USING (id_donnee) ".
				"WHERE r.id_form=$id_form AND r.confirmation='valide' AND r.statut='publie' AND c.champ="._q($row2['champ'])));
			if (!$row3 OR !($total_reponses=$row3['num']))
				continue;
	
			// Construire la liste des valeurs autorisees pour le champ
			$liste = array();
			if ($row2['type'] != 'mot'){
				$res3 = spip_query("SELECT * FROM spip_forms_champs_choix WHERE champ="._q($row2['champ']));
				while ($row3=spip_fetch_array($res3))
					$liste[$row3['choix']] = $row3['titre'];
			}
			else {
				$id_groupe = intval($row2['extra_info']);
				$res3 = spip_query("SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe ORDER BY titre");
				while ($row3 = spip_fetch_array($res3))
					$liste[$row3['id_mot']] = $row3['titre'];
			}
	
			// Nombre de reponses pour chaque valeur autorisee
			$query = "SELECT c.valeur, COUNT(*) AS num ".
				"FROM spip_forms_donnees AS r LEFT JOIN spip_forms_donnees_champs AS c USING (id_donnee) ".
				"WHERE r.id_form=$id_form AND r.confirmation='valide' AND r.statut='publie' ".
				"AND c.champ="._q($row2['champ'])." GROUP BY c.valeur";
			$result = spip_query($query);
			$chiffres = array();
			// Stocker pour regurgiter dans l'ordre
			while ($row = spip_fetch_array($result)) {
				$chiffres[$row['valeur']] = $row['num'];
			}
			
			// Afficher les resultats
			$r .= "<strong>".propre($row2['nom'])." :</strong><br />\n";
			$r .= "<div class='sondage_table'>";
			foreach ($liste as $valeur => $nom) {
				$r .= "<div class='sondage_ligne'>";
				$n = $chiffres[$valeur];
				$taux = floor($n * 100.0 / $total_reponses);
				$r .= "<div class='ligne_nom'>".typo($nom)." </div>";
				$r .= "<div style='width: 60%;'><div class='ligne_barre' style='width: $taux%;'></div></div>";
				$r .= "<div class='ligne_chiffres'>$n ($taux&nbsp;%)</div>";
				$r .= "</div>\n";
			}
			$r .= "</div>\n";
			$r .= "<br />\n";
		}
	
		$query = "SELECT COUNT(*) AS num FROM spip_forms_donnees ".
			"WHERE id_form=$id_form AND confirmation='valide' AND r.statut='publie'";
		$result = spip_query($query);
		list($num) = spip_fetch_array($result,SPIP_NUM);
		$r .= "<strong>"._T("forms:total_votes")." : $num</strong>";
	
		$r .= "</div>\n";
		
		return $r;
	}
function wrap_split($wrap){
	$wrap_start="";
	$wrap_end="";
	if (preg_match(",<([^>]*)>,Ui",$wrap,$regs)){
		array_shift($regs);
		foreach($regs as $w){
			if ($w{0}=='/'){
			 //$wrap_end .= "<$w>";
			}
			else {
				if ($w{strlen($w)-1}=='/')
					$w = strlen($w)-1;
				$wrap_start .= "<$w>";
				$w = explode(" ",$w);
				if (is_array($w)) $w = $w[0];
				$wrap_end = "</$w>" . $wrap_end;
			}
		}
	}
	return array($wrap_start,$wrap_end);
}

function balise_RESULTATS_SONDAGE($p) {
	$_id_form = champ_sql('id_form', $p);

	$p->code = "Forms_afficher_reponses_sondage(" . $_id_form . ")";
	$p->statut = 'html';
	return $p;
}

function forms_valeur($tableserialisee,$cle,$defaut=''){
	$t = unserialize($tableserialisee);
	return isset($t[$cle])?$t[$cle]:$defaut;
}

// http://doc.spip.org/@puce_statut_article
function forms_puce_statut_donnee($id, $statut, $id_form, $ajax = false) {
	include_spip('inc/instituer_forms_donnee');
	return puce_statut_donnee($id,$statut,$id_form,$ajax);
}
?>