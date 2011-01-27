<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

/* callback pour filtrer tout $_POST et ne recuperer que les destinations */
function destination_post_filter($var)
{
	if (preg_match ('/^destination_id/', $var)>0) return TRUE;
	return FALSE;
}

function action_ajouter_comptes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();


	$date= _request('date');
	$imputation= _request('imputation');
	if ($GLOBALS['association_metas']['comptes_stricts']=="on") {
		if ($montant_req = _request('montant')){
			$montant = intval($montant_req);
		}
		$r = sql_fetsel('direction', 'spip_asso_plan', "code=$imputation");
		if ($r['direction'] == "credit")
		{
			$recette = $montant;
			$depense = 0;
		}
		else
		{
			$recette = 0;
			$depense = $montant;
		}
	}
	else
	{
		if ($recette_req = _request('recette')){
			$recette = intval($recette_req);
		}
		if ($depense_req = _request('depense')){
			$depense = intval($depense_req);
		}
	}
	$justification= _request('justification');
	$journal= _request('journal');

	/* on verifie les valeurs de recette et depense: positif et pas d'entree recette et depense simultanees */
	if (($recette<0) || ($depense<0) || ($recette>0 && $depense>0))
	{
		include_spip('inc/minipres');
		$url_retour = generer_url_ecrire('edit_compte');
		echo minipres(_T('asso:erreur_titre'),_T('asso:erreur_recette_depense').'<br/><h1><a href="'.$url_retour.'">Retour</a><h1>');
		exit;
	}

	include_spip('base/association');		

	$id_compte = sql_insertq('spip_asso_comptes', array(
		    'date' => $date,
		    'imputation' => $imputation,
		    'recette' => $recette,
		    'depense' => $depense,
		    'journal' => $journal,
		    'justification' => $justification));

	/* Si on doit gerer les destinations */
	if ($GLOBALS['association_metas']['destinations']=="on")
	{
		if ($recette>0) {
			$attribution_montant = "recette";
		}
		else
		{
			$attribution_montant = "depense";
		}

		/* on recupere dans $_POST toutes les keys des entrees commencant par destination_id */
		$toutesDestinationsPOST = array_filter(array_keys($_POST), "destination_post_filter");
		
		/* on boucle sur toutes les cles trouvees, les montant ont des noms de champs identiques mais prefixes par montant_ */
		$total_destination = 0;
		$id_inserted = array();
		foreach ($toutesDestinationsPOST as $destination_id)
		{
			$id_destination = _request($destination_id);
			/* on verifie qu'on n'a pas deja inserer une destination avec cette id */
			if (!array_key_exists($id_destination,$id_inserted)) {
				$id_inserted[$id_destination]=0;
			}
			else {/* on a deja insere cette destination: erreur */
				include_spip('inc/minipres');
				$url_retour = generer_url_ecrire('edit_compte','id='.$id_compte);
				echo minipres(_T('asso:erreur_titre'),_T('asso:erreur_destination_dupliquee').'<br/><h1><a href="'.$url_retour.'">Retour</a><h1>');
				exit;
			}
			$montant = intval(_request('montant_'.$destination_id));
			$total_destination += $montant;
			sql_insertq('spip_asso_destination_op', array(
			    'id_compte' => $id_compte,
			    'id_destination' => $id_destination,
			    $attribution_montant => $montant));
		}
		
		/* on verifie que la somme des montants des destinations correspond au montant de l'operation */
		if (($recette>0 && $total_destination != $recette) || ($depense>0 && $total_destination != $depense))
		{
			include_spip('inc/minipres');
			$url_retour = generer_url_ecrire('edit_compte','id='.$id_compte);
			echo minipres(_T('asso:erreur_titre'),_T('asso:erreur_montant_destination').'<br/><h1><a href="'.$url_retour.'">Retour</a><h1>');
			exit;
		}
	}
}
?>
