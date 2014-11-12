<?
if (!defined('_ECRIRE_INC_VERSION')) return;
function minus_formulaire_charger($flux){
	
    if (substr($flux["args"]["form"],0,7) == "editer_"){
		if (trop_majuscules()){ // si on a trop de majuscule, on le marque avec un hidden
		    $flux["data"]["_hidden"].="<input type='hidden' name='titre_trop_majusucules' value='oui' />\n";
		}
	}
    return $flux;
}
function minus_formulaire_verifier($flux){
	if (substr($flux["args"]["form"],0,7) == "editer_" and $flux["args"]["args"][0]=='oui'){// on suppose que les formulaires editer_xxx passse tout oui si on edite pour la première fois
		if (trop_majuscules() and !_request("titre_trop_majusucules")){ // si ion a trop de majuscules, on l'affiche, sauf si on l'a déjà affiché une fois.
		    $flux["data"]["titre"] = _T("minus:trop_majuscule");
		    }
		}
		
	
	return $flux;
    }

function trop_majuscules(){
	// return True s'il y a trop de majuscule dans le titre
	$titre = supprimer_numero(_request("titre"));
	return (prop_maj($titre) >= 0.3);
	}
function prop_maj($txt){
  // return la proportion de minuscule
  if (mb_strlen($txt, 'UTF-8') > 0){
    $nb_maj = levenshtein (mb_strtolower($txt, 'UTF-8'),$txt);
    return $nb_maj/mb_strlen($txt, 'UTF-8');
    }
  else return 0;
}

?>
