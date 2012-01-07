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
	/* Nota: il faut creer un conteneur (appeler ici 'type_operation_imputation') pour pouvoir y injecter par JS le select#imputation (ce qui se fait dans la fonction appelee et non ici pour eviter certains effets de bord de Tidy). Cette acrobatie est necessaire parce-que "<select> <option>aucun</option> <script>rSI...</script> <noscript>...opts...</noscript> </select>" n'est pas valide (et est malencontreusement corrige par Tidi) ; mais que la forme correcte est "<script>document.write('<select><option>aucun</option></select>');rSI..</script> <noscrip><select><option>aucun</option>...opts...</select></noscript>" */
	$res = "<span id='type_operation_imputation'> </span> <script type='text/javascript'>\n<!--//--><![CDATA[//><!--\n
	remplirSelectImputation($type_operation";
	if ($id_compte) {
		$res .= ",$imputation";
	}
	$intro = '<select name="imputation" id="imputation" class="formo">
<option value="0">-- ' . _T('choisir_un_code') . '</option>';
	$res .= ");\n//--><!]]>\n</script><noscript>\n$intro";
	foreach ($GLOBALS['association_metas'] as $key => $val) {
		if (substr($key, 0, 6) === "classe") {
			$tableau = association_liste_plan_comptable($val);
			foreach ($tableau as $k => $v) {
				if($k!=581) { // code virement interne
					$res .= "\n<option value='$k'" . (($k==$imputation)?' selected="selected"':'') . ">$k - $v</option>";
				}
			}
		}
	}
	$res .= "\n</select>\n</noscript>";
	return $res;
}

?>
