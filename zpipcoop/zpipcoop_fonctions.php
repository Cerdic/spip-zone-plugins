<?php
// =======================================================================================================================================
// Filtres : 
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : regroupe les filtres definis par le squelette
// =======================================================================================================================================
//
include_spip('inclure/zpipcoop_filtres');

// =======================================================================================================================================
//  Repris du plugins tickets
// =======================================================================================================================================
// Auteur: Smellup
// Fonction : tickets_texte
// =======================================================================================================================================
//

foreach (array('severite', 'type', 'statut') as $nom){
	eval("function tickets_texte_$nom(\$niveau) {
		\$type = tickets_liste_$nom();
		if (isset(\$type[\$niveau])) {
			return \$type[\$niveau];
		}
	}");
}

function tickets_icone_statut ($niveau) {
	$img = array(
		"redac" => "puce-blanche.gif",
		"ouvert" => "puce-orange.gif",
		"resolu" => "puce-verte.gif",
		"ferme" => "puce-poubelle.gif"
		);
	return $img[$niveau];
}


function tickets_liste_statut($connecte = true){
	$statuts = array(
		"redac" => _T("tickets:statut_redac"),
		"ouvert" => _T("tickets:statut_ouvert"),
		"resolu" => _T("tickets:statut_resolu"),
		"ferme" => _T("tickets:statut_ferme"),
	);
	if (!$connecte) {
		unset($statuts['redac']);
	}
	return $statuts;
}

function tickets_liste_type($id_ticket = null){
	$types = array(
		1 => _T("ticketskiss:type_probleme"),
		2 => _T("ticketskiss:type_amelioration"),
		3 => _T("ticketskiss:type_tache"),
	);
	return $types;
}

function tickets_liste_severite($id_ticket = null){
	$severites = array(
		1 => _T("ticketskiss:severite_bloquant"),
		2 => _T("ticketskiss:severite_important"),
		3 => _T("ticketskiss:severite_normal"),
		4 => _T("ticketskiss:severite_peu_important"),
	);
	return $severites;
}


?>
