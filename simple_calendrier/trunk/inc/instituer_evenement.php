<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction du core, adaptee ici, qui permet d'afficher
// un formulaire de changement de statut pour un objet.
// Appelee par :
// - prive/infos/evenement_fonctions


function inc_instituer_evenement($id_objet, $statut=-1)
{
	// le statut n'est pas fournit => on s'arrete.
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
    if (!autoriser('publier', 'evenement', $id_objet)) {
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
	  "<ul id='instituer_sortie-$id_objet' class='instituer_breve instituer'>" 
	  . "<li>" . _T('simplecal:entree_evenement_publie') 
	  ."<ul>";
	
	// On y ajoute la suite : partie variable selon le statut en cours.
	// Le lien de redirection en cas de changement effectif est stocke en attendant la suite.
	$href = redirige_action_auteur('editer_evenement', $id_objet, 'evenement_voir', "id_evenement=$id_objet");
	// Pour chaque etat possible du statut,
	foreach($etats as $s=>$lib_statut){
		// 1- On complete le lien de redirection avec les parametres specifiques
		$href = parametre_url($href, 'statut', $s);
		// 2- On teste si le statut est actuel ou possible
		if ($s==$statut)
			// Si le statut est celui en cours sur l'objet
			// alors on l'affiche en tant que tel
			$res .= "<li class='$s selected'>" . puce_statut($s) . $lib_statut[0] . '</li>';
		else
			// Sinon, c'est un nouvel etat possible,
			// alors on l'affiche avec un lien qui change le statut
			$res .= "<li class='$s'><a href='$href' onclick='return confirm(confirm_changer_statut);'>" . puce_statut($s) . $lib_statut[0] . '</a></li>';
	}

	// On y ajoute maintenant la fin du code hmtl (partie fixe)
	$res .= "</ul></li></ul>";
    
	// Puis on renvoie le tout !
	return $res;
}


?>
