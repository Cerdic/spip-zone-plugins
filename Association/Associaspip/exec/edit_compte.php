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
	
		echo "<script type='text/javascript' src='".find_in_path("javascript/jquery.destinations_form.js")."'></script>";
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
				if ($destination_query = sql_select('spip_asso_destination_op.id_destination, spip_asso_destination_op.recette, spip_asso_destination_op.depense, spip_asso_destination.intitule', 'spip_asso_destination_op RIGHT JOIN spip_asso_destination ON spip_asso_destination.id_destination=spip_asso_destination_op.id_destination', "id_compte=$id_compte", '', 'spip_asso_destination.intitule'))
				{
					$destination = array();
					while ($destination_op = sql_fetch($destination_query))
					{	/* soit recette soit depense est egal a 0, donc pour l'affichage du montant on se contente les additionner */
						$destination[$destination_op[id_destination]] = $destination_op[recette]+$destination_op[depense]; 
					
					}
				}
				else
				{
					$destination='';
				}
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
			. ($recette+$depense).'" type="text" id="montant" class="formo" />'; // on a soit recette soit depense egal a 0, on fait la somme pour avoir toujours celui qui nous 
		}
		else
		{
			$res .= '<label for="recette"><strong>' 
			. _T('asso:recette') . '</strong></label>'
			. '<input name="recette" value="'
			. $recette.'" type="text" id="recette" class="formo" />'
			. '<label for="depense"><strong>' 
			. _T('asso:depense') . '</strong></label>'
			. '<input name="depense" value="'
			. $depense.'"  type="text" id="depense" class="formo" />';
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
			// recupere la liste de toutes les destination
			$liste_destination = '';
			$sql = sql_select('id_destination,intitule', 'spip_asso_destination', "", "", "intitule");
			while ($destination_info = sql_fetch($sql)) {
				$id_destination = $destination_info['id_destination'];
			 	$liste_destination .= "<option value='$id_destination'>".$destination_info['intitule'].'</option>';
			}

			if ($liste_destination)
			{
				
			 	$res .= '<label for="destination"><strong>'
				. _T('asso:destination')
				. '&nbsp;:</strong></label>'
				. '<div id="divTxtDestination">';

				$idIndex=1;
				if ($destination != '') /* si on a une liste de destinations (on edite une operation) */
				{
					foreach ($destination as $destId => $destMontant)
					{						
						$liste_destination_selected = preg_replace('/(value=\''.$destId.'\')/', '$1 selected="selected"', $liste_destination);
						$res .= '<p class="formo" id="row'.$idIndex.'"><select name="destination_id'.$idIndex.'" id="destination_id'.$idIndex.'" >'
						. $liste_destination_selected
						. '</select><input name="montant_destination_id'.$idIndex.'" value="'
						. $destMontant
						. '" type="text" id="montant_destination_id'.$idIndex.'" />';
						$res .= "<button class='destButton' type='button' onClick='addFormField(); return false;'>+</button>";
						if ($idIndex>1)
						{
							$res .= "<button class='destButton' type='button' onClick='removeFormField(\"#row".$idIndex."\"); return false;'>-</button>";
						}
						$res .= '</p>';
						$idIndex++;
					}
				}
				else /* pas de destination deja definies pour cette operation */
				{
					$res .= '<p id="row1" class="formo"><select name="destination_id1" id="destination_id1" >'
					. $liste_destination
					. '</select><input name="montant_destination_id1" value="'
					. ''
					. '" type="text" id="montant_destination_id1"/>'
					. "<button class='destButton' type='button' onClick='addFormField(); return false;'>+</button></p>";
				}

				$res .= '<input type="hidden" id="idNextDestination" value="'.($idIndex+1).'"></div>';
			}
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
