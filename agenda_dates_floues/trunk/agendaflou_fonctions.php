<?php

/**
 * Afficher de facon textuelle les dates de debut et fin en fonction des cas, en prenant en compte si elles sont floues
 *
 * Cette fonction fait comme affdate_debut_fin() mais gère aussi la floutitude des dates.
 * - Lundi 20 fevrier a 18h
 * - Le 20 fevrier de 18h a 20h
 * - Du 20 au 23 fevrier
 * - Du 20 fevrier au 30 mars
 * - Du 20 fevrier 2007 au 30 mars 2008
 * $horaire='oui' ou true permet d'afficher l'horaire, toute autre valeur n'indique que le jour
 * $forme peut contenir une ou plusieurs valeurs parmi
 *  - abbr (afficher le nom des jours en abbrege)
 *  - hcal (generer une date au format hcal)
 *  - jour (forcer l'affichage des jours)
 *  - annee (forcer l'affichage de l'annee)
 *
 * @see affdate_debut_fin
 * @param string $date_debut
 * @param string $date_fin
 * @param string $horaire
 * @param string $forme
 * @param string $date_debut_floue
 * @param string $date_fin_floue
 * @return string
 */
function affdate_debut_fin_floue($date_debut, $date_fin, $horaire = 'oui', $forme='', $date_debut_floue='', $date_fin_floue=''){
	$abbr = $jour = '';
	$affdate = "affdate";
	if (strpos($forme,'abbr') !==false) $abbr = 'abbr';
	if (strpos($forme,'annee')!==false) $affdate = 'affdate';
	if (strpos($forme,'jour') !==false) $jour = 'jour';
	
	$dtstart = $dtend = $dtabbr = "";
	if (strpos($forme,'hcal')!==false) {
		$dtstart = "<abbr class='dtstart' title='".date_iso($date_debut)."'>";
		$dtend = "<abbr class='dtend' title='".date_iso($date_fin)."'>";
		$dtabbr = "</abbr>";
	}

	$date_debut = strtotime($date_debut);
	$date_fin = strtotime($date_fin);
	$d = date("Y-m-d", $date_debut);
	$f = date("Y-m-d", $date_fin);
	$h = ($horaire==='oui' OR $horaire===true);
	$hd = _T('date_fmt_heures_minutes_court', array('h'=> date("H",$date_debut), 'm'=> date("i",$date_debut)));
	$hf = _T('date_fmt_heures_minutes_court', array('h'=> date("H",$date_fin), 'm'=> date("i",$date_fin)));
	
	// S'il y a au moins une des deux dates floues
	if ($date_debut_floue or $date_fin_floue) {
		if ($date_debut_floue == 'mois'){
			$date_debut_floue = 'nom_mois';
		}
		if ($date_fin_floue == 'mois'){
			$date_fin_floue = 'nom_mois';
		}
		
		if ($date_debut_floue){
			$date_debut = $date_debut_floue($d).($date_debut_floue != 'annee' ? ' '.annee($d) : '');
		}
		else{
			$date_debut = $affdate($d);
		}
		
		if ($date_fin_floue){
			$date_fin = $date_fin_floue($f).($date_fin_floue != 'annee' ? ' '.annee($f) : '');
		}
		else{
			$date_fin = $affdate($f);
		}
		
		if ($date_debut == $date_fin){
			$s = spip_ucfirst($date_debut);
		}
		else{
			$s = spip_ucfirst($date_debut).' - '.spip_ucfirst($date_fin);
		}
	}
	// meme jour
	else if ($d==$f) {
		$nomjour = nom_jour($d,$abbr);
		$s = $affdate($d);
		$s = _T('date_fmt_jour',array('nomjour'=>$nomjour,'jour' => $s));
		if ($h){
			if ($hd==$hf){
				// Lundi 20 fevrier a 18h25
				$s = spip_ucfirst(_T('date_fmt_jour_heure',array('jour'=>$s,'heure'=>$hd)));
				$s = "$dtstart$s$dtabbr";
			}else{
				// Le <abbr...>lundi 20 fevrier de 18h00</abbr> a <abbr...>20h00</abbr>
				if($dtabbr && $dtstart && $dtend)
					$s = spip_ucfirst(_T('date_fmt_jour_heure_debut_fin_abbr',array('jour'=>$s,'heure_debut'=>$hd,'heure_fin'=>$hf,'dtstart'=>$dtstart,'dtend'=>$dtend,'dtabbr'=>$dtabbr)));
				// Le lundi 20 fevrier de 18h00 a 20h00
				else
					$s = spip_ucfirst(_T('date_fmt_jour_heure_debut_fin',array('jour'=>$s,'heure_debut'=>$hd,'heure_fin'=>$hf)));
			}
		}else{
			if($dtabbr && $dtstart)
				$s = $dtstart.spip_ucfirst($s).$dabbr;
			else
				$s = spip_ucfirst($s);
		}
	}
	// meme annee et mois, jours differents
	else if ((date("Y-m",$date_debut))==date("Y-m",$date_fin)) {
		if(!$h)
			$date_debut = jour($d);
		else
			$date_debut = $affdate($d);
		$date_fin = $affdate($f);
		if($jour){
			$nomjour_debut = nom_jour($d,$abbr);
			$date_debut = _T('date_fmt_jour',array('nomjour'=>$nomjour_debut,'jour' => $date_debut));
			$nomjour_fin = nom_jour($f,$abbr);
			$date_fin = _T('date_fmt_jour',array('nomjour'=>$nomjour_fin,'jour' => $date_fin));
		}
		if ($h){
			$date_debut = _T('date_fmt_jour_heure',array('jour'=>$date_debut,'heure'=>$hd));
			$date_fin = _T('date_fmt_jour_heure',array('jour'=>$date_fin,'heure'=>$hf));
		}
		$date_debut = $dtstart.$date_debut.$dtabbr;
		$date_fin = $dtend.$date_fin.$dtabbr;
		
		$s = _T('date_fmt_periode',array('date_debut' => $date_debut,'date_fin'=>$date_fin));
	}
	else {
		$date_debut = affdate($d);
		$date_fin = affdate($f);
		if($jour){
			$nomjour_debut = nom_jour($d,$abbr);
			$date_debut = _T('date_fmt_jour_periode',array('nomjour'=>$nomjour_debut,'jour' => $date_debut));
			$nomjour_fin = nom_jour($f,$abbr);
			$date_fin = _T('date_fmt_jour_periode',array('nomjour'=>$nomjour_fin,'jour' => $date_fin));
		}
		if ($h){
			$date_debut = _T('date_fmt_jour_heure',array('jour'=>$date_debut,'heure'=>$hd)); 
			$date_fin = _T('date_fmt_jour_heure',array('jour'=>$date_fin,'heure'=>$hf));
		}
		
		$date_debut = $dtstart.$date_debut.$dtabbr;
		$date_fin=$dtend.$date_fin.$dtabbr;
		$s = _T('date_fmt_periode',array('date_debut' => $date_debut,'date_fin'=>$date_fin));
		
	}
	return $s;
}
