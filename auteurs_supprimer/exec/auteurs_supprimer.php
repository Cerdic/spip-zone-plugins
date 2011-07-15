<?php

/**
 * Permet de supprimer plusieurs auteurs par un seul formulaire.
 *
 * Si SPIP-Listes actif, supprime également les abonnements
 * aux listes et le format de réception de l'auteur.
 *
 * Ne permet pas de supprimer les auteurs
 * qui ont un article.
 *
 * Squelette à appeler via :
 * 	http://<votredomain>/?page=auteurs_supprimer
 * ou via le bouton dans sous-menu des auteurs
 *
 * @author Christian Paulus
 * @license GPLv3
 * @version 20110714
 */

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	header('Location: '
		   . htmlspecialchars(sinon($GLOBALS['meta']['adresse_site'],'.'))
		   . '/?page=auteurs_supprimer'
		   );
	exit;