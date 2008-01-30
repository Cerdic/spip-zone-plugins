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
	$liste_type_evt,
	$liste_tye_union;

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
	"anul",
	"resi",
	"div",
	"marr",
	"marb",
	"marc",
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
	"ordi",
	"ordn",
	"bles",
	"basm",
	"barm",
	"conf",
	"conl",
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

// -- Liste des type d'evenements --------------------------------------
$liste_type_union = array(
	"marr",
	"pacs",
	"ulib",
	"conc");
?>