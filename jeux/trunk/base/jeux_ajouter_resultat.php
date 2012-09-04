<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function jeux_ajouter_resultat($id_jeu, $resultat, $total, $resultat_long=''){
	include_spip('inc/session');
	$id_auteur = session_get('id_auteur');
	if (!$id_auteur) return;
	$requete = sql_fetsel('type_resultat', 'spip_jeux', "id_jeu=$id_jeu");
	$type_resultat = $requete['type_resultat'];
	// valeurs possibles : 'defaut', 'aucun', 'premier', 'dernier', 'meilleur', 'meilleurs', 'tous'
	if($type_resultat=='defaut')
		$type_resultat = function_exists('lire_config')?lire_config('jeux/type_resultat'):'dernier';
	// valeurs possibles : 'aucun', 'premier', 'dernier', 'meilleur', 'meilleurs', 'tous'
	if($type_resultat=='aucun') return;
	$resultat = intval($resultat);
	$total = intval($total);
	// un $id_resultat nul entraine une insertion
	// un $id_resultat non nul entraine un remplacement
	$id_resultat = 0;

	// on insere ou on remplace ?
	// si on remplace, verifier quel type de resultat : le meilleur, le premier ou le dernier
	$requete = sql_fetsel('resultat_court,total,id_resultat', 'spip_jeux_resultats', "id_jeu=$id_jeu AND id_auteur=$id_auteur");
	if (($type_resultat!='tous') && $requete) {
		// ici on va probablement remplacer le score en base...
		// valeurs possibles : 'premier', 'dernier', 'meilleur', 'meilleurs'
		$id_resultat = $requete['id_resultat'];
		switch($type_resultat) {
			case 'premier': 
				// score present, donc on part
				return;
			case 'dernier': 
				// remplacement systematique ici
				break;
			case 'meilleurs':
				// on poursuit avec 'meilleur' et on insere si ok, 
				$id_resultat = 0;
			case 'meilleur':
				$resultat_en_base = intval($requete['resultat_court']);
				$total_en_base = intval($requete['total']);
				$score_en_base = !$total_en_base?$resultat_en_base:$resultat_en_base/$total_en_base;
				$score = !$total?$resultat:$resultat/$total;
				// si pas mieux, on part
				if($score_en_base >= $score) return;
				break;
		}
	}

	// ca y est, on peut enregistrer le resultat
	jeux_ajouter_resultat_base($id_resultat, $id_jeu, $id_auteur, $resultat, $resultat_long, $total, $type_resultat);
}

function jeux_ajouter_resultat_base($id_resultat, $id_jeu, $id_auteur, $resultat, $resultat_long, $total, $type_resultat) {
	if($id_resultat) {

        sql_updateq('spip_jeux_resultats', array('resultat_court'=>$resultat, 'resultat_long'=>$resultat_long, 'total'=>$total), "id_resultat=$id_resultat");

		spip_log("Le resultat #$id_resultat de l'auteur #$id_auteur au jeu #$id_jeu a ete modifie (type '$type_resultat')",'jeux');
	} else {

        sql_insertq('spip_jeux_resultats', array('id_jeu'=>$id_jeu, 'id_auteur'=>$id_auteur, 'resultat_court'=>$resultat, 'resultat_long'=>$resultat_long, 'total'=>$total));
 
		spip_log("Le resultat de l'auteur #$id_auteur au jeu #$id_jeu a ete enregistre (type '$type_resultat')",'jeux');
	}
}
?>
