<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_MENUS_AFFICHER_ENTREE_dist($p) {
	
	$contexte = interprete_argument_balise(1,$p);
	$id_menus_entree = champ_sql('id_menus_entree', $p);
	$type_entree = champ_sql('type_entree', $p);
	$parametres = champ_sql('parametres', $p);
	
	// Par défaut ça affiche le mode du menu public
	if ($contexte != "'appel_formulaire'")
		$contexte = "'appel_menu'";
	
	$p->code =  "(!$id_menus_entree) ? _T('zbug_champ_hors_motif', array('champ'=>'AFFICHER_ENTREE', 'motif'=>'MENUS_ENTREES')) : recuperer_fond(
		'menus/'.$type_entree,
		array_merge(unserialize($parametres), array($contexte=>true, 'env'=>\$Pile[0]))
	)";
	
	return $p;
    
}

?>
