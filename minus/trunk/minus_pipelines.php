<?
if (!defined('_ECRIRE_INC_VERSION')) return;
function minus_formulaire_charger($flux){
	if ($flux["args"]["form"] == "editer_article" or $flux["args"]["form"]=="editer_evenement"){
		if (trop_majuscules()){ // si on a trop de majuscule, on le marque avec un hidden
		    $flux["data"]["_hidden"].="<input type='hidden' name='titre_trop_majusucules' value='oui' />\n";
		}
	}
    return $flux;
}
function minus_formulaire_verifier($flux){
	if (($flux["args"]["form"] == "editer_article" and !(_request("id_article") > 0)) or ($flux["args"]["form"]=="editer_evenement" and !(_request("id_evenement") > 0))){
		if (trop_majuscules() and !_request("titre_trop_majusucules")){ // si ion a trop de majuscules, on l'affiche, sauf si on l'a déjà affiché une fois.
		    $flux["data"]["titre"] = _T("minus:trop_majuscule");
		    }
		}
		
	
	return $flux;
    }

function trop_majuscules(){
	// return True s'il y a trop de majuscule dans le titre
	$titre = _request("titre");
	return (prop_minus($titre) < 0.7);
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