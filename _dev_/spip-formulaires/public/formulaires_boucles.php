<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	function boucle_FORMULAIRES_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_formulaires";  
		if (!isset($boucle->modificateur['criteres']['statut'])) {
			if (!isset($boucle->modificateur['tout']))
				array_unshift($boucle->where, array("'='", "'$id_table.statut'", "'\"en_ligne\"'"));
		}
        return calculer_boucle($id_boucle, $boucles); 
	}


	function boucle_BLOCS_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_blocs";  
		$boucle->default_order[] = "'ordre'" ;
        return calculer_boucle($id_boucle, $boucles); 
	}


	function boucle_QUESTIONS_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_questions";  
		$boucle->default_order[] = "'ordre'" ;
        return calculer_boucle($id_boucle, $boucles); 
	}


	function boucle_CHOIX_QUESTION_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_choix_question";
		$boucle->default_order[] = "'ordre'" ;
        return calculer_boucle($id_boucle, $boucles); 
	}


?>