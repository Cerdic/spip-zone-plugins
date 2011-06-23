<?php
/******************************************************************************************
 * Dépublication permet de faire expirer les droits d'un auteur à une date donnée.		  *
 * Copyright (C) 2005-2010 Nouveaux Territoires support<at>nouveauxterritoires.fr		  *
 * http://www.nouveauxterritoires.fr							    					  *
 *                                                                                        *
 * Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes *
 * de la Licence Publique Générale GNU publiée par la Free Software Foundation            *
 * (version 3).                                                                           *
 *                                                                                        *
 * Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       *
 * ni explicite ni implicite, y compris les garanties de commercialisation ou             *
 * d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  *
 * pour plus de détails.                                                                  *
 *                                                                                        *
 * Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    *
 * en même temps que ce programme ; si ce n'est pas le cas,								  * 
 * regardez http://www.gnu.org/licenses/ 												  *
 * ou écrivez à la	 																	  *
 * Free Software Foundation,                                                              *
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   *
 ******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');   // for spip presentation functions
include_spip('inc/config');   		// for spip presentation functions
include_spip('inc/layer');          // for spip layer functions
include_spip('inc/utils');          // for _request function
include_spip('inc/plugin');         // xml function

include_spip('inc/date');

// $id, $type = 'auteurs_infos'
function inc_depublication_auteurs_dist($id, $flag='', $statut='', $type= 'auteur_infos') {
	
	global $spip_lang_left, $spip_lang_right, $options;

	//ne fait rien si le plugin n'est pas initialisé ie n'a pas de version
	if (!isset($GLOBALS['meta']['depublication_base_version'])) {
		return "";
	}
	
	$depub_auteur = lire_config('depublication/depub_auteur');
	if (! $depub_auteur) {
		return "";
	}
	$date = sql_getfetsel("depublication", "spip_auteurs_depublication", "id_auteur=$id"); 
	if ($date == '0000-00-00 00:00:00') {
		$date = '';
	}
	
	
		if (preg_match("'([0-9]{4})-([0-9]{2})-([0-9]{2})( ([0-9]{2}):([0-9]{2}))?'", $date, $regs)) {
			$annee = $regs[1];
			$mois = $regs[2];
			$jour = $regs[3];
			$heures = $regs[5];
			$minutes = $regs[6];
		} else {
		
			// on regarde la conf pour savoir la valeur à ajouter à la date
			$delai = lire_config('depublication/delai');
			$delaiunite = lire_config('depublication/delaiunite');
			
			$secondes = date('s');
			$minutes = date('i');
			$heures = date('H');
			$jours = date('d');
			$semaines = date('d');
			$mois = date('m');
			$annees = date('Y');
			
			switch ($delaiunite) {
			
				case 'secondes':
					$secondes += $delai;
					break;
				
				case 'minutes':
					$minutes += $delai;
					break;
				case 'heures':
					$heures += $delai;
					break;
				case 'jours':
					$jours += $delai;
					break;
				case 'semaines':
					$semaines += ($delai * 7);
					break;
				case 'mois':
					$mois += $delai;
					break;
				case 'annees':
					$annees += $delai;
					break;
			}
			
			$dateDelai = mktime($heures, $minutes, $secondes, $mois , $jours, $annees);
			
			$annee = date('Y', $dateDelai);
			$mois = date('m', $dateDelai);
			$jour = date('d', $dateDelai);
			$heures = date('H', $dateDelai);
			$minutes = date('i', $dateDelai);
			
		}
		
		
		
		if ($date != '') {
			$date = 'le '.majuscules(affdate_heure($date));
		} else {
			$date = _T('depublication:nodate');
		}
	
	include_spip('inc/autoriser');
	if (autoriser('depublication',$type,$id,null,array('statut'=>$statut))) {
	
		/*$js = "size='1' class='fondl'
			onchange=\"findObj_forcer('valider_depublication').style.visibility='visible';\"";*/
		
		if ($date != _T('depublication:nodate')) {
			$js = " onchange=\"findObj_forcer('valider_depublication').style.visibility='visible';\"";
		}
		
		$idom = "depublication" . "_objet_$id";
		
				$bouton = bouton_block_depliable(_T('depublication:depublication_auteur')." :<span  align='center'>".$date."</span>",'ajax',$idom);
				/*$masque = '<input type="checkbox" name="supp" id="supp" value="supp"/><label for="supp">'._T('depublication:supp_date_auteur').'</label><br/><br/>'
					. _T('depublication:date_depub_auteur')."<br/>"
					. afficher_jour($jour, "name='jour' id='jour' $js", true) 
					. afficher_mois($mois, "name='mois' id='mois' $js", true)
					. afficher_annee($annee, "name='annee' id='annee' $js")
					. (' - '
						. afficher_heure($heures, "name='heure' id='heure' $js")
			      		. afficher_minute($minutes, "name='minute' id='minute' $js"))
			  		. "&nbsp;\n";*/
			  		
			  	$masque =
					 //_T('depublication:date_depub_auteur')."<br/>"
					afficher_jour($jour, "name='jour' id='jour' $js", false)
					. afficher_mois($mois, "name='mois' id='mois' $js", false)
					. afficher_annee($annee, "name='annee' id='annee' $js", $debut_date_publication)
					. (' - '
						. afficher_heure($heures, "name='heure' id='heure' $js")
						. afficher_minute($minutes, "name='minute' id='minute' $js"))
					. "&nbsp;\n"
					.(($date == _T('depublication:nodate')) ? '' : '<br/><br/><input type="checkbox" name="supp" id="supp" value="supp" '.$js.'/><label for="supp">'._T('depublication:supp_date_article').'</label>');
			  		
			  		
			  		
			  	/*$contenu = "<div style='margin: 5px; margin-$spip_lang_left: 20px;'>"
					.  ajax_action_post("depublication_auteur", 
						"$id",
						$script,
						"id=$id",
						$masque,
						_T('bouton_changer'),
				    	   " class='fondo'", "",
						"&id=$id")
					.  "</div>";*/
					
				$contenu = "<div style='margin: 5px; margin-$spip_lang_left: 20px;'>"
						.  ajax_action_post("depublication_auteurs",
							"$id",
							$script,
							"id=$id",
							$masque,
							_T('bouton_changer'),
							(($date == _T('depublication:nodate')) ? '' : " class='visible_au_chargement'")." id='valider_depublication' style='float: right' ", "",
							"&id=$id")
						.  "</div>";
				

	
				$res = debut_cadre_enfonce(find_in_path("img/depublication-24.png"), true, "", $bouton)
					. debut_block_depliable($flag === 'ajax',$idom)
					. $contenu
					. fin_block()
					. fin_cadre_enfonce(true);
				
				return ajax_action_greffe("depublication_auteurs",$id, $res);
				
			
	} else {
	
		$contenu = "<b>"._T('depublication:depublication_auteur')." :  <span  align='center'>".$date."</span></b>";
		$res = debut_cadre_enfonce(find_in_path("img/depublication-24.png"), true, "", $bouton)
			. $contenu
			. fin_cadre_enfonce(true);


		return ajax_action_greffe("depublication_auteurs",$id, $res);		
	}
}



?>