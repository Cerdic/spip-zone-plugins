<?php
/*
+-------------------------------------------+
| / orig GAFoSPIP v. 0.5 - 21/08/07 - spip 1.9.2
+-------------------------------------------+
| Gestion Alternative des Forums SPIP
+-------------------------------------------+
| Hugues AROUX - SCOTY @ koakidi.com
+-------------------------------------------+
| Script origine : spipBB 
| Modif . scoty pour GAFoSPIP v. 0.4
+-------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip ("inc/spipbb");

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
	$auteur=spipbb_auteur_infos($id_auteur_session);

	# chps spip passer dans le formulaire
	$new_pass = _request('new_pass');
	$new_pass2 = _request('new_pass2');
	$auteur_bio = corriger_caracteres(_request('bio'));
	$auteur_pgp = corriger_caracteres(_request('pgp'));
	$auteur_nom_site = corriger_caracteres(_request('nom_site')); //h.?? attention mix avec $nom_site_spip ;(
	$auteur_url_site = vider_url(_request('url_site'));
	$auteur_email = _request('email');
	$nouveau = _request('nouveau');

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
			$htpass = generer_htpass($new_pass);
			$alea_actuel = creer_uniqid();
			$alea_futur = creer_uniqid();
			$pass = md5($alea_actuel.$new_pass);
			$query_pass = " pass='$pass', htpass='$htpass', 
							alea_actuel='$alea_actuel', 
							alea_futur='$alea_futur', ";
			effacer_low_sec($auteur['id_auteur']);
		} else
			$query_pass = '';
		
		
		#
		# Extra : recup champs extra (gaf ou pas) passer dans le formulaire
		#
		$renvois_chps=array();
		$add_extra = '';
		if ($GLOBALS['champs_extra']) {
		# tous extra (auteurs) existants
			$ts_extra = lire_config("~".$id_auteur_session);
		# traite extra 'auteur' postes (return array)
			$recup_form = spipbb_extra_recup_saisie("auteurs");
		# merge (remplace dans 'ts_extra' si present par 'recup_form')
			#$n_extra=array();
			$n_extra = array_merge($ts_extra,$recup_form);

			$extra = serialize($n_extra);
			
			$add_extra = ", extra="._q($extra)." ";
			
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
		
		$traiter_chps=array();
		
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
		
		
		if(!$echec) {
			#  maj spip
			sql_query("UPDATE spip_auteurs SET $query_pass
				nom="._q($auteur['nom']).",
				login="._q($auteur['login']).",
				bio="._q($auteur_bio).",
				email="._q($auteur_email).",
				nom_site="._q($auteur_nom_site).",
				url_site="._q($auteur_url_site).",
				pgp="._q($auteur_pgp).",
				statut="._q($auteur['statut'])."
				$add_extra
				WHERE id_auteur=".$auteur['id_auteur']);
			
			# maj table support
			if(count($traiter_chps)>=1) {
				$set='';
				foreach($traiter_chps as $k => $v) {
					if($k=="date_crea_spipbb" && $v=='') {
						$set.= ",".$k."=NOW()";
					}
					elseif($k=="refus_suivi_thread" && is_array($v)) {
						$set.= ",".$k."="._q(join(',',$v));
					}
					else {
						$set.= ",".$k."="._q($v);
					}
				}
				$set=substr($set,1);
				if(strlen($set)>0) { $sep = ","; }
				
				if($nouveau) {
					sql_query("INSERT INTO spip_".$table_support." SET id_auteur=".$auteur['id_auteur']." ".$sep.$set);
				}
				else {
					sql_query("UPDATE spip_".$table_support." SET $set WHERE id_auteur=".$auteur['id_auteur']);	
				}
			}
		}
	
	
	}
	
	# retour
	#
	$ch_retour = array (
		'nom' => $auteur['nom'],
		'email' => $auteur_email,
		'url_site' => $auteur_url_site,
		'nom_site' => $auteur_nom_site,
		'pgp' => $auteur_pgp,
		'bio' => $auteur_bio,
		'echec' => $echec
		);
	$ch_retour=array_merge($ch_retour,$renvois_chps,$traiter_chps);
	return array('formulaires/formulaire_spipbb_profil', 0,$ch_retour);

}

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
}

?>
