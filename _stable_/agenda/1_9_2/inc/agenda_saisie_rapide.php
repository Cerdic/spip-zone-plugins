<?php

if ($GLOBALS['spip_version_code']<1.92){
 function set_request($var, $val = NULL) {
	unset($_GET[$var]);
	unset($_POST[$var]);
	if ($val !== NULL) $_GET[$var] = $val;
 }
}

// retourne un tableau de mots ou d'expressions a partir d'un texte
function Agenda_retourne_liste_mots($texte) {
	$texte = filtrer_entites(trim($texte));
	$texte = preg_replace("/[\|\s\t\n\r]+/", " ", $texte);
	$split = split('"', $texte);
	$c = count($split);
	for($i=0; $i<$c; $i++) if ($i & 1) $split[$i] = preg_replace('/[ ,]+/', '+', trim($split[$i]));
	$texte = join('', $split);
	$texte = preg_replace("/ *,+ */","\t", $texte);
	$texte = preg_replace("/\++/"," ", $texte);
	return array_unique(split("\t", trim($texte)));
}

function Agenda_compile_texte_saisie_rapide($texte) {
	$t = preg_split(",[\n\r]+,", html_entity_decode($texte));
	foreach($t as $e=>$v) {
		$t[$e] = trim(str_replace("\t", " ", $t[$e]));
		if (ereg ("([0-9]{1,2})/([0-9]{1,2})/?([0-9]{4})?-?([0-9]{1,2})?/?([0-9]{1,2})?/?([0-9]{4})? +".
		"([0-9]{1,2})?:?([0-9]{1,2})?-?([0-9]{1,2})?:?([0-9]{1,2})? *".
		'" *([^ ^"][^"]*) *" *("([^"]*)")? *("([^"]*)")? *'.
		'((MOTS|REP) *=.*)?', $t[$e]=trim($t[$e]), $regs)) {
			$evenement_horaire = true;
			// annee_debut omise ou nulle
			if(!intval($regs[3])) $regs[3]=date('Y', time());
			// annee_fin omise ou nulle
			if(!intval($regs[6])) $regs[6]=$regs[3]; ;
			// heure_fin omise
			if($regs[9].$regs[10]=='') { $regs[9]=$regs[7]; $regs[10]=$regs[8]; }
			if ($regs[7].$regs[8].$regs[9].$regs[10]=='') $evenement_horaire=false;
			// date_fin omise
			if($regs[4].$regs[5]=='') { $regs[4]=$regs[1]; $regs[5]=$regs[2]; }
			// format complet
			for ($i=0;$i<=10;$i++) $regs[$i]=sprintf("%02d", intval($regs[$i]));
			// cas des REP= et MOTS=
			$listes = preg_split('/(MOTS|REP) *= */', $regs[16], -1, PREG_SPLIT_DELIM_CAPTURE);
			$rep = $mots = array();
			foreach($listes as $i => $valeur)
				if ($i & 1)
					foreach($listes as $i => $valeur)
					if ($i & 1) {
						if ($valeur=='REP' && ereg('([0-9 /,]*)', $listes[$i+1], $regs2))
							$rep = array_merge($rep, Agenda_retourne_liste_mots($regs2[1]));
						elseif ($valeur=='MOTS')
							$mots = array_merge($mots, Agenda_retourne_liste_mots($listes[$i+1]));
					}
			// todo : mettre les mots au format des select du formulaire normal
			$mots_compiles = Agenda_verifie_les_mots_clefs($mots);
			// mettre les repetitions au format du textarea du formulaire normal
			$selected_rep = array();
spip_log($rep);
			foreach($rep as $k=>$r){
				$r =explode("/",$r);
				// annee omise
				if(!intval($r[2])) $r[2]=date('Y', time());
				// mois omis
				if(!intval($r[1])) $r[1]=date('m', time());
				$selected_rep[] = sprintf('%02d/%02d/%04d',$r[1], $r[0], $r[2]);
				$rep[$k] = mktime($regs[7], $regs[8], null, $r[1], $r[0], $r[2]);
			}
			$selected_rep = join(',',$selected_rep);
			// remise en forme en doubon : idem a un post ou idem a un spip_query
			$t[$e]=array_merge(array(
				'jour_evenement_debut' =>$regs[1],
				'mois_evenement_debut' =>$regs[2],
				'annee_evenement_debut' =>$regs[3],
				'heure_evenement_debut' =>$regs[7],
				'minute_evenement_debut' =>$regs[8],
				'jour_evenement_fin' =>$regs[4],
				'mois_evenement_fin' =>$regs[5],
				'annee_evenement_fin' =>$regs[6],
				'heure_evenement_fin' =>$regs[9],
				'minute_evenement_fin' =>$regs[10],
				'evenement_horaire' => $evenement_horaire,
				'evenement_titre' =>$regs[11],
				'evenement_lieu' =>$regs[13],
				'evenement_descriptif' =>$regs[15],
				'evenement_groupe_mot_select' => $mots_compiles['echo'],
				'evenement_repetitions' => $rep,
				'selected_date_repetitions' => $selected_rep
			), $mots_compiles['post']);
		}
		else {
			if (strlen($t[$e])) $t[$e]=array(); else unset($t[$e]);
		}
	}
	return $t;
}

// retourne le tableau des mots acceptes par groupe
function Agenda_verifie_les_mots_clefs($mots_envoyes) {
 	$les_mots_ok = $mots_compiles = array();
	// on recupere tous les mots cles sur les evenements
	$res = spip_query("SELECT * FROM spip_groupes_mots WHERE evenements='oui'");
	while ($row = spip_fetch_array($res,SPIP_ASSOC)){
		$id_groupe = $row['id_groupe'];
		$titre = supprimer_numero($row['titre']);
		$res2= spip_query("SELECT * FROM spip_mots WHERE id_groupe=".spip_abstract_quote($id_groupe));
		while ($row2 = spip_fetch_array($res2,SPIP_ASSOC)){
			$les_mots_ok[]=array(	'nb'=>0, 'id_mot'=>$row2['id_mot'], 'titre_mot'=> $row2['titre'],
											'id_groupe'=>$id_groupe, 'titre_groupe'=> $titre,
											'echo' => $titre.':'.$row2['titre']);
		}
	}
	$mots_compiles = array('echo'=>array(),'post'=>array());
	// on voit quels mots cles on retient...
	foreach($mots_envoyes as $mot) {
		if (preg_match('/((([^:]+):)?(.*))/', $mot, $regs))
			foreach($les_mots_ok as $mot_ok=>$tab){
				$test_mot_ok = ($tab['titre_mot']==$regs[4]) || ($tab['id_mot']==$regs[4]);
				$test_groupe_ok = (''==$regs[3]) || ($tab['titre_groupe']==$regs[3]) || ($tab['id_groupe']==$regs[3]);
				if ($test_mot_ok && $test_groupe_ok) {
					$mots_compiles['echo'][$tab['id_mot']] = $tab['echo'];
					$mots_compiles['post']["evenement_groupe_mot_select_".$tab['id_groupe']][$tab['id_mot']] = $tab['id_mot'];
					break;
				}
			}
	}
	return $mots_compiles;
}

function Agenda_formulaire_saisie_rapide_previsu() {
	global $spip_lang_right;
	$out = "";
	if ($evenements_saisie_rapide = _request('evenements_saisie_rapide')){
		$t = Agenda_compile_texte_saisie_rapide($evenements_saisie_rapide);
		$out .= "<div class='liste liste-evenements'>";
		$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		$table = array();
		$table[] = array('',
			'<strong>'._T('agenda:evenement_titre').'</strong>',
			'<strong>'. _T('agenda:evenement_lieu').'</strong>',
			'<strong>'._T('agenda:evenement_descriptif').'</strong>');
//spip_log($t);'<strong>'.
		foreach($t as $id_eve=>$eve) if (count($eve)) {
			$vals = array();
			$titre = typo($eve['evenement_titre']);
			$lieu = typo($eve['evenement_lieu']);
			$descriptif = typo($eve['evenement_descriptif']);
			$horaire = $eve['evenement_horaire'];
			$temp = $eve['annee_evenement_debut']."-".$eve['mois_evenement_debut']."-".$eve['jour_evenement_debut']." ".$eve['heure_evenement_debut'].":".$eve['minute_evenement_debut'];
			$date_debut = strtotime($temp);
			$temp = $eve['annee_evenement_fin']."-".$eve['mois_evenement_fin']."-".$eve['jour_evenement_fin']." ".$eve['heure_evenement_fin'].":".$eve['minute_evenement_fin'];
			$date_fin = strtotime($temp);

			$s = Agenda_afficher_date_evenement($date_debut, $date_fin, $horaire);
			if ($c = count($eve['evenement_repetitions'])){
				$s_rep = "";
				foreach($eve['evenement_repetitions'] as $rep){
					$rep_date_debut = $rep;
					$rep_date_fin = $rep_date_debut + $date_fin - $date_debut;
					$s_rep .= Agenda_afficher_date_evenement($rep_date_debut, $rep_date_fin, $horaire)."<br/>";
				}
				$s .= "<br/><div style=\"\">".bouton_block_invisible("repetitions_evenement_rapide_{$id_eve}");
				$s .= $c . "&nbsp;" . _T('agenda:evenement_repetitions');
				$s .= debut_block_invisible("repetitions_evenement_rapide_{$id_eve}");
				$s .= $s_rep;
				$s .= fin_block().'</div>';
			}
			$vals[] = $s;
			$vals[] = $titre;
			$vals[] = $lieu;
			$s = '';
			if ($c = count($eve['evenement_groupe_mot_select'])){
				$s .= "<br/>".bouton_block_invisible("mots_evenement_rapide_{$id_eve}");
				$s .= $c . "&nbsp;" . _T('public:mots_clefs');
				$s .= debut_block_invisible("mots_evenement_rapide_{$id_eve}");
				$s .= implode(", ", $eve['evenement_groupe_mot_select']);
				$s .= fin_block();
			}
			$vals[] = propre($descriptif) . $s;
			$table[] = $vals;

		} // foreach($t as $id_eve=>$eve) if (count($eve))
		else $table[] = array('', '?', '', '');

		$largeurs = array('', '', '', '');
		$styles = array('arial11', 'arial11', 'arial11', 'arial11');
		$out .= afficher_liste($largeurs, $table, $styles);

		$out .= "</table></div>\n";
		$out .= "<span style='display:none'>";
		$out .= "<textarea name='evenements_saisie_rapide' rows='10' class='forml' >";
		$out .= _request('evenements_saisie_rapide');
		$out .= "</textarea>";
		$out .= "</span>";
		$out .= "<div style='text-align:$spip_lang_right;'><input class='fondo' type='submit' value='"._T('bouton_enregistrer')."'></div>";
 	}
 	return $out;
}

function Agenda_formulaire_saisie_rapide() {
	global $spip_lang_right;
	$out = "";
	//$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_AGENDA."/img_pack/agenda-24.png",true, "", _T('saisierapide:saisie_rapide_votre_liste'));
	$out .= _T('saisierapide:votre_liste_infos');
	$out .= "<input type='hidden' name='saisie_rapide' value='1' />";
  $out .= "<textarea name='evenements_saisie_rapide' rows='10' class='forml' >";
  $out .= _request('evenements_saisie_rapide');
  $out .= "</textarea>";
  $out .= "<a href='".generer_url_ecrire("saisie_rapide", "id_article=$id_article")."'>"._T('saisierapide:saisie_rapide_reset')."</a>";
  $out .= "<div style='text-align:$spip_lang_right;'><input class='fondo' type='submit' value='"._T('previsualiser')."'></div>";
  $out .= "<p>";
  //$out .= fin_cadre_enfonce(true);

  $out .= debut_cadre_formulaire('',true);
  $out .= propre(_T('saisierapide:explications'))
  			. '<hr />'
			. propre(_T('saisierapide:exemples', array('Y' => date('Y', time()))));
  $out .= fin_cadre_formulaire(true);
  return $out;
}

?>