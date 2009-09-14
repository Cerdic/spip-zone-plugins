<?php

/*	Fonction du core, adaptee ici, qui permet d'afficher	*/
/*	un formulaire de changement de statut pour un objet.	*/
/*	Appelee par :						*/
/*		- prive/infos/annonce_fonctions			*/
/*		- prive/infos/evenement_fonctions		*/
/*		- prive/infos/publication_fonctions		*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_instituer_evenement($id_objet, $statut=-1)
{
	$type_objet = 'evenement';

	// Si statut a sa valeur par defaut, alors on s'arrete.
	if ($statut == -1) return "";

	// On associe les statuts possibles a leurs etiquettes
	$liste_statuts = array(
	 	// statut => array(titre,image)
		'prop' => array(_T('vu:item_'.$type_objet.'_propose'),''),	
		'publie' => array(_T('vu:item_'.$type_objet.'_valide'),''),	
		'refuse' => array(_T('vu:item_'.$type_objet.'_refuse'),'')	
	);

	// Si le statut ne figure pas dans le tableau ci dessus,
	// alors on l'y ajoute (point d'entree pour ajouter d'
	// autres statuts : plugin Corbeille par exemple).
	if (!in_array($statut, array_keys($liste_statuts)))
		$liste_statuts[$statut] =  array($statut,'');

	// On stocke dans la variable, le debut de code html du 
	// formulaire (partie fixe)	
	$res =
	  "<ul id='instituer_$type_objet-$id_objet' class='instituer_breve instituer'>" 
	  . "<li>" . _T('vu:entree_'.$type_objet.'_publie') 
	  ."<ul>";
	
	// On y ajoute la suite : partie variable selon le statut en cours.
	// Le lien de redirection en cas de changement effectif est
	// stocke en attendant la suite.
	$href = redirige_action_auteur('editer_'.$type_objet, $id_objet, 'veille_voir', "id_$type_objet=$id_objet");
	// Pour chaque etat possible du statut,
	foreach($liste_statuts as $s=>$affiche){
		// 1- On complete le lien de redirection avec les parametres specifiques
		$href = parametre_url($href,'statut',$s);
		// 2- On teste si le statut est actuel ou possible
		if ($s==$statut)
			// Si le statut est celui en cours sur l'objet
			// alors on l'affiche en tant que tel
			$res .= "<li class='$s selected'>" . puce_statut($s) . $affiche[0] . '</li>';
		else
			// Sinon, c'est un nouvel etat possible,
			// alors on l'affiche avec un lien qui change le statut
			$res .= "<li class='$s'><a href='$href' onclick='return confirm(confirm_changer_statut);'>" . puce_statut($s) . $affiche[0] . '</a></li>';
	}

	// On y ajoute maintenant la fin du code hmtl (partie fixe)
	$res .= "</ul></li></ul>";

	// Puis on renvoie le tout !
	return $res;
}


?>
