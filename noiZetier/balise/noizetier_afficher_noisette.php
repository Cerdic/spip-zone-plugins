<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_NOIZETIER_AFFICHER_NOISETTE_dist($p) {
	
	$id_noisette = champ_sql('id_noisette', $p);
	$noisette = champ_sql('noisette', $p);
	$parametres = champ_sql('parametres', $p);
	$contexte = champ_sql('contexte', $p);

	// si pas de contexte attribuer, on passe tout le contexte que l'on recoit
	// sinon, on regarde si 'null' ou 'env' est indique :
	// si null => aucun contexte
	// si 'env' => tout le contexte recu.
	$environnement = '$Pile[0]';
	
	$p->code =  "(!$id_noisette) ? _T('zbug_champ_hors_motif', array('champ'=>'ID_NOISETTE', 'motif'=>'NOISETTES')) : recuperer_fond(
		'noisettes/'.$noisette,
		array_merge(
			unserialize($parametres),
			(((\$contexte = unserialize($contexte)) and (\$contexte = array_flip(\$contexte))) ?
				(isset(\$contexte['aucun']) ? array() :
					(isset(\$contexte['env']) ? $environnement :
						array_intersect_key($environnement, \$contexte))) :
							$environnement)),
		array('ajax'=>true)
	)";
		
	return $p;
    
}

?>
