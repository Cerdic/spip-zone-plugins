<?
if (!defined('_ECRIRE_INC_VERSION')) return;
function minus_formulaire_charger($flux){
	if ($flux["args"]["form"] == "editer_article"){
		$titre = _request("titre");
		if (prop_minus($titre) < 0.7){ // si moins de 70% de minuscule > il y a un souci -> on le marque en hidden
		    $flux["data"]["_hidden"].="<input type='hidden' name='titre_trop_majusucules' value='oui' />\n";
		}
	}
    return $flux;
}
function minus_formulaire_verifier($flux){
	if ($flux["args"]["form"] == "editer_article" and !(_request("id_article") > 0)){
		$titre = _request("titre");
		if (prop_minus($titre) < 0.7 and !_request("titre_trop_majusucules")){ // si moins de 70% de minuscule > il y a un souci ; mais si on n'a déjà signalé une fois, on ne le signale pas une seconde fois
		    $flux["data"]["titre"] = _T("minus:trop_majuscule");
		    }
		}
		
	
	return $flux;
    }

function prop_minus($txt){
  // return la proportion de minuscule
  if (mb_strlen($txt, 'UTF-8') > 0){
    $nb_minuscule = levenshtein (mb_strtoupper($txt, 'UTF-8'),$txt);
    return $nb_minuscule/mb_strlen($txt, 'UTF-8');
    }
  else return 1;
}

?>