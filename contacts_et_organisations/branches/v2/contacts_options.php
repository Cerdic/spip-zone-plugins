<?php

/**
 * Chargement du plugin Contacts & Organisations
 *
 * Si la configuration du plugin le demande, le répertoire zpip1 est ajouté
 * aux chemins des répertoires SPIP.
 *
 * Pour chaque balise, il est possible de surcharger, dans son fichier
 * mes_fonctions.php, la fonction balise_TOTO_dist par une fonction
 * `balise_TOTO()` respectant la même API : elle recoit en entrée un objet
 * de classe CHAMP, le modifie et le retourne. Cette classe est definie
 * dans public/interfaces.
 * 
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2013
 * @author Cyril Marion, Matthieu Marcillaud, Rastapopoulos
 *
 * @package SPIP\Contacts\Options
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/config');
if (lire_config('contacts_et_organisations/activer_squelettes_publics_zpip_v1')) {
	_chemin(_DIR_PLUGIN_CONTACTS . 'zpip1');
}
