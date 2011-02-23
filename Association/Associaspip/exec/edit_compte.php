<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/autoriser');

function prefixPlugin_insert_head($flux){
          $flux .= "<!-- un commentaire pour rien ! -->\n";
          return $flux;
      }


function exec_edit_compte() {
		
	$id_compte= intval(_request('id'));

	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
	
		association_onglets();
		
		echo debut_gauche("", true);
		
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);	
		
		echo association_retour();
		
		echo debut_droite("",true);

		$data = !$id_compte ? '' :sql_fetsel('*', 'spip_asso_comptes', "id_compte=$id_compte") ;
		if ($data) {
			$imputation=$data['imputation'];
			$date=$data['date'];
			$recette=$data['recette'];
			$depense=$data['depense'];
			$journal=$data['journal'];
			$justification=$data['justification'];
			if ($GLOBALS['association_metas']['destinations']=="on")
			{
				$destination = association_liste_destinations_associees($id_compte);
			}
		} else {
			$imputation=$recette=$depense=$journal=$justification=$destination='';
			$date = date('Y-m-d');
		}

		debut_cadre_relief(  "", false, "", $titre = _T('asso:modification_des_comptes'));
		
		$sql = sql_select('code,intitule,direction', 'spip_asso_plan', "classe<>". sql_quote($GLOBALS['association_metas']['classe_banques']), "", "code") ;
		$res = '';
		while ($banque = sql_fetch($sql)) {
			$code = $banque['code'];
			$s = ($imputation==$code) ? ' selected="selected"' : '';
			if  ($GLOBALS['association_metas']['comptes_stricts']=="on") {
				if ($banque['direction'] == "credit") {
					$d = '&nbsp;-&nbsp;'._T('asso:crediteur');
				}
				else {
					$d = '&nbsp;-&nbsp;'._T('asso:debiteur');
				}
			}
			else {
				$d = '';
			}
			$res .= "\n<option value='$code'$s>".$banque['intitule'].$d.'</option>';
		}
		if ($res)
			$res = '<label for="imputation"><strong>' 
			. _T('asso:imputation')
			. '</strong></label>'
			. '<select name="imputation" id="imputation" class="formo">'
			. $res
			. '</select>';

		$res .= '<label for="date"><strong>' 
		. _T('asso:date_aaaa_mm_jj') . '</strong></label>'
		. '<input name="date" value="'
		. $date.'" type="text" id="date" class="formo" />';
		if ($GLOBALS['association_metas']['comptes_stricts']=="on") {
			$res .= '<label for="montant"><strong>' 
			. _T('asso:montant') . '</strong></label>'
			. '<input name="montant" value="'
			. association_nbrefr($recette+$depense).'" type="text" id="montant" class="formo" />'; // on a soit recette soit depense egal a 0, on fait la somme pour avoir toujours celui qui nous 
		}
		else
		{
			$res .= '<label for="recette"><strong>' 
			. _T('asso:recette') . '</strong></label>'
			. '<input name="recette" value="'
			. association_nbrefr($recette).'" type="text" id="recette" class="formo" />'
			. '<label for="depense"><strong>' 
			. _T('asso:depense') . '</strong></label>'
			. '<input name="depense" value="'
			. association_nbrefr($depense).'"  type="text" id="depense" class="formo" />';
		}
		$res .= association_mode_de_paiement($journal, _T('asso:prets_libelle_mode_paiement'));

		$action = ($id_compte ? 'modifier' : 'ajouter');

		$res .= '<label for="justification"><strong>'
		. _T('asso:justification')
		. '&nbsp;:</strong></label>'
		. '<input name="justification" value="'
		. $justification
		. '" type="text" id="justification" class="formo" />';

		if ($GLOBALS['association_metas']['destinations']=="on")
		{
			$res .= association_editeur_destinations($destination);
		}

		$res .= '<div style="float:right;"><input type="submit" value="'
		. _T('asso:bouton_'. $action)
		. '" class="fondo" /></div>';

		echo redirige_action_post($action . '_comptes', $id_compte, 'comptes', '', "<div>$res</div>");

		fin_cadre_relief();  
		echo fin_page_association();
	}
}
?>
