<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Chercher une publicité à afficher suivant le contexte
 *
 * @param int $id_encart Identifiant de l'encart demandé
 * @param array $contexte Tableau pouvant contenir des informations sur le contexte de la page
 * @return int Retourne un identifiant de publicité
 */
function campagnes_chercher_campagne($id_encart, $contexte=array(), $plusieurs_affichages='non', $retourner_affiches=false){
	static $deja_affiches = array();
	
	if ($retourner_affiches){
		if (isset($deja_affiches[$id_encart])){
			return $deja_affiches[$id_encart];
		}
		else{ return array(); }
	}
	
	// Si pas le param principal on sort
	if (!$id_encart = intval($id_encart)){
		return 0;
	}
	
	// La publicité doit au moins être publiée et être dans l'encart
	$where = array(
		'id_encart = '.$id_encart,
		'statut = '.sql_quote('publie')
	);
	
	// Si on a déjà sorti des publicités dans le même hit PHP, on ne les affiche pas deux fois si pas accepté explicitement
	if ($plusieurs_affichages != 'oui' and isset($deja_affiches[$id_encart])){
		$where[] = sql_in('id_campagne', $deja_affiches[$id_encart], 'NOT');
	}
	
	// S'il y a un contexte, on ajoute que la publicité doit :
	// - soit ne pas avoir de restrictions d'affichage du tout
	// - soit avoir *au moins un* des couples param=valeur dans sa liste de restrictions
	//   par exemple "id_article=123"
	if ($contexte and is_array($contexte)){
		$where_contexte = '((contextes = "")';
		foreach ($contexte as $param=>$valeur){
			$where_contexte .= " OR (contextes LIKE '%$param=$valeur%')";
		}
		$where_contexte .= ')';	
		
		$where[] = $where_contexte;
	}
	
	if ($campagne = sql_fetsel(
		'id_campagne, rand() as alea',
		'spip_campagnes',
		$where,
		'',
		'alea',
		'0,1'
	)){
		// On garde en mémoire ce qui est déjà sorti dans ce hit PHP
		if (!isset($deja_affiches[$id_encart])){
			$deja_affiches[$id_encart] = array();
		}
		$deja_affiches[$id_encart][] = $campagne['id_campagne'];
		return $campagne['id_campagne'];
	}
	else{ return 0; }
}

?>
