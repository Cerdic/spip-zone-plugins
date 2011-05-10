<?php

/*	Fichier PHP du formulaire CVT : editer_publication. Etroitement 	*/
/*	lie a la structure HTML du meme nom. Pour plus d'infos sur 	*/
/*	les formulaires CVT : http://www.spip.net/fr_article3800.html .	*/
/*	Ces deux sont conjointement appeles par la balise 		*/
/*	#FORMULAIRE_EDITER_PUBLICATION dans le fond				*/ 
/*	'prive/editer/publication.html'.					*/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

// Choix par defaut des options de presentation
function publications_edit_config($row)
{
	global $spip_ecran, $spip_lang, $spip_display;

	// Tout ceci est optionnel, les valeurs affichees ici
	// pour chaque variable sont identiques a celles que
	// l'on trouve de base dans la dist pour les breves.
	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['afficher_barre'] = $spip_display != 4;
	$config['langue'] = $spip_lang;
	$config['restreint'] = ($row['statut'] == 'publie');

	return $config;

}

//
// Fonction CVT
//


// C comme Charger : indique à SPIP quels sont 
// les champs que l’internaute peut remplir.
function formulaires_editer_publication_charger_dist($id_publication='new', $id_rubrique='', $retour='', $lier_trad=0, $config_fonc='publications_edit_config', $row=array(), $hidden=''){

	// Le principe : on liste dans la variable $valeurs 
	// les champs de saisie du formulaire et, pour chaque 
	// champ, la valeur initiale par defaut. 	
	// Ici, simplification maximum, on laisse la fonction 
	// generique Charger du core effectuer le chargement.
	$valeurs = formulaires_editer_objet_charger('publication',$id_publication,$id_parent, $lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
	
}


// V comme Verifier : on verifie que les valeurs
// saisies par l’internaute son correctes.
function formulaires_editer_publication_verifier_dist($id_publication='new', $id_rubrique='', $retour='', $lier_trad=0, $config_fonc='publications_edit_config', $row=array(), $hidden=''){

	// Le principe : la fonction teste les données saisies
	// dans chaque champ selon ses contraintes propres et 
	// renvoie une erreur si le teste echoue.
	// Ici,  c'est encore une fonction generique du core 
	// ('formulaires_editer_objet_verifier') qui s'en occupe
	// (voir ecrire/inc/editer.php).
	$erreurs = formulaires_editer_objet_verifier('publication',$id_publication,array('titre'));

	// On peut par exemple y inserer un test de validite du
	// champ 'peremption' (date qui doit être superieure a 
	// la date de creation). A voir plus tard...

	return $erreurs;
}


// T comme Traiter : realiser toutes les operations
// de traitement du formulaire
function formulaires_editer_publication_traiter_dist($id_publication='new', $id_rubrique='', $retour, $lier_trad=0, $config_fonc='publications_edit_config', $row=array(), $hidden=''){

	// Le principe : si aucune erreur trouvee, alors on 
	// execute les actions suivantes. En pratique on ne 
	// gere ici que le message d'erreur ou de succes, pour
	// l'edition a proprement parle on renvoie vers la fonction
	// 'editer_$type' (/action/editer_*.php).
	// Ici, c'est toujours la fonction generique du core
	// qui s'en occupe pour nous. 
	$resultat = formulaires_editer_objet_traiter('publication',$id_publication,$id_parent,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $resultat;
}

?>
