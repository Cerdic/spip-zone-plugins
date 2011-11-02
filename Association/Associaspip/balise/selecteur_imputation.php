<?php

/* * *************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 09/2011									   *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

/* Cette balise affiche un selecteur du code d'imputation fonction de la classe */
if (!defined("_ECRIRE_INC_VERSION"))
	return;

function balise_SELECTEUR_IMPUTATION_dist($p) {
	/* on recupere dans l'environement le type d'operation */
	return calculer_balise_dynamique($p, 'SELECTEUR_IMPUTATION', array('id_compte', 'type_operation', 'imputation'));
}

function balise_SELECTEUR_IMPUTATION_dyn($id_compte, $type_operation, $imputation) {
	$res = '<label for="imputation"><strong>' . _T('asso:imputation') . '</strong></label>';
	$res .= '<select name="imputation" id="imputation" class="formo" >';
	$res .= '<option value="0" selected="selected">-- choisissez un code</option>';
	$res .= '</select>';
	if (!$id_compte) {
		$res .= "<script> remplirSelectImputation(" . $type_operation . ");</script>";
	}
	else {
		$res .= "<script> remplirSelectImputation(" . $type_operation . "," . $imputation . ");</script>";
	}
	return $res;
}

?>
