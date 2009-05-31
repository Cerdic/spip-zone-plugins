<?php

/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	include_spip('base/forms_temporaire');
	
	//
	// Afficher le diagramme de resultats d'un sondage
	//

	function Forms_afficher_reponses_sondage($id_form) {
		$r = '';
		$id_form = intval($id_form);
	
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if (!$row = spip_fetch_array($result)) return '';
	
		$sondage = $row['sondage'];
		$structure = unserialize($row['structure']);
		$champs = array();
		foreach ($structure as $index => $t) {
			if ($t['type'] != 'select' && $t['type'] != 'multiple' && $t['type'] != 'mot')
				continue;
			$champs[] = $t;
		}
	
		$r .= "<div class='spip_sondage'>\n";
	
		// Compter les reponses pour chaque champ de type choix (unique / multiple / mot-cle)
		foreach ($champs as $t) {
			// On recompte le nombre total de reponses reelles 
			// car les champs ne sont pas forcement obligatoires
			$query = "SELECT COUNT(DISTINCT c.id_reponse) AS num ".
				"FROM spip_reponses AS r LEFT JOIN spip_reponses_champs AS c USING (id_reponse) ".
				"WHERE r.id_form=$id_form AND r.statut='valide' AND c.champ='".addslashes($t['code'])."'";
			$result = spip_query($query);
			list ($total_reponses) = spip_fetch_array($result,SPIP_NUM);
			if (!$total_reponses) continue;
	
			// Construire la liste des valeurs autorisees pour le champ
			if ($t['type'] != 'mot')
				$liste = $t['type_ext'];
			else {
				$id_groupe = intval($t['type_ext']['id_groupe']);
				$query = "SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe ORDER BY titre";
				$result = spip_query($query);
				$liste = array();
				while ($row = spip_fetch_array($result)) {
					$id_mot = $row['id_mot'];
					$titre = $row['titre'];
					$liste[$id_mot] = $titre;
				}
			}
	
			// Nombre de reponses pour chaque valeur autorisee
			$query = "SELECT c.valeur, COUNT(*) AS num ".
				"FROM spip_reponses AS r LEFT JOIN spip_reponses_champs AS c USING (id_reponse) ".
				"WHERE r.id_form=$id_form AND r.statut='valide' ".
				"AND c.champ='".addslashes($t['code'])."' GROUP BY c.valeur";
			$result = spip_query($query);
			$chiffres = array();
			// Stocker pour regurgiter dans l'ordre
			while ($row = spip_fetch_array($result)) {
				$chiffres[$row['valeur']] = $row['num'];
			}
			
			// Afficher les resultats
			$r .= "<strong>".propre($t['nom'])." :</strong><br />\n";
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
	
		$query = "SELECT COUNT(*) AS num FROM spip_reponses ".
			"WHERE id_form=$id_form AND statut='valide'";
		$result = spip_query($query);
		list($num) = spip_fetch_array($result,SPIP_NUM);
		$r .= "<strong>"._T("forms:total_votes")." : $num</strong>";
	
		$r .= "</div>\n";
		
		return $r;
	}


function balise_RESULTATS_SONDAGE($p) {
	$_id_form = champ_sql('id_form', $p);

	$p->code = "Forms_afficher_reponses_sondage(" . $_id_form . ")";
	$p->statut = 'html';
	return $p;
}

/*function boucle_FORMS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_forms";
	$boucle->hash = '
	// CREER les table temporaire forms_champs et forms_champs_choix
	forms_creer_tables_temporaires_boucles();
';
	return calculer_boucle($id_boucle, $boucles); 
}*/

function forms_valeur($tableserialisee,$cle,$defaut=''){
	$t = unserialize($tableserialisee);
	return isset($t[$cle])?$t[$cle]:$defaut;
}

?>