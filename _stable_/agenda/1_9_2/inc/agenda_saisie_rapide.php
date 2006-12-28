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
	$t=split("\n",html_entity_decode($texte));	
	foreach($t as $e=>$v) {
		$t[$e]=trim(str_replace("\t", " ", $t[$e]));
		if (ereg ("([0-9]{1,2})/([0-9]{1,2})/?([0-9]{4})?-?([0-9]{1,2})?/?([0-9]{1,2})?/?([0-9]{4})? +".
		"([0-9]{1,2})?:?([0-9]{1,2})?-?([0-9]{1,2})?:?([0-9]{1,2})? *".
		'" *([^ ^"][^"]*) *" *("([^"]*)")? *("([^"]*)")? *'.
		'((MOTS|REP) *=.*)?', $t[$e]=trim($t[$e]), $regs)) {
			$evenement_horaire = true;
			// annee_debut omise
			if($regs[3]=='') $regs[3]=date('Y', time());
			// annee_fin omise
			if($regs[6]=='') $regs[6]=$regs[3]; ;
			// heure_fin omise
			if($regs[9].$regs[10]=='') { $regs[9]=$regs[7]; $regs[10]=$regs[8]; }   
			if ($reg[7].$reg[8].$reg[9].$reg[10]=='') $evenement_horaire=false;
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
			$selected_rep = "";
			foreach($rep as $k=>$r){
				$r =explode("/",$r);
				$selected_rep .= ",".sprintf('%02d',$r[1])."/".sprintf('%02d',$r[0])."/".sprintf('%04d',$r[2]);
				$rep[$k] = mktime($reg[7],$regs[8],null,$r[1],$r[0],$r[2]);
			}
			$selected_rep = substr($selected_rep,1);
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
			),$mots_compiles['post']);
		} 
		else {
			if ($t[$e]!="") $t[$e]=array(); else unset($t[$e]);
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
		foreach($t as $eve) {
			$vals = array();
			$titre = typo($eve['evenement_titre']);
			$lieu = typo($eve['evenement_lieu']);
			$descriptif = typo($eve['evenement_descriptif']);
			$horaire = $eve['evenement_horaire'];
			$date_debut = strtotime($eve['jour_evenement_debut']."-".$eve['mois_evenement_debut']."-".$eve['annee_evenement_debut']." ".$eve['heure_evenement_debut'].":".$eve['minute_evenement_debut']);
			$date_fin = strtotime($eve['jour_evenement_fin']."-".$eve['mois_evenement_fin']."-".$eve['annee_evenement_fin']." ".$eve['heure_evenement_fin'].":".$eve['minute_evenement_fin']);
			
			$s = Agenda_afficher_date_evenement($date_debut,$date_fin, $horaire);
			$s_rep = "";
			$count_rep = 0;
			foreach($eve['evenement_repetitions'] as $rep){
				$rep_date_debut = $rep;
				$rep_date_fin = $rep_date_debut+$date_fin-$date_debut;
				$s_rep .= Agenda_afficher_date_evenement($rep_date_debut,$rep_date_fin,$horaire)."<br/>";
				//$s_rep .= $rep."<br/>";
				$count_rep++;
			}
			if (strlen($s_rep)){
				$s .= "<br/>".bouton_block_invisible("repetitions_evenement_$id_evenement");
				$s .= "$count_rep ". _T('agenda:evenement_repetitions');
				$s .= debut_block_invisible("repetitions_evenement_$id_evenement");
				$s .= $s_rep;
				$s .= fin_block();
			}
			$vals[] = $s;

			$vals[] = $titre;

			$vals[] = $lieu;
			
			$vals[] = propre($descriptif);
			
			$vals[] = implode(", ",$eve['evenement_groupe_mot_select']);
		
			$table[] = $vals;
			
		}
	
		$largeurs = array('', '', '', '', '');
		$styles = array('arial11', 'arial11', 'arial2', 'arial11', 'arial11');
		$out .= afficher_liste($largeurs, $table, $styles, false);
	
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
	$out .= _T('saisierapide:saisie_rapide_votre_liste_infos');
	$out .= "<input type='hidden' name='saisie_rapide' value='1' />";
  $out .= "<textarea name='evenements_saisie_rapide' rows='10' class='forml' >";
  $out .= _request('evenements_saisie_rapide');
  $out .= "</textarea>";
  $out .= "<a href='".generer_url_ecrire("saisie_rapide", "id_article=$id_article")."'>"._T('saisierapide:saisie_rapide_reset')."</a>";
  $out .= "<div style='text-align:$spip_lang_right;'><input class='fondo' type='submit' value='"._T('previsualiser')."'></div>";
  $out .= "<p>";
  //$out .= fin_cadre_enfonce(true);

  $out .= debut_cadre_formulaire('',true);
  $out .= _T('saisierapide:saisie_rapide_explications');
  $out .= fin_cadre_formulaire(true);
  return $out;
}

?>