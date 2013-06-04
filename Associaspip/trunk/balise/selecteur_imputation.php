<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Cette balise affiche un selecteur du code d'imputation fonction de la classe
 */
function balise_SELECTEUR_IMPUTATION_dist($p) {
	// on recupere dans l'environement le type d'operation
	return calculer_balise_dynamique($p, 'SELECTEUR_IMPUTATION', array('id_compte', 'type_operation', 'imputation'));
}

function balise_SELECTEUR_IMPUTATION_dyn($id_compte, $type_operation, $imputation) {
	/**
	 * @note
	 *   Il faut creer un conteneur (appeler ici 'type_operation_imputation')
	 *   pour pouvoir y injecter par JS le select#imputation (ce qui se fait dans
	 *   la fonction appelee et non ici pour eviter certains effets de bord de
	 *   Tidy). Cette acrobatie est necessaire parce-que
	 *   "<select> <option>aucun</option> <script>rSI...</script> <noscript>...opts...</noscript> </select>"
	 *   n'est pas valide (et est malencontreusement corrige par Tidi) ; mais
	 *   que la forme correcte est
	 *   "<script>document.write('<select><option>aucun</option></select>');rSI..</script> <noscrip><select><option>aucun</option>...opts...</select></noscript>"
	 */
	$res = "<span id='type_operation_imputation'> </span> <script type='text/javascript'>\n<!--//--><![CDATA[//><!--\n
	remplirSelectImputation($type_operation";
	if ($id_compte) {
		$res .= ",$imputation";
	}
	$res .= ');
	//--><!]]>
	</script><noscript><div>
	<select name="imputation" id="imputation" class="select">
<option value="0">-- ' . _T('asso:choisir_ref_compte') . '</option>';

	include_spip('inc/association_comptabilite');
	$interne = $GLOBALS['association_metas']['pc_intravirements'];
	foreach ( array(
	$GLOBALS['association_metas']['classe_charges'],
	$GLOBALS['association_metas']['classe_produits'],
	$GLOBALS['association_metas']['classe_banques'],
	$GLOBALS['association_metas']['classe_contributions_volontaires']
	) as $key => $val) {
		$res .= "\n<optgroup label='$val - ". comptabilite_reference_intitule($code) ."'>";
		foreach (comptabilite_liste_comptesclasse($val, 1) as $k => $v) {
			if ($k != $interne) { // code virement interne
				$s = ($k==$imputation)?' selected="selected"':'';
				$res .= "\n<option value='$k'$s>$k-$v</option>";
			}
		}
		$res .= "\n</optgroup>";
	}
	$res .= "\n</select>\n</div></noscript>";
	return $res;
}

?>