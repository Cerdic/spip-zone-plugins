<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/puce_statut');

// Appelee par : prive/infos/evenement_fonctions
function inc_instituer_evenement($id_evenement, $statut=-1){
	if ($statut == -1) return "";

	// Statuts possibles 
	$etats = array(
	 	// statut => array(titre,image)
        'prepa' => array(_T('simplecal:info_statut_encours'),''),	
		'prop' => array(_T('simplecal:info_statut_proposee'),''),	
		'publie' => array(_T('simplecal:info_statut_validee'),''),	
		'refuse' => array(_T('simplecal:info_statut_refusee'),''),
		'poubelle' => array(_T('simplecal:info_statut_poubelle'),'')
	);
    
    // Autorisation => retrait de certains statuts
    if (!autoriser('publier', 'evenement', $id_evenement)) {
		unset($etats['publie']);
		unset($etats['refuse']);
	}
    

	// Si le statut ne figure pas dans le tableau ci dessus,
	// alors on l'y ajoute (point d'entree pour ajouter d'autres statuts : 
    // plugin Corbeille par exemple).
	if (!in_array($statut, array_keys($etats)))
		$etats[$statut] =  array($statut,'');

	// debut de code html du formulaire (partie fixe)	
	$res =
	  "<ul id='instituer_evenement-$id_evenement' class='instituer_breve instituer'>" 
	  . "<li>" . _T('simplecal:entree_evenement_publie') 
	  ."<ul>";
	
    /*
	$href = redirige_action_auteur('editer_evenement', $id_evenement, 'evenement_voir', "id_evenement=$id_evenement");
	foreach($etats as $s=>$lib_statut){
		$href = parametre_url($href, 'statut', $s);
		
		if ($s==$statut) {
			$res .= "<li class='$s selected'>" . puce_statut($s) . $lib_statut[0] . '</li>';
        } else {
			$res .= "<li class='$s'><a href='$href' onclick='return confirm(confirm_changer_statut);'>" . puce_statut($s) . $lib_statut[0] . '</a></li>';
        }
	}
    */
    
    foreach($etats as $s=>$affiche){
		$href = generer_action_auteur('instituer_evenement',"$id_evenement-$s",self());
		if ($s==$statut) {
			$res .= "<li class='$s selected'>" . puce_statut($s) . $affiche[0] . '</li>';
        } else {
			$res .= "<li class='$s'><a href='$href' onclick='return confirm(confirm_changer_statut);'>" . puce_statut($s) . $affiche[0] . '</a></li>';
        }
	}

	$res .= "</ul></li></ul>";
    
	return $res;
}


?>
