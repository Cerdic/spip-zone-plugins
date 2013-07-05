<?
if (!defined('_ECRIRE_INC_VERSION')) return;

function minus_formulaire_verifier($flux){
	if ($flux["args"]["form"] == "editer_article"){
		$titre = _request("titre");
		if (prop_minus($titre) < 0.7){ // si moins de 70% de minuscule > il y a un souci
		    $flux["data"]["titre"] = _T("minus:trop_majuscule");
			}
		}
	
	return $flux;
    }

function prop_minus($txt){
  // return la proportion de minuscule
  $nb_minuscule = levenshtein (mb_strtoupper($txt, 'UTF-8'),$txt);
  return $nb_minuscule/mb_strlen($txt, 'UTF-8');
}

?>