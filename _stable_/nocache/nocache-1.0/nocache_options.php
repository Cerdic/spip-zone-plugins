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


/**
 * Désactivation du cache de pages
 */
if ($spip_version_code >= 1.92 && $spip_version_code < 2)
  {

    /** 
     * Surcharge de public_cacher_dist(...) dans
     * 'ecrire/public/cacher.php' - plus précisément copier/coller de
     * la partie "// Cas ignorant le cache car completement dynamique"
     * 
     * Merci à 'Committo, Ergo Sum' pour l'idée
     * http://forum.spip.org/fr_3874.html
     */
    function public_cacher($contexte, &$use_cache, &$chemin_cache, &$page, &$lastmodified)
    {
      $use_cache = -1;
      $lastmodified = 0;
      $chemin_cache = "";
      $page = array();
      return;
    }
    /* Il existe une autre solution implémentée dans le plugin
       'desactiver_cache_1_9', qui consiste à forcer
       $_SERVER['REQUEST_METHOD']='POST', sauf pour quelques fichiers
       - mais ce n'est pas très propre AMHA. */
  }
else if (version_compare($spip_version_branche, "2.0.0"))
  {
    // Inspiré du plugin 'couteau_suisse':
    define('_NO_CACHE', -1); // toujours recalculer sans stocker
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
include_spip('public/composer');
if ($spip_version_code >= 1.92 && $spip_version_code < 2)
  {
    function public_composer($squelette, $mime_type, $gram, $sourcefile)
    {
      // Si le fichier a déjà été compilé dans cette requête, on le garde
      $nom = $mime_type . '_' . md5($squelette);
      if (function_exists($nom))
	return $nom;

      // sinon on le supprime et on repasse la main à la fonction d'origine
      $phpfile = sous_repertoire(_DIR_SKELS) . $nom . '.php';
      supprimer_fichier($phpfile);
      return public_composer_dist($squelette, $mime_type, $gram, $sourcefile);
    }
  }
else if (version_compare($spip_version_branche, "2.0.0"))
  {
    function public_composer($squelette, $mime_type, $gram, $source, $connect='')
    {
      // Si le fichier a déjà été compilé dans cette requête, on le garde
      $nom = calculer_nom_fonction_squel($squelette, $mime_type, $connect);
      if (function_exists($nom))
	return array($nom);
      
      // sinon on le supprime et on repasse la main à la fonction d'origine
      $phpfile = sous_repertoire(_DIR_SKELS,'',false,true) . $nom . '.php';
      supprimer_fichier($phpfile);
      return public_composer_dist($squelette, $mime_type, $gram, $source, $connect);
    }
  }


/**
 * Recharge les fichiers plugin.xml.
 * 
 * Utile quand on est en train de développer un plugin (sinon il faut
 * aller dans la page 'exec=admin_plugin' qui appelle aussi
 * verif_plugin()). Ne recharge que les plugins actifs.
 */
/* Le fichier est déjà chargé par PHP, donc on peut le supprimer sans
 risque. L'absence du fichier force la revérification des plugins à
 chaque page affichée. */
supprimer_fichier(_DIR_TMP."charger_plugins_options.php");
/* Autre solution: demander le rechargement. Mais comme ce
 rechargement n'est effectué qu'après que les plugins sont chargés
 (puisque nous sommes dans un plugin..), les changements ne seront
 effectifs qu'au prochain chargement. La solution avec 'spip_unlink'
 fonctionne donc mieux car le rechargement est différé. */
//include_spip('inc/plugin');
//verif_plugin();
