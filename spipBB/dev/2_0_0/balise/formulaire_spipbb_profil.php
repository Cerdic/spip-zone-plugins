<?php
#------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                          #
#  File    : balise/formulaire_spipbb_profil                 #
#  Init    : James-Booz                                      #
#  Modif   : scoty pour GAFoSPIP v. 0.4                      #
#  Authors :                                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs   #
#  Contact : chryjs!@!free!.!fr                              #
#------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
include_spip('inc/spipbb_common');
include_spip('inc/traiter_imagerie');
include_spip('inc/filtres');
spipbb_log('included',2,__FILE__);

function balise_FORMULAIRE_SPIPBB_PROFIL ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_SPIPBB_PROFIL', array('id_auteur'));
}

function balise_FORMULAIRE_SPIPBB_PROFIL_stat($args, $filtres) {
	// Pas d'id_auteur ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_SPIPBB_PROFIL',
					'motif' => 'AUTEURS')), '');
	return ($args);
}

function balise_FORMULAIRE_SPIPBB_PROFIL_dyn($id_auteur) {
	$statut=$GLOBALS["auteur_session"]["statut"];
	$id_auteur_session=$GLOBALS["auteur_session"]['id_auteur'];

	# detail infos sur auteur
	$auteur=spipbb_donnees_auteur($id_auteur_session);
	$echec=""; #initialisation

	# chps spip passer dans le formulaire
	$new_pass = _request('new_pass');
	$new_pass2 = _request('new_pass2');
	$auteur_bio = corriger_caracteres(_request('bio'));
	$auteur_pgp = corriger_caracteres(_request('pgp'));
	$auteur_nom_site = corriger_caracteres(_request('nom_site')); //h.?? attention mix avec $nom_site_spip ;(
	$auteur_url_site = vider_url(_request('url_site'));
	//$auteur_email = _request('email'); On ne change pas l'email... seuls les admins le peuvent (dans l'interface privée)
	$nouveau = _request('nouveau'); // nouveau == 1 si date_crea_spipbb est vide

	$traiter_chps=array(); // c: 21/12/7 Bug report BB du 2
	$renvois_chps=array(); // c: 23/12/7 Bug report Jack sur gmane

	if($modif=_request('modif')) {

		// changement de pass, a securiser en jaja ?
		if ($new_pass AND ($statut != '5poubelle')
					AND $auteur['login'] AND $auteur['source'] == 'spip') {
			if ($new_pass != $new_pass2)
				$echec .= _T('info_passes_identiques');
			else if ($new_pass AND strlen($new_pass) < 6)
				$echec .= _T('info_passe_trop_court');
			else {
				$modif_login = true; #h.??
				$auteur_new_pass = $new_pass;
			}
		}

		if ($modif_login) {#h.??
			#include_spip('inc/session');
			#zap_sessions ($auteur['id_auteur'], true);
			if ($id_auteur_session == $auteur['id_auteur'])
				supprimer_sessions($GLOBALS['spip_session']);
		}

		if ($new_pass) {
			if (!function_exists('generer_htpass')) include_spip('inc/acces');
			$htpass = generer_htpass($new_pass);
			$alea_actuel = creer_uniqid();
			$alea_futur = creer_uniqid();
			$pass = md5($alea_actuel.$new_pass);
			// c: 10/2/8 compat multibases
			/*
			$query_pass = " pass='$pass', htpass='$htpass',
							alea_actuel='$alea_actuel',
							alea_futur='$alea_futur', ";
							*/
			$query_pass = array("pass"=>$pass,"htpass"=>$htpass,
							"alea_actuel"=>$alea_actuel,
							"alea_futur"=>$alea_futur);
			effacer_low_sec($auteur['id_auteur']);
		} else
			$query_pass = array();

		#
		# Extra : recup champs extra (gaf ou pas) passer dans le formulaire
		#

		$add_extra = array();// c: 10/2/8 compat multibases
		if ($GLOBALS['champs_extra']) {
		# tous extra (auteurs) existants
			$ser_extra = lire_config("~".$id_auteur_session);
			$ts_extra = @unserialize($ser_extra);
			if (!is_array($ts_extra)) $ts_extra=$ser_extra; // c: 15/4/8 recuperation des anciens formats mal geres. a terme cette manip deviendra obsolete a priori
		# traite extra 'auteur' postes (return array)
			$recup_form = spipbb_extra_recup_saisie("auteurs");
		# merge (remplace dans 'ts_extra' si present par 'recup_form')
			#$n_extra=array();
			if (!is_array($ts_extra)) {
				spipbb_log("ts extra not array:".$ts_extra,3,__FILE__);
				$ts_extra=array($ts_extra);
			}
			if (!is_array($recup_form)) {
				spipbb_log("recup_form not array:".$recup_form,3,__FILE__);
				$recup_form=array($recup_form);
			}

			$n_extra = array_merge($ts_extra,$recup_form); // array verifies
			$extra = serialize($n_extra);
			$add_extra ["extra"]= $extra;// c: 10/2/8 compat multibases c: 15/4/8 pas de _q
			# recup champs extra pour retour form (ENV)
			foreach($n_extra as $k => $v) {
				$renvois_chps[$k]=$v;
			}
		}

		#
		# Table support : recup chps passer dans formulaire
		#

		# recup config gafospip
		$support_auteurs=lire_config("spipbb/support_auteurs");
		$table_support=lire_config("spipbb/table_support");
		#$champs_gaf = lire_config("spipbb/champs_gaf");

		if($support_auteurs=='table' && $table_support!='') {
			# pour les champs connus GAF on attribue la val. recup
			foreach($GLOBALS['champs_sap_spipbb'] as $chp => $def) {
				$filtres_recup=$def['filtres_recup'];
				if($filtres_recup!='' && function_exists($filtres_recup)) {
					$traiter_chps[$chp] = $filtres_recup(_request($chp));
				}
				else {
					$traiter_chps[$chp] = _request($chp);
				}
			}
		}

		if(empty($echec)) {
			#  maj spip
			// c: 15/4/8 : 1 on a aucune raison de MAJ le nom donc on zappe
			// de plus sql_updateq => pas besoin de _q car quote fait par spip_xx_cite
			@sql_updateq("spip_auteurs",array_merge($query_pass,array(
				login=>$auteur['login'],
				bio=>$auteur_bio,
				//email=>$auteur_email,
				nom_site=>$auteur_nom_site,
				url_site=>$auteur_url_site,
				pgp=>$auteur_pgp,
				statut=>$auteur['statut']),
				$add_extra),"id_auteur=".$auteur['id_auteur']);

			# maj table support
			if(count($traiter_chps)>=1) {
				$set=array();// c: 10/2/8 compat multibases
				foreach($traiter_chps as $k => $v) {
					if($k=="date_crea_spipbb" && $v=='') {
						$set[$k]="NOW()";
					}
					elseif($k=="refus_suivi_thread" && is_array($v)) {
						$set[$k]=join(',',$v); // c: 15/4/8 pas de _q avec updateq et insertq
					}
					else {
						$set[$k]=$v; // c: 15/4/8 pas de _q avec updateq et insertq
					}
				}
				if($nouveau) {
					@sql_insertq("spip_".$table_support,array_merge(array("id_auteur"=>$auteur['id_auteur']),$set));
				}
				else {
					@sql_updateq("spip_".$table_support, $set, "id_auteur=".$auteur['id_auteur']);
				}
			}
		}
	}

	# retour
	#
	$ch_retour = array (
		'nom' => $auteur['nom'],
	//	'email' => $auteur_email,
		'url_site' => $auteur_url_site,
		'nom_site' => $auteur_nom_site,
		'pgp' => $auteur_pgp,
		'bio' => $auteur_bio,
		'echec' => $echec
		);
	$ch_retour=array_merge($ch_retour,$renvois_chps,$traiter_chps); // array deja init
	return array('formulaires/formulaire_spipbb_profil', 0,$ch_retour);
} // balise_FORMULAIRE_SPIPBB_PROFIL_dyn

# base  script :inc/extra.php : function extra_recup_saisie()
# qq modifs, retourne un array()
// recupere les valeurs postees pour reconstituer l'extra
// http://doc.spip.org/@extra_recup_saisie
function spipbb_extra_recup_saisie($type, $c=false) {
	$champs = $GLOBALS['champs_extra'][$type];
	$extra = Array();
	if (is_array($champs)) {
		foreach($champs as $champ => $config) {
			if (($val = _request("$champ",$c)) !== NULL) {
				list($style, $filtre, , $choix,) = explode("|", $config);
				list(, $filtre) = explode(",", $filtre);
				switch ($style) {
				case "multiple":
					$choix =  explode(",", $choix);
					$multiple = array();
					for ($i=0; $i < count($choix); $i++) {
						$val2 = _request("$champ$i",$c);
						if ($filtre && function_exists($filtre))
							 $multiple[$i] = $filtre($val2);
						else
							$multiple[$i] = $val2;
					}
					$extra[$champ] = $multiple;
					break;

				case 'case':
				case 'checkbox':
					if (_request("{$champ}_check") == 'on')
						$val = 'true';
					else
						$val = 'false';
					// pas de break; on continue

				default:
					#traiter date prim enreg.
					if($champ=='date_crea_spipbb' && ($val=='' || $val==false )) {
						$val=date('Y-m-d H:i:s');
					}
					if($champ=='refus_suivi_thread' && is_array($val)) {
						$val = join(',',$val);
					}
					if ($filtre && function_exists($filtre))
						$extra[$champ] = $filtre($val);
					else
						$extra[$champ] = $val;
					break;
				}
			}
		}
	}
	return $extra;
} // spipbb_extra_recup_saisie

?>