<?php
/* *********************************************************************
   *
   * Copyright (c) 2008
   * Xavier Burot
   * fichier : base/genea_listes.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return; // Securite

global
	$liste_filiation,
	$liste_civilite,
	$liste_type_evt;

// -- Liste des differents types de filiation --------------------------
$liste_filiation = array(
	"einc",
	"eado",
	"eadu",
	"eleg",
	"eile",
	"eleg",
	"emon",
	"enat",
	"erec",
	"etro");

// -- Liste des differents types de civilite utilisable ----------------
$liste_civilite = array(
	"m",
	"mlle",
	"mme",
	"dr",
	"sir");

// -- Liste des type d'evenements --------------------------------------
$liste_type_evt = array(
	"birt",
	"deat",
	"titl",
	"nati",
	"annul",
	"resi",
	"div",
	"marb",
	"marl",
	"mars",
	"enga",
	"even",
	"buri",
	"crem",
	"reti",
	"prob",
	"will",
	"grad",
	"cens",
	"natu",
	"immi",
	"emig",
	"chra",
	"ordn",
	"bles",
	"basm",
	"barm",
	"conf",
	"fcom",
	"bapm",
	"chr",
	"adop",
	"cast",
	"dscr",
	"educ",
	"occu",
	"reli",
	"prop",
	"idno",
	"ssn",
	"fact",
	"divf");

?>