<?php
// Désactiver le cache SPIP
// Copyright (c) 2001-2009 Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James
// Copyright (C) 2009  Cliss XXI
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

// Auteur: Sylvain Beucler <beuc@beuc.net>

// Chargé à chaque appel de page
if (!defined('_ECRIRE_INC_VERSION')) return; // sécurité
include_spip('public/composer');

/**
 * Désactivation du cache de pages
 * 
 * Surcharge de public_cacher_dist(...) dans
 * 'ecrire/public/cacher.php' - plus précisément copier/coller de la
 * partie "// Cas ignorant le cache car completement dynamique"
 * 
 * Merci à 'Committo, Ergo Sum' pour l'idée
 * http://forum.spip.org/fr_3874.html
 */
function public_cacher($contexte, &$use_cache, &$chemin_cache, &$page, &$lastmodified) {
  $use_cache = -1;
  $lastmodified = 0;
  $chemin_cache = "";
  $page = array();
  return;
}

/**
 * Désactivation du cache de squelettes. Normalement les squelettes
 * sont rafraîchis 1) si on recalcule (géré par le cache de page) et
 * 2) si le squelette .html est plus récent que le cache - donc c'est
 * automatique a priori (cf. la fonction squelette_obsolete($phpfile,
 * $source)). Cependant, les balises incluses dans les squelettes ne
 * sont pas recalculées, même si on a changé leur définitions, d'où
 * l'utilité de cette fonction.
 * 
 * Surcharge de public_composer_dist(...) dans
 * 'ecrire/public/composer.php'
 */
function public_composer($squelette, $mime_type, $gram, $source, $connect='')
{
  // Si le fichier a déjà été compilé dans cette requête, on le garde
  $nom = calculer_nom_fonction_squel($squelette, $mime_type, $connect);
  if (function_exists($nom))
    return array($nom);

  // sinon on le supprime et on repasse la main à la fonction d'origine
  $phpfile = sous_repertoire(_DIR_SKELS,'',false,true) . $nom . '.php';
  spip_unlink($phpfile);
  return public_composer_dist($squelette, $mime_type, $gram, $source, $connect);
}
