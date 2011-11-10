<?php

/* * *************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 08/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

/* Cette balise affiche une série de boutons radios pour "simplifier" la saisie du trésorier.
 * Ils sont au nombre de 4, dans le cas d'une trésorerie simple, à savoir :
 * - "Recette", "Dépense", "Virement" et "Contrib. volontaire"
 * - des <div> dans le formulaire CVT "editer_asso_comptes.html" qui sont cachés/affichés en fonction du contexte
 * - pour une nouvelle saisie, on suppose que c'est une dépense par défaut
 * - lorsqu'on édite une saisie, les boutons (non concernés !!) sont désactivés ...
 * - dans le cas d'un virement, on génère 2 écritures avec le compte 58xx
 * - pour une contribution volontaire, on ne parle que de "recette ou dépense évaluée". Elles ne sont pas comptabilisées
 * dans le bilan "en caisse" mais s'affichent au pied du compte de résultat
 */

if (!defined("_ECRIRE_INC_VERSION"))
	return;

function balise_BOUTON_TYPE_OPERATION_dist($p) {
	/* on recupere dans l'environement le code qui doit donc etre assignees par la fonction charger du formulaire contenant la balise */
	return calculer_balise_dynamique($p, 'BOUTON_TYPE_OPERATION', array('id_compte', 'type_operation'));
}

function balise_BOUTON_TYPE_OPERATION_dyn($id_compte, $type_operation) {
	$res = "<script type='text/javascript' src='" . find_in_path("javascript/association.js") . "'></script>";
	$res .= "\n<li class='editer_type_operation'><label><strong>" . _T('asso:bouton_radio_type_operation_titre') . "</strong></label>";

	$res .= "\n<div class='choix'>";

	$num_classe = $GLOBALS['association_metas']['classe_charges'];
	$res .= "\n<input type='radio'";
	$res .= (($id_compte && ($type_operation !== $num_classe)) ? " disabled='disabled' " : " ");
	$res .= "class='radio' name='type_operation' value='" . $num_classe . "' id='type_operation_depense'";
	$res .= (($type_operation == $num_classe) ? " checked='checked'" : "");
	$res .= " onclick=\"remplirSelectImputation(" . $num_classe . ");
		afficheDiv('label_imputation','label_depense','depense','label_journal_depense','mode_paiement','justification','destination');
		cacheDiv('label_destination','label_depense_evaluee','label_journal_recette','recette'); \" />";
	$res .= "\n<label for='type_operation_depense'>D&eacute;pense</label>";

	$num_classe = $GLOBALS['association_metas']['classe_produits'];
	$res .= "\n<input type='radio'";
	$res .= (($id_compte && ($type_operation !== $num_classe)) ? " disabled='disabled' " : " ");
	$res .= "class='radio' name='type_operation' value='" . $num_classe . "' id='type_operation_recette'";
	$res .= (($type_operation == $num_classe) ? " checked='checked'" : "");
	$res .= " onclick=\"remplirSelectImputation(" . $num_classe . ");
		afficheDiv('label_imputation','label_recette','recette','label_journal_recette','mode_paiement','justification','destination');
		cacheDiv('label_destination','label_recette_evaluee','label_journal_depense','depense'); \" />";
	$res .= "\n<label for='type_operation_recette'>Recette</label>";

	$num_classe = $GLOBALS['association_metas']['classe_banques'];
	$res .= "\n<input type='radio'";
	$res .= (($id_compte && ($type_operation !== $num_classe)) ? " disabled='disabled' " : " ");
	$res .= "class='radio' name='type_operation' value='" . $num_classe . "' id='type_operation_virement'";
	$res .= (($type_operation == $num_classe) ? " checked='checked'" : "");
	$res .= " onclick=\"remplirSelectImputation(" . $num_classe . ");
		afficheDiv('label_destination','label_depense','depense','label_journal_depense','mode_paiement','justification');
		cacheDiv('label_imputation','label_depense_evaluee','label_journal_recette','recette','destination'); \" />";
	$res .= "\n<label for='type_operation_virement'>Virement</label>";

	$num_classe = $GLOBALS['association_metas']['classe_contributions_volontaires'];
	if(sql_countsel('spip_asso_plan', "classe='$num_classe'")) {
		$res .= "\n<input type='radio'";
		$res .= (($id_compte && ($type_operation !== $num_classe)) ? " disabled='disabled' " : " ");
		$res .= "class='radio' name='type_operation' value='" . $num_classe . "' id='type_operation_contribution_volontaire'";
		$res .= (($type_operation == $num_classe) ? " checked='checked'" : "");
		$res .= " onclick=\"remplirSelectImputation(" . $num_classe . ");
			afficheDiv('label_imputation','label_depense_evaluee','label_recette_evaluee','depense','recette','justification','destination');
			cacheDiv('label_destination','label_depense','label_recette','mode_paiement'); \" />";
		$res .= "\n<label for='type_operation_contribution_volontaire'>Contrib. volontaire</label>";
	}
	$res .= "\n</div>";
	$res .= "\n</li>";
	
	return $res;
}

?>
