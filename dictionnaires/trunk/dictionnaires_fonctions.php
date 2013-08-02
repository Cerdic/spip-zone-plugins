<?php

/**
 * Filtres pour les squelettes
 * 
 * @package SPIP\Dictionnaires\Fonctions
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Ajoute sur le texte les définitions sur les termes à définir.
 *
 * N'ajoute pas de définition si les termes à définir sont déjà
 * dans des balises de définition ou de code, genre <abbr> ou <code>
 * 
 * @param string $texte  Texte 
 * @return string        Texte
**/
function filtre_definitions_dist($texte) {
	/**
	 * Eviter d'être considéré comme spam lors du post si on met un mot considéré comme définition
	 * par exemple dans le titre d'un commentaire (plugin forum) ou d'un ticket (plugin tickets)
	 */
	if(_request('formulaire_action') && (substr(_request('formulaire_action'),0,7) == 'editer_'))
		return $texte;
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



/**
 * Callback réceptionnant les captures de termes à définir
 *
 * Remplace la trouvaille par une description avec sa définition.
 * Et on ne le fait qu'au moment de la première occurence, lorsque
 * c'est configuré comme tel.
 *
 * @param string $captures Terme trouvé
 * @return string          HTML du terme et de sa définition
**/
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
