<?php
/*
| 3/11/07 -
| affiche_milieu => exec=auteur_infos
*/

spipbb_log("included",3,__FILE__);

include_spip('inc/presentation');
include_spip('inc/minipres');
include_spip('inc/texte');
include_spip('inc/layer');

function spipbb_auteur_infos($id_auteur=0) {
	if (empty($id_auteur)) return;
	# spip
	global 	$connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur,
			$couleur_claire, $couleur_foncee,
			$spip_lang_right,$spip_lang_left;

	$aff="";
	$aff.= "<div id='spipbb_editer_infos-$id_auteur'>";

	$visible = ($id_auteur==$connect_id_auteur) ;
	
	$bouton = bouton_block_depliable(_T('spipbb:config_champs_auteur'),$visible,"spipbb_$id_auteur");

	#$aff.= debut_cadre_relief(_DIR_PLUGIN_GAF."img_pack/gaf_ico-24.gif",true);
	$aff.=debut_cadre_enfonce(_DIR_PLUGIN_SPIPBB."img_pack/gaf_ico-24.gif", true, "", $bouton);

	$aff.= debut_block_depliable($visible,"spipbb_$id_auteur");

	$aff.= formulaire_spipbb_auteur_infos($id_auteur);

	$aff.= fin_block(true);
	$aff.= fin_cadre_enfonce(true);
	#$aff.= fin_cadre_relief(true);
	$aff.= "</div>";

	return $aff;

}

function formulaire_spipbb_auteur_infos($id_auteur=0) {
	if (empty($id_auteur)) return ;
	$table_support=lire_config("spipbb/table_support");
	$type_support = lire_config('spipbb/support_auteurs');

	$affiche='';
	$select=array();// c: 10/2/8 compat multibases

	# statut de cet id_auteur

	$ra=sql_fetsel("statut","spip_auteurs","id_auteur=$id_auteur");
	$aut_statut=$ra['statut'];

	# prepa des champs a presenter
	foreach($GLOBALS['champs_sap_spipbb'] as $chp => $def) {
		// c: 10/2/8 compat multibases
		//$select.= ",".$chp;
		$select[]= $chp;
		$tab_suppleant[$chp]="";
	}

	if ($type_support=="table") {
		$r=sql_fetsel($select,"spip_$table_support","id_auteur=$id_auteur");
	}

	# cas nouv. inscrit
	if (!$r) {
		$r=$tab_suppleant;
		$affiche.= "<input type='hidden' name='spipbb_nouveau' value='1' />";
	}

	foreach($r as $champ => $val) {
		# elements de construction du champ
		list($form, $filtre, $intitule, $choix, $valeurs)=explode('|',$GLOBALS['champs_sap_spipbb'][$champ]['extra']);
		# statut de ce champ
		$statuts=explode(',',$GLOBALS['champs_sap_spipbb'][$champ]['extra_proposes']);

		# valider affichage du champ sur statut
		$aff_champ=false;
		if (in_array('tous',$statuts)) {
			$aff_champ=true;
		}
		else {
			if (in_array($aut_statut,$statuts)) {
				$aff_champ=true;
			}
		}

		#
		## h.10/11 et si on utilsait le truc de inc/extra.php
		## Construction des champs

		if($aff_champ) {
			if($form!="hidden") {
				if(!$intitule) $intitule=ucfirst($champ);
				$affiche.="<br /><b>$intitule</b><br />\n";
			}


			switch($form) {

				// complique car la valeur n'esst pas envoyee ar le nav si unchecked
				case "case":
				case "checkbox":
					#$affiche = ereg_replace("<br />$", "&nbsp;", $affiche);
					$affiche .= "<input type='hidden' name='spipbb_$champ' value='1' />"
							."<input type='checkbox' name='{spipbb_$champ}_check'";
					if ($val == 'true')
						$affiche .= " checked";
						$affiche .= " />";
					break;

				case "list":
				case "liste":
				case "select":
					$choix = explode(",",$choix);
					if (!is_array($choix)) {
						$affiche .= "Pas de choix d&eacute;finis.\n";
						break;
					}

					// prendre en compte les valeurs des champs
					// si elles sont renseignees
					$valeurs = explode(",",$valeurs);
					if($valeurs == explode(",",""))
						$valeurs = $choix ;

					$affiche .= "<select name='spipbb_$champ' ";
					$affiche .= "class='forml'>\n";
					$i = 0 ;
					while (list(, $choix_) = each($choix)) {
						$valsel = $valeurs[$i] ;
						$affiche .= "<option value=\"$valsel\"";
						if ($valsel == entites_html($val))
							$affiche .= " selected";
						$affiche .= ">$choix_</option>\n";
						$i++;
					}
					$affiche .= "</select>";
					break;

				case "radio":
					$choix = explode(",",$choix);
					if (!is_array($choix)) {
						$affiche .= "Pas de choix d&eacute;finis.\n";
						break;
					}
					$valeurs = explode(",",$valeurs);
					if($valeurs == explode(",",""))
						$valeurs = $choix ;

					$i=0;
					while (list(, $choix_) = each($choix)) {
						$affiche .= "<input type='radio' name='spipbb_$champ' ";
						$valsel = $valeurs[$i] ;
						if (entites_html($val)== $valsel)
							$affiche .= " checked";

						// premiere valeur par defaut
						if (!$val AND $i == 0)
							$affiche .= " checked";

						$affiche .= " value='$valsel'>$choix_</input>\n";
						$i++;
					}
					break;

				// A refaire car on a pas besoin de renvoyer comme pour checkbox
				// les cases non cochees
				## h. 10/11 ... euh ça marche comment ce truc 'multiple' ???
				case "multiple":
					$choix = explode(",",$choix);
					if (!is_array($choix)) {
						$affiche .= "Pas de choix d&eacute;finis.\n";
						break;
					}
					$affiche .= "<input type='hidden' name='spipbb_{$champ}' value='1' />";
					for ($i=0; $i < count($choix); $i++) {
						$affiche .= "<input type='checkbox' name='spipbb_$champ$i'";
						if (entites_html($val[$i])=="on")
							$affiche .= " checked";
						$affiche .= ">\n";
						$affiche .= $choix[$i];
						$affiche .= "</input>\n";
					}
					break;

				case "bloc":
				case "block":
					$affiche .= "<textarea name='spipbb_$champ' class='forml' rows='5' cols='40'>".entites_html($val)."</textarea>\n";
					break;

				## h.10/11 .. on a besoin
				case "hidden":
					$affiche.="<input type='hidden' name='spipbb_$champ' value='$val' />\n";
					break;
				## sert pas ici
				/*
				case "masque":
					$affiche .= "<span style='color: #555555'>".interdire_scripts($val)."</span>\n";
					break;
				*/
				case "ligne":
				case "line":
				default:
					$affiche .= "<input type='text' name='spipbb_$champ' class='forml'\n";
					$affiche .= " value=\"".entites_html($val)."\" size='40'>\n";
					break;
			}
		}
	}

	$affiche.="<div style='text-align:right;'>";
	$affiche.="<input type='submit' name='modifier' value='"._T('bouton_modifier')."' class='fondo' />\n";
	$affiche.="</div>";

	$retour= ajax_action_auteur('spipbb_editer_infos',$id_auteur,'auteur_infos','id_auteur='.$id_auteur,$affiche);
	return $retour;
}
?>