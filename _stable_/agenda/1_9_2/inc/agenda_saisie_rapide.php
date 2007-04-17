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
	$texte = preg_replace("/[\|\s\t\n\r]+/", " ", $texte);
	$split = split('"', $texte);
	$c = count($split);
	for($i=0; $i<$c; $i++) if ($i & 1) $split[$i] = preg_replace('/[ ,]+/', '+', trim($split[$i]));
	$texte = join('', $split);
	$texte = preg_replace("/ *,+ */","\t", $texte);
	$texte = preg_replace("/\++/"," ", $texte);
	return array_unique(split("\t", trim($texte)));
}

define('_format_heure', '([0-2]\d|\d)?:?([0-5]\d|\d)?');
define('_format_annee', '([12]\d{3}|\d{2}|\d{1})?');
define('_format_date1', '([1-9]|\d{2})/([1-9]|\d{2})/?'._format_annee.' ?'._format_heure);
define('_format_date2', '- ?([1-9]|\d{2})/([1-9]|\d{2})/?'._format_annee);
define('_format_descrip', '" ?([^ ^"][^"]*) ?" ?("([^"]*)")? ?("([^"]*)")? ?((MOTS|REP) ?=.*)?');

function Agenda_compile_une_ligne($s0) {
		$s = trim($s0);
		$s = preg_replace(",\s+,", " ", $s);

		// recherche de la date_debut
		if (!preg_match(","._format_date1.",", $s, $r1)) return false;
		list(,$s) = split($r1[0], $s, 2); $s = trim($s);
		// annee_debut omise et format
		if($r1[3]=='') $r1[3]=date('Y', time());
			else $r1[3] = date("Y", mktime(0,0,0,1,1,intval($r1[3])));
		// heure_debut omise
		$evenement_horaire = $r1[4].$r1[5]!='';
		if($r1[4]=='') $r1[4]='00';
		if($r1[5]=='') $r1[5]='00';

		// recherche de la date_fin
		if (preg_match(","._format_date2.",", $s, $r2)) $s = trim(substr($s, strlen($r2[0])));
		if (preg_match(',-? ?'._format_heure.',', $s, $r3)) $s = trim(substr($s, strlen($r3[0])));
		// date_fin omise
		if($r2[1]=='') $r2[1]=$r1[1];
		if($r2[2]=='') $r2[2]=$r1[2];
		if($r2[3]=='') $r2[3]=$r1[3];	
			// annee en format 0000
			else $r2[3] = date("Y", mktime(0,0,0,1,1,intval($r2[3])));
		// heure_fin omise
		$evenement_horaire |= $r3[1].$r3[2]!='';
		$r2[4]=!intval($r3[1])?'00':$r3[1];
		$r2[5]=!intval($r3[2])?'00':$r3[2];
		if("$r2[4]$r2[5]"==="0000" && "$r1[1]$r1[2]$r1[3]"==="$r2[1]$r2[2]$r2[3]")
			{ $r2[4]=$r1[4]; $r2[5]=$r1[5]; }
		// date_fin anterieure a date_debut
		if (mktime($r2[4], $r2[5], 0, $r2[2], $r2[1], $r2[3]) < mktime($r1[4], $r1[5], 0, $r1[2], $r1[1], $r1[3])) {
			$temp = array($r2[4], $r2[5], $r2[2], $r2[1], $r2[3]); 
			list($r2[4], $r2[5], $r2[2], $r2[1], $r2[3]) = array($r1[4], $r1[5], $r1[2], $r1[1], $r1[3]);
			list($r1[4], $r1[5], $r1[2], $r1[1], $r1[3]) = $temp;
		}
		// merge et format '00'
		unset($r2[0]);
		$r1 = array_merge($r1, $r2);
		for ($i=1;$i<=10;$i++) if(strlen($r1[$i])<2) $r1[$i] = sprintf("%02d", intval($r1[$i]));

		// recherche du reste de la chaine
		if (!preg_match(","._format_descrip.",", $s, $r2)) return false;
		// cas des REP= et MOTS=
		$listes = preg_split('/(MOTS|REP) *= */', $r2[6], -1, PREG_SPLIT_DELIM_CAPTURE);
		$selected_rep = $rep = $mots = array();
		foreach($listes as $i => $valeur) if ($i & 1) {
			if ($valeur=='REP' && preg_match('@([0-9 /,]+)@', $listes[$i+1], $regs2))
				$rep = array_merge($rep, Agenda_retourne_liste_mots($regs2[1]));
			elseif ($valeur=='MOTS')
				$mots = array_merge($mots, Agenda_retourne_liste_mots($listes[$i+1]));
		}
		// mettre les repetitions au format du textarea du formulaire normal
		foreach($rep as $k=>$r){
			$r =explode("/", $r);
			// annee omise : annee de l'evenement source
			if(!intval($r[2])) $r[2]=$r1[3];
			// mois omis : mois de l'evenement source
			if(!intval($r[1])) $r[1]=$r1[2];
			$selected_rep[] = sprintf('%02d/%02d/%04d', $r[1], $r[0], $r[2]);
			$rep[$k] = mktime($r1[4], $r1[5], 0, $r[1], $r[0], $r[2]);
		}
		// on renvoie un tableau avec toutes les donnees interpretees
		return array(0=>$r1, 'horaire'=>$evenement_horaire?'oui':'non', 
			'titre'=>$r2[1], 'lieu'=>$r2[3], 'descrip'=>$r2[5], 'rep'=>$rep, 'selected_rep'=>$selected_rep, 'mots'=>$mots);
}

function Agenda_compile_texte_saisie_rapide($texte) {
	$t = preg_split(",[\n\r]+,", html_entity_decode($texte));
	foreach($t as $e=>$v) {
		if (!$r = Agenda_compile_une_ligne($v)) {
			if (strlen(trim($v))) $t[$e]=array(); else unset($t[$e]);
			continue;
		}
		// todo : mettre les mots au format des select du formulaire normal 
		// pour faciliter la selection par l'utilisateur
		$mots_compiles = Agenda_verifie_les_mots_clefs($r['mots']);
		$selected_rep = join(',', $r['selected_rep']);
		// remise en forme en doubon : idem a un post ou idem a un spip_query
		$t[$e]=array_merge(array(
			'jour_evenement_debut' => $r[0][1],
			'mois_evenement_debut' => $r[0][2],
			'annee_evenement_debut' => $r[0][3],
			'heure_evenement_debut' => $r[0][4],
			'minute_evenement_debut' => $r[0][5],
			'jour_evenement_fin' => $r[0][6],
			'mois_evenement_fin' => $r[0][7],
			'annee_evenement_fin' => $r[0][8],
			'heure_evenement_fin' => $r[0][9],
			'minute_evenement_fin' => $r[0][10],
			'evenement_horaire' => $r['horaire'],
			'evenement_titre' => $r['titre'],
			'evenement_lieu' => $r['lieu'],
			'evenement_descriptif' => $r['descrip'],
			'evenement_groupe_mot_select' => $mots_compiles['echo'],
			'evenement_repetitions' => $r['rep'],
			'selected_date_repetitions' => $selected_rep
		), $mots_compiles['post']);
	} // foreach($t as $e=>$v)
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
		foreach($t as $id_eve=>$eve) if (count($eve)) {
			$vals = array();
			$titre = typo($eve['evenement_titre']);
			$lieu = typo($eve['evenement_lieu']);
			$descriptif = typo($eve['evenement_descriptif']);
			$horaire = $eve['evenement_horaire'];
			$date_debut = mktime($eve['heure_evenement_debut'], $eve['minute_evenement_debut'], 0, 
				$eve['mois_evenement_debut'], $eve['jour_evenement_debut'], $eve['annee_evenement_debut']);
			$date_fin = mktime($eve['heure_evenement_fin'], $eve['minute_evenement_fin'], 0,
				$eve['mois_evenement_fin'], $eve['jour_evenement_fin'], $eve['annee_evenement_fin']);

			$s = Agenda_afficher_date_evenement($date_debut, $date_fin, $horaire);
			if ($c = count($eve['evenement_repetitions'])){
				$s_rep = "";
				foreach($eve['evenement_repetitions'] as $rep){
					$rep_date_debut = $rep;
					$rep_date_fin = $rep_date_debut + $date_fin - $date_debut;
					$s_rep .= Agenda_afficher_date_evenement($rep_date_debut, $rep_date_fin, $horaire)."<br/>";
				}
				$s .= "<br/><div style=\"\">".bouton_block_invisible("repetitions_evenement_rapide_{$id_eve}");
				if ($c>1) $s .= _T('agenda:nb_repetitions', array('nb' => $c));
					else $s .= _T('agenda:une_repetition');
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
				if ($c>1) $s .= _T('agenda:nb_mots_clefs', array('nb' => $c));
					else $s .= _T('agenda:un_mot_clef');
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
	global $spip_lang_right, $id_article;
	$out = "";
	//$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_AGENDA."/img_pack/agenda-24.png",true, "", _T('saisierapide:saisie_rapide_votre_liste'));
	$out .= _T('saisierapide:votre_liste_infos');
	$out .= "<input type='hidden' name='saisie_rapide' value='1' />";
	$out .= "<textarea name='evenements_saisie_rapide' rows='10' class='forml' >";
	$out .= _request('evenements_saisie_rapide');
	$out .= "</textarea>";
	$out .= ajax_action_auteur('editer_evenement',"$id_article-creer-0", $script, "id_article=$id_article&saisie_rapide=1", array(_T("saisierapide:reset"),''));
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