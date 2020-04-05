<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function dictionnaires_declarer_url_objets($objets){
	$objets[] = 'dictionnaire';
	$objets[] = 'definition';
	return $objets;
}

function dictionnaires_post_edition($flux){
	// Seulement si c'est une modif d'objet
	if ($flux['args']['action'] == 'modifier' and $id_objet = $flux['args']['id_objet']){
		$trouver_table = charger_fonction('trouver_table', 'base/');
		$desc = $trouver_table($flux['args']['table_objet'], $flux['args']['serveur']);
		$id_table_objet = id_table_objet($flux['args']['type']);
		
		// On cherche les champs textuels
		$champs_texte = array();
		foreach ($desc['field'] as $champ=>$sql){
			if (preg_match('/(text|blob|varchar)/', $sql)){
				$champs_texte[] = $champ;
			}
		}
		
		// On récupère ces champs
		$textes = sql_fetsel($champs_texte, $flux['args']['spip_table_objet'], "$id_table_objet = $id_objet");
		// On récupère les définitions
		include_spip('inc/dictionnaires');
		$definitions = dictionnaires_lister_definitions();
		
		// On les scanne
		foreach ($textes as $texte){
			
		}
	}
}

function dictionnaires_post_propre($texte){
	$GLOBALS['dictionnaires_id_texte'] = uniqid();
	include_spip('inc/dictionnaires');
	$definitions = dictionnaires_lister_definitions();
	$masques = array();
	foreach ($definitions as $definition){
		$masques[] = $definition['masque'];
	}
	
	// Quelque soit le type de définition
	// on ne fait rien à l'intérieur de certaines balises
	$masque_echap = '{<(acronym|abbr|a|code|cadre|textarea)[^>]*>.*</\1>}Uis';
	preg_match_all($masque_echap, $texte, $balises_echap, PREG_SET_ORDER);
	$morceaux_a_traiter = preg_split($masque_echap, $texte);
	
	// On parcours uniquement les textes hors balises <abbr>
	foreach ($morceaux_a_traiter as $cle => $morceau_a_traiter){
		// On ne traite que ce qui est hors balises
		$masque_balises = '{<([a-z]+[^<>]*)>}i';
		preg_match_all($masque_balises, $morceau_a_traiter, $balises, PREG_SET_ORDER);
		$textes_a_traiter = preg_split($masque_balises, $morceau_a_traiter);
		$textes_a_traiter = preg_replace_callback($masques, 'dictionnaires_replace_callback', $textes_a_traiter);
		
		foreach ($textes_a_traiter as $cle2 => $texte_a_traiter){
			$textes_a_traiter[$cle2] = $texte_a_traiter ;
			if (isset($balises[$cle2][0])) 
				$textes_a_traiter[$cle2] .= $balises[$cle2][0];
		}
		
		$morceaux_a_traiter[$cle] = join('', $textes_a_traiter);
		if (isset($balises_echap[$cle][0])) 
			$morceaux_a_traiter[$cle] .= $balises_echap[$cle][0];
	}
	$texte = join('', $morceaux_a_traiter);
	
	$texte = echappe_retour($texte, 'dictionnaires');
	return $texte;
}

function dictionnaires_replace_callback($captures){
	include_spip('inc/config');
	static $deja_remplaces = array();
	static $id_texte = '';
	$definitions = dictionnaires_lister_definitions_par_terme();
	$remplacer_celui_la = true;
	
	// Si c'est un nouveau texte, on vide la liste des déjà remplacés
	$nouveau_texte = ((!$id_texte) or ($id_texte != $GLOBALS['dictionnaires_id_texte']));
	if ($nouveau_texte){
		$id_texte = $GLOBALS['dictionnaires_id_texte'];
		$deja_remplaces = array();
	}
	
	// Par défaut rien
	$retour = $captures[0];
	
	// On cherche la définition du terme trouvé
	if ($definition = $definitions[$captures[2]] or $definition = $definitions[strtolower($captures[2])]){
		$type = $definition['type'];
		
		// Si on a demandé à remplacer uniquement le premier mot trouvé
		if (
			($type and lire_config('dictionnaires/remplacer_premier_'.$type))
			or (!$type and lire_config('dictionnaires/remplacer_premier_defaut'))
		){
			foreach ($definition['termes'] as $terme){
				if (in_array($terme, $deja_remplaces)){
					$remplacer_celui_la = false;
				}
			}
		}
		
		// On ne travaille pas pour rien !
		if ($remplacer_celui_la){
			if (function_exists("dictionnaires_remplacer_$type")) { $remplacer = "dictionnaires_remplacer_$type"; }
			elseif (function_exists("dictionnaires_remplacer_${type}_dist")) { $remplacer = "dictionnaires_remplacer_${type}_dist"; }
			elseif (function_exists("dictionnaires_remplacer_defaut")) { $remplacer = "dictionnaires_remplacer_defaut"; }
			else { $remplacer = "dictionnaires_remplacer_defaut_dist"; }
		
			$retour = $captures[1].code_echappement($remplacer($captures[2], $definition), 'dictionnaires');
			$deja_remplaces = array_merge($deja_remplaces, $definition['termes']);
		}
	}
	
	return $retour;
}

?>
