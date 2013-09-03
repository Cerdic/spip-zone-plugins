<?php

/*

    This file is part of Trad-Lang.

    Trad-Lang is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Trad-Lang is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Trad-Lang; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    Copyright 2003 
        Florent Jugla <florent.jugla@eledo.com>, 
        Philippe Riviere <fil@rezo.net>

*/

//print_r($_GET);
//exit;

ini_set(memory_limit, "32M");

$home = "../..";
chdir($home);

$tlversion = "v0.4";
//$fond='vide';
$delais=10000000;
$flag_preserver=true;	// pas de bouton SPIP "recalculer cette page"

// on sauvegarde la valeur de 
// spip lang pour eviter les
// interferences avec SPIP
//$spip_lang_sv = $spip_lang;

//include('./inc-public.php3');  

define("DIRTD", "./trad-lang2");  // nom rep. pour les include
define("TRAD_LANG", "trad_lang2");  // nom table dans la base
define("NOMAPP", "trad-lang2");   // nom appli. pour calcul URL
define("URLAPP", "/demo-trad-lang/trad-lang2");  // URL
define ("DIRMOD", "./trad-lang2/modules");

include(DIRTD."/scripts/conf.php");
include(DIRTD."/scripts/debug.php");
include(DIRTD."/scripts/mod.php");
include(DIRTD."/scripts/app.php");
include(DIRTD."/scripts/fonc.php");
include(DIRTD."/scripts/etap.php");

if (!isset($lgi))
  $lgi='fr';

$app = new app;
$app->lgi = $lgi;

include("ecrire/inc_version.php3");
include("ecrire/inc_lang.php3");
include("ecrire/inc_filtres.php3");
include("ecrire/inc_charsets.php3");
include("ecrire/inc_texte.php3");
include_ecrire('inc_presentation.php3');

// initialisation de la var. globale spip_lang utilisee
// par les fonctions _T de SPIP
$spip_lang = $app->lgi;

$spip_actif = (file_exists("ecrire/inc_connect.php3"));
if (!$spip_actif) {
  echo "ERREUR TECHNIQUE. SPIP n'est pas activé.";
  exit;
}

include_ecrire('inc_session.php3');
verifier_visiteur();
if (!$auteur_session)
{
  //echo _TT('ts:texte_avis_enregistrer');
  Header("Location: ../../spip_login.php3?var_url=.%2F".NOMAPP."%2Fscripts%2Fint.php%3Fetape%3Dpage_garde");
  exit;
}

$app->verif_droit($auteur_session);

$app->aut = $auteur_session['nom'];
$app->login = $auteur_session['login'];
$g_deb->log(3, "Log de ".$app->aut);
$g_deb->log(3, "Login= ".$app->login);

//include_ecrire('inc_filtres.php3');
include_ecrire("inc_connect.php3");

// recuperation des ref. sur
// les modules
$mod = strtolower(eregi_replace("[^a-z0-9_]", "", $mod));
$app->init_modules($mod);

$app->set_dir(get_dir($spip_lang)); 
$app->init_langues();

$etp = fabrique_etape($etape, &$app);

if ($etp)
  $etp->run(&$app);
else
  echo "ERREUR TECHNIQUE. Il y a un bug dans le programme";

exit;

?>

