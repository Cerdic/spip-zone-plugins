<?php

include_spip('inc/utils');

// texte figurant par défaut dans la zone de saisie 
define("TXT_A_RECHERCHER", "votre recherche");

// taille du contexte max affiché avant et après chaque extrait trouvé
define("CONTEXTE_NB_CHARS", 20);

function remplace_txt($entite, $id, $champs, $txt_search, $remplace, $remplace_par="") {
	$len = intval(CONTEXTE_NB_CHARS);
	$len_moins_un = max($len-1,0);

	$nom_table = "spip_".$entite;
	if($entite=="article" || $entite=="rubrique" || $entite=="auteur")
		$nom_table .= "s";

	$retour = "";
	$select = "$id, titre";

	foreach ($champs as $i => $nom_champ) {
		$select .= ', '.$nom_champ;
	}

	$pattern = "/(^.{0,$len_moins_un}|.{".$len.'})'.preg_quote($txt_search, '/')."(.{0,$len_moins_un}$|.{".$len.'})/s';

	if($resultats = sql_select($select, $nom_table)) {
		while($res = sql_fetch($resultats)) {
			$nouvelles_valeurs = array();
			$found = false;
			$details = '';
			// on parcourt tous les champs
			foreach ($champs as $i => $nom_champ) {
				$nb = 0;
				
				if ($len_moins_un and preg_match_all($pattern, $res[$nom_champ], $matches))
					$details .= '..'.implode ('...', $matches[0]).'.. ';
				$nouvelles_valeurs[$nom_champ] = str_replace($txt_search, $remplace_par, $res[$nom_champ], $nb);
				if($nb>0) 
					$found = true;
			}

			if($found) {
				$url = generer_url_entite($res[$id], $entite);
				if ($entite =='forum')
					$url = str_replace ('debut_id_forum', 'id_forum', $url);
				$retour .= "Texte trouv&eacute; dans <a href='$url'>$entite ".$res[$id]."</a> ({$res['titre']})<br>";
				// Mise à jour d'un champ de la table
				if($remplace) 
					sql_updateq($nom_table, $nouvelles_valeurs, $id."=".intval($res[$id]));
				elseif ($len_moins_un)
					$retour .= "<small style='margin-left: 2em'><small>".htmlspecialchars($details)."</small></small><br>";
			}
		}
	}
	return $retour;
}

function formulaires_rechercher_remplacer_charger_dist(){
	$valeurs = array('txt_search'=> TXT_A_RECHERCHER,
		'remplace'=>'',
		'remplacer_par'=>'');

	return $valeurs;
}

function formulaires_rechercher_remplacer_verifier_dist(){
	$erreurs = array();

	if(!_request('txt_search'))
		$erreurs['txt_search'] = 'Saisie obligatoire';

	if(count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';

	return $erreurs;
}

function formulaires_rechercher_remplacer_traiter_dist(){	
	$msg = "";

	$msg .= remplace_txt('article', 'id_article', array("surtitre","titre","soustitre","descriptif","texte","chapo","ps"), _request('txt_search'), _request('remplace'), _request('remplacer_par'));
	$msg .= remplace_txt('rubrique', 'id_rubrique', array("titre","descriptif","texte"), _request('txt_search'), _request('remplace'), _request('remplacer_par'));
	$msg .= remplace_txt('auteur', 'id_auteur', array("bio"), _request('txt_search'), _request('remplace'), _request('remplacer_par'));
	$msg .= remplace_txt('forum', 'id_forum', array("texte"), _request('txt_search'), _request('remplace'), _request('remplacer_par'));
	$msg .= remplace_txt('syndic', 'id_syndic', array("descriptif"), _request('txt_search'), _request('remplace'), _request('remplacer_par'));
	$msg .= "Fin du traitement";
	return array('message_ok'=>$msg);
}

?>
