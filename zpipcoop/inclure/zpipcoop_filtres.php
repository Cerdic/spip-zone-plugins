<?php
// =======================================================================================================================================
// Filtre : afaire_liste_par_jalon
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : Retourne les blocs d'affichage des afaire par jalon dans la page afaire
// =======================================================================================================================================
//
function afaire_liste_par_jalon($jalons) {
	$page = NULL;
	if (($jalons) && defined('_TICKETS_LISTE_JALONS')) {
		$liste = explode(":", $jalons);
		$i =0;
		foreach($liste as $_jalon) {
			$i += 1;
			$page .= recuperer_fond('inclure/afaire/inc_afaire_jalon', 
				array('jalon' => $_jalon, 'ancre' => 'ancre_jalon_'.strval($i)));
		}
	}
	return $page;
}
// FIN du Filtre : afaire_liste_par_jalon

// =======================================================================================================================================
// Filtre : afaire_tdm_par_jalon
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : Retourne les blocs d'affichage des afaire par jalon dans la page afaire
// =======================================================================================================================================
//
function afaire_tdm_par_jalon($jalons) {
	$page = NULL;
	if (($jalons) && defined('_TICKETS_LISTE_JALONS')) {
		$liste = explode(":", $jalons);
		$i =0;
		foreach($liste as $_jalon) {
			$i += 1;
			$nb = afaire_compteur_jalon($_jalon);
			$nb_str = ($nb == 0) ? _T('zpipcoop:0_ticket') : (($nb == 1) ? strval($nb).' '._T('zpipcoop:1_ticket') : strval($nb).' '._T('zpipcoop:n_tickets'));
			$page .= '<li><a href="#ancre_jalon_'.strval($i).'" title="'._T('zpipcoop:afaire_aller_jalon').'">'
				._T('zpipcoop:afaire_colonne_jalon').'&nbsp;&#171;&nbsp;'.$_jalon.'&nbsp;&#187;, '.$nb_str
				.'</a></li>';
		}
	}
	$nb = afaire_compteur_jalon();
	if ($nb > 0) {
		$nb_str = ($nb == 1) ? strval($nb).' '._T('zpipcoop:1_ticket') : strval($nb).' '._T('zpipcoop:n_tickets');
		$page .= '<li><a href="#ancre_jalon_non_planifie" title="'._T('zpipcoop:afaire_aller_jalon').'">&#171;&nbsp;'
			._T('zpipcoop:afaire_non_planifies').'&nbsp;&#187;, '.$nb_str
			.'</a></li>';
	}
	return $page;
}
// FIN du Filtre : afaire_tdm_par_jalon

// =======================================================================================================================================
// Filtre : afaire_compteur_jalon
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : Retourne le nombre de afaire pour le jalon ou pour le jalon et le statut choisis
// =======================================================================================================================================
//
function afaire_compteur_jalon($jalon='', $statut='') {
	$valeur = 0;
	// Nombre total de afaire pour le jalon
	$select = array('t1.id_ticket');
	$from = array('spip_afaire AS t1');
	$where = array('t1.jalon='.sql_quote($jalon));
	if ($statut)
		$where = array_merge($where, array('t1.statut='.sql_quote($statut)));
	$result = sql_select($select, $from, $where);
	$valeur = sql_count($result);
	return $valeur;
}
// FIN du Filtre : afaire_compteur_jalon

// =======================================================================================================================================
// Filtre : afaire_avancement_jalon
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : Retourne le pourcetage de afaire termines sur le nombre de afaire total du jalon
// =======================================================================================================================================
//
function afaire_avancement_jalon($jalon='') {
	$valeur = 0;
	// Nombre total de afaire pour le jalon
	$select = array('t1.id_ticket');
	$from = array('spip_afaire AS t1');
	$where = array('t1.jalon='.sql_quote($jalon));
	$result = sql_select($select, $from, $where);
	$n1 = sql_count($result);
	// Nombre de afaire termines pour le jalon
	if ($n1 != 0) {
		$where = array_merge($where, array(sql_in('t1.statut', array('resolu','ferme'))));
		$result = sql_select($select, $from, $where);
		$n2 = sql_count($result);
		$valeur = floor($n2*100/$n1);
	}
	return $valeur;
}
// FIN du Filtre : afaire_avancement_jalon

// =======================================================================================================================================
// Filtre : afaire_ticket_existe
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : Retourne l'info qu'au moins un ticket a ete cree
// =======================================================================================================================================
//
function afaire_ticket_existe($bidon) {
	$existe = false;
	// Test si la table existe
	$table = sql_showtable('spip_afaire', true);
	if ($table) {
		// Nombre total de afaire
		$from = array('spip_afaire AS t1');
		$where = array();
		$result = sql_countsel($from, $where);
		// Nombre de afaire termines pour le jalon
		if ($result >= 1)
			$existe = true;
	}
	return $existe;
}
// FIN du Filtre : afaire_ticket_existe
?>
