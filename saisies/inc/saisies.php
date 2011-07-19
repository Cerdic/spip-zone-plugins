<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Une librairie pour manipuler ou obtenir des infos sur un tableau de saisies
 *
 * saisies_lister_par_nom()
 * saisies_lister_champs()
 * saisies_lister_valeurs_defaut()
 * saisies_charger_champs()
 * saisies_chercher()
 * saisies_supprimer()
 * saisies_inserer()
 * saisies_deplacer()
 * saisies_modifier()
 * saisies_verifier()
 * saisies_comparer()
 * saisies_generer_html()
 * saisies_generer_vue()
 * saisies_generer_nom()
 * saisies_inserer_html()
 * saisies_lister_disponibles()
 * saisies_autonomes()
 */

// Différentes méthodes pour trouver les saisies
include_spip('inc/saisies_lister');

// Différentes méthodes pour manipuler une liste de saisies
include_spip('inc/saisies_manipuler');

// Les outils pour afficher les saisies et leur vue
include_spip('inc/saisies_afficher');

/*
 * Cherche la description des saisies d'un formulaire CVT dont on donne le nom
 *
 * @param string $form Nom du formulaire dont on cherche les saisies
 * @return array Retourne les saisies du formulaire sinon false
 */
function saisies_chercher_formulaire($form, $args){
	if ($fonction_saisies = charger_fonction('saisies', 'formulaires/'.$form, true)
		and $saisies = call_user_func_array($fonction_saisies, $args)
		and is_array($saisies)
		// On passe les saisies dans un pipeline normé comme pour CVT
		and $saisies = pipeline(
			'formulaire_saisies',
			array(
				'args' => array('form' => $form, 'args' => $args),
				'data' => $saisies
			)
		)
		// Si c'est toujours un tableau après le pipeline
		and is_array($saisies)
	){
		return $saisies;
	}
	else{
		return false;
	}
}

/*
 * Cherche une saisie par son id, son nom ou son chemin et renvoie soit la saisie, soit son chemin
 *
 * @param array $saisies Un tableau décrivant les saisies
 * @param unknown_type $id_ou_nom_ou_chemin L'identifiant ou le nom de la saisie à chercher ou le chemin sous forme d'une liste de clés
 * @param bool $retourner_chemin Indique si on retourne non pas la saisie mais son chemin
 * @return array Retourne soit la saisie, soit son chemin, soit null
 */
function saisies_chercher($saisies, $id_ou_nom_ou_chemin, $retourner_chemin=false){

	if (is_array($saisies) and $id_ou_nom_ou_chemin){
		if (is_string($id_ou_nom_ou_chemin)){
			$nom = $id_ou_nom_ou_chemin;
			// identifiant ? premier caractere @
			$id = ($nom[0] == '@');

			foreach($saisies as $cle => $saisie){
				$chemin = array($cle);
				if ($nom == ($id ? $saisie['identifiant'] : $saisie['options']['nom'])) {
					return $retourner_chemin ? $chemin : $saisie;
				} elseif ($saisie['saisies'] and is_array($saisie['saisies']) and ($retour = saisies_chercher($saisie['saisies'], $nom, $retourner_chemin))) {
					return $retourner_chemin ? array_merge($chemin, array('saisies'), $retour) : $retour;
				}

			}
		}
		elseif (is_array($id_ou_nom_ou_chemin)){
			$chemin = $id_ou_nom_ou_chemin;
			$saisie = $saisies;
			// On vérifie l'existence quand même
			foreach ($chemin as $cle){
				if (isset($saisie[$cle])) $saisie = $saisie[$cle];
				else return null;
			}
			// Si c'est une vraie saisie
			if ($saisie['saisie'] and $saisie['options']['nom'])
				return $retourner_chemin ? $chemin : $saisie;
		}
	}
	
	return null;
}

/*
 * Génère un nom unique pour un champ d'un formulaire donné
 *
 * @param array $formulaire Le formulaire à analyser 
 * @param string $type_saisie Le type de champ dont on veut un identifiant 
 * @return string Un nom unique par rapport aux autres champs du formulaire
 */
function saisies_generer_nom($formulaire, $type_saisie){
	$champs = saisies_lister_champs($formulaire);
	
	// Tant que type_numero existe, on incrémente le compteur
	$compteur = 1;
	while (array_search($type_saisie.'_'.$compteur, $champs) !== false)
		$compteur++;
	
	// On a alors un compteur unique pour ce formulaire
	return $type_saisie.'_'.$compteur;
}

/*
 * Crée un identifiant Unique
 * pour toutes les saisies donnees qui n'en ont pas 
 *
 * @param Array $saisies Tableau de saisies
 * @param Bool $regenerer_id Régénère un nouvel identifiant pour toutes les saisies ?
 * @return Array Tableau de saisies complété des identifiants
 */
function saisies_identifier($saisies, $regenerer = false) {
	if (!is_array($saisies)) {
		return array();
	}
	foreach ($saisies as $k => $saisie) {
		$saisies[$k] = saisie_identifier($saisie, $regenerer);
	}
	return $saisies;
}

/**
 * Crée un identifiant Unique
 * pour la saisie donnee si elle n'en a pas
 * (et pour ses sous saisies éventuels)
 *
 * @param Array $saisie Tableau d'une saisie
 * @param Bool $regenerer_id Régénère un nouvel identifiant pour la saisie ?
 * @return Array Tableau de la saisie complété de l'identifiant
**/
function saisie_identifier($saisie, $regenerer = false) {
	if (!isset($saisie['identifiant']) OR !$saisie['identifiant']) {
		$saisie['identifiant'] = uniqid('@');
	} elseif ($regenerer) {
		$saisie['identifiant'] = uniqid('@');
	}
	if (isset($saisie['saisies']) AND is_array($saisie['saisies'])) {
		$saisie['saisies'] = saisies_identifier($saisie['saisies'], $regenerer);
	}
	return $saisie;
}

/*
 * Vérifier tout un formulaire tel que décrit avec les Saisies
 *
 * @param array $formulaire Le contenu d'un formulaire décrit dans un tableau de Saisies
 * @return array Retourne un tableau d'erreurs
 */
function saisies_verifier($formulaire){
	include_spip('inc/verifier');
	$erreurs = array();
	$verif_fonction = charger_fonction('verifier','inc',true);
	
	$saisies = saisies_lister_par_nom($formulaire);
	foreach ($saisies as $saisie){
		$obligatoire = $saisie['options']['obligatoire'];
		$champ = $saisie['options']['nom'];
		$file = ($saisie['saisie'] == 'input' and $saisie['options']['type'] == 'file');
		$verifier = $saisie['verifier'];

		// Si le nom du champ est un tableau indexé, il faut parser !
		if (preg_match('/([\w]+)((\[[\w]+\])+)/', $champ, $separe)){
			$valeur = _request($separe[1]);
			preg_match_all('/\[([\w]+)\]/', $separe[2], $index);
			// On va chercher au fond du tableau
			foreach($index[1] as $cle){
				$valeur = $valeur[$cle];
			}
		}
		// Sinon la valeur est juste celle du nom
		else
			$valeur = _request($champ);
		
		// On regarde d'abord si le champ est obligatoire
		if ($obligatoire
			and $obligatoire != 'non'
			and (
				($file and !$_FILES[$champ]['name'])
				or (!$file and (
					is_null($valeur)
					or (is_string($valeur) and trim($valeur) == '')
					or (is_array($valeur) and count($valeur) == 0)
				))
			)
		)
			$erreurs[$champ] = _T('info_obligatoire');
		
		// On continue seulement si ya pas d'erreur d'obligation et qu'il y a une demande de verif
		if (!$erreurs[$champ] and is_array($verifier) and $verif_fonction){
			// Si le champ n'est pas valide par rapport au test demandé, on ajoute l'erreur
			if ($erreur_eventuelle = $verif_fonction($valeur, $verifier['type'], $verifier['options']))
				$erreurs[$champ] = $erreur_eventuelle;
		}
	}
	
	return $erreurs;
}

/*
 * Transforme une chaine en tableau avec comme principe :
 * - une ligne devient une case
 * - si la ligne est de la forme truc|bidule alors truc est la clé et bidule la valeur
 *
 * @param string $chaine Une chaine à transformer
 * @return array Retourne un tableau PHP
 */
function saisies_chaine2tableau($chaine, $separateur="\n"){
	if ($chaine and is_string($chaine)){
		$tableau = array();
		// On découpe d'abord en lignes
		$lignes = explode($separateur, $chaine);
		foreach ($lignes as $i=>$ligne){
			$ligne = trim(trim($ligne), '|');
			// Si ce n'est pas une ligne sans rien
			if ($ligne !== ''){
				// Si on trouve un découpage dans la ligne on fait cle|valeur
				if (strpos($ligne, '|') !== false){
					list($cle,$valeur) = explode('|', $ligne, 2);
					$tableau[$cle] = $valeur;
				}
				// Sinon on génère la clé
				else{
					$tableau[$i] = $ligne;
				}
			}
		}
		return $tableau;
	}
	// Si c'est déjà un tableau on le renvoie tel quel
	elseif (is_array($chaine)){
		return $chaine;
	}
	else{
		return array();
	}
}

/*
 * Transforme un tableau en chaine de caractères avec comme principe :
 * - une case de vient une ligne de la chaine
 * - chaque ligne est générée avec la forme cle|valeur
 */
function saisies_tableau2chaine($tableau){
	if ($tableau and is_array($tableau)){
		$chaine = '';
	
		foreach($tableau as $cle=>$valeur){
			$ligne = trim("$cle|$valeur");
			$chaine .= "$ligne\n";
		}
		$chaine = trim($chaine);
	
		return $chaine;
	}
	// Si c'est déjà une chaine on la renvoie telle quelle
	elseif (is_string($tableau)){
		return $tableau;
	}
	else{
		return '';
	}
}

/*
 * Génère une page d'aide listant toutes les saisies et leurs options
 */
function saisies_generer_aide(){
	// On a déjà la liste par saisie
	$saisies = saisies_lister_disponibles();
	
	// On construit une liste par options
	$options = array();
	foreach ($saisies as $type_saisie=>$saisie){
		$options_saisie = saisies_lister_par_nom($saisie['options'], false);
		foreach ($options_saisie as $nom=>$option){
			// Si l'option n'existe pas encore
			if (!isset($options[$nom])){
				$options[$nom] = _T_ou_typo($option['options']);
			}
			// On ajoute toujours par qui c'est utilisé
			$options[$nom]['utilisee_par'][] = $type_saisie;
		}
		ksort($options_saisie);
		$saisies[$type_saisie]['options'] = $options_saisie;
	}
	ksort($options);
	
	return recuperer_fond(
		'inclure/saisies_aide',
		array(
			'saisies' => $saisies,
			'options' => $options
		)
	);
}

/*
 * Le tableau de saisies a-t-il une option afficher_si ?
 *
 * @param array $saisies Un tableau de saisies
 * @return boolean
 */

function saisies_afficher_si($saisies) {
	$saisies = saisies_lister_par_nom($saisies,true);
	// Dès qu'il y a au moins une option afficher_si, on l'active
	foreach ($saisies as $saisie) {
		if (isset($saisie['options']['afficher_si']))
			return true;
	}
	return false;
}

?>
