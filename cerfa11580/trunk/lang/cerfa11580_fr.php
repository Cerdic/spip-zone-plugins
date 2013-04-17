<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin & Marcel Bolla & gilcot
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

//[fr] Ceci est un fichier langue de SPIP pour le français
//[en] This is a SPIP language file for french (source)

//@note : Fichier de langue séparé car concernant une spécificité française qui
//@note : n'a pas forcement besoin d'être traduit et n'a pas d'intérêt hors de
//@note : France (relatif à l'Article 200-5 du Code Général des Impôts)
//@note : Préfixe "cerfa11580" car s'appuie sur le formulaire Cerfa n° 11580
//@note : *1 "Reçu dons aux œuvres"
//@note : *3 "Reçu au titre des dons à certains organismes d'intérêt général"
//@note : c'est le dernier en vigueur depuis l'arrêté du 26 juin 2008
//@cf : http://vosdroits.service-public.fr/professionnels-entreprises/R17454.xhtml
//@cf : http://www.impots.gouv.fr/portal/deploiement/p1/fichedescriptiveformulaire_5952/fichedescriptiveformulaire_5952.pdf
//@cf : http://droit-finances.commentcamarche.net/faq/14172-cerfa-11580-remplir-un-formulaire-de-recu-de-dons


$GLOBALS[$GLOBALS['idx_lang']] = array(

	'config_aide_infofiscal' => 'Dates au format jj/mm/aaaa séparées par un espace.',
	'config_aide_type' => '
	sigles :
		s-b-l = à but non lucratif ;
		d-i-g = d\'intérêt général ;
		d-u-p = d\'utilité publique ;
	<br />abbréviations :
		asso. = association(s) ;
		fond. = fondation(s) ;
		ent. = entreprise(s) ;
		ets. = établissement(s) ;
		org. = organisme(s) ;
		pers. = personne(s) ;
		pub. = public(s)/publique(s) ;
		sté. = société(s) ;
		sup. = supérieur(e)(s) ;
	...',

	'config_libelle_cgi200' => 'Art. 200 du CGI',
	'config_libelle_cgi238' => 'Art. 238 bis du CGI',
	'config_libelle_cgi885' => 'Art. 885-0 V bis A du CGI',

	'config_libelle_type0' => 'Désactiver (gestion manuelle ou non-usage)',
	'config_libelle_type1' => 'Asso./fond. reconnue d-u-p (dater)',
	'config_libelle_type2' => 'Asso. du 57/67/68 dont la mission est reconnue d-u-p (dater)',
	'config_libelle_type3' => 'Fondation universitaire/partenariale',
	'config_libelle_type4' => 'Fondation d\'entreprise',
	'config_libelle_type5' => 'Œuvre/org. d-i-g',
	'config_libelle_type6' => 'Musée de France',
	'config_libelle_type7' => 'Ets. enseignement sup./artistique pub./privé, d-i-g, s-b-l',
	'config_libelle_type8' => 'Org. dont objet exclusif est participation financière à création ent.',
	'config_libelle_type9' => 'Asso. culturelle ou de bienfaisance + Ets. pub. cultes reconnus du 57/67/68',
	'config_libelle_type10' => 'Org. dont activité principale organisation de festivals',
	'config_libelle_type11' => 'Asso. fournissant gratuitement aide/soins à pers. en difficulté...',
	'config_libelle_type12' => 'Fond. du patrimoine ou fond./asso. lui affectant ses dons',
	'config_libelle_type13' => 'Ets. de recherche public/privé, d-i-g, s-b-l',
	'config_libelle_type14' => 'Ent. insertion ou ent. de travail temporaire insertion',
	'config_libelle_type15' => 'Asso. intermédiaires',
	'config_libelle_type16' => 'Ateliers/chantiers d\'insertion',
	'config_libelle_type17' => 'Ent. adaptées',
	'config_libelle_type18' => 'Agence Nationale de la Recherche',
	'config_libelle_type19' => 'Sté./org. agréé de recherche scientifique/technique',
	'config_libelle_type20' => 'Autre organisme (à préciser)',
	'config_libelle_typefiscal' => 'Type',
	'config_libelle_tauxfiscal' => '% cotisation statutaire',

	'intitule_cgi' => "Les dons et versements que nous recevont ouvrent droit à la réduction d'impôt
prévue aux articles 200, 238 bis et 885-0 V bis A du code général des impôts (CGI)",
	'intitule_tco' => "Nous somme reconnue comme :", // Type ou Catégore d'Organisme

	'intitule_type1' => "Association ou fondation reconnue d'utilité publique par décret en date du @date@ (publication au Journal officiel)",
	'intitule_type2' => "Association située dans le département de la Moselle, du Bas-Rhin ou du Haut-Rhin dont la mission a été reconnue d'utilité publique par arrêté préfectoral en date du @date@",
	'intitule_type3' => "Fondation universitaire ou fondation partenariale mentionnées respectivement aux articles L. 719-12 et L. 719-13 du code de l'éducation",
	'intitule_type4' => "Fondation d'entreprise",
	'intitule_type5' => "Œuvre ou organisme d'intérêt général",
	'intitule_type6' => "Musée de France",
	'intitule_type7' => "Établissement d'enseignement supérieur ou d’enseignement artistique public ou privé, d’intérêt général, à but non lucratif",
	'intitule_type8' => "Organisme ayant pour objet exclusif de participer financièrement à la création d'entreprises",
	'intitule_type9' => "Association cultuelle ou de bienfaisance et établissement public des cultes reconnus d'Alsace-Moselle",
	'intitule_type10' => "Organisme ayant pour activité principale l'organisation de festivals",
	'intitule_type11' => "Association fournissant gratuitement une aide alimentaire ou des soins médicaux à des personnes en difficulté ou favorisant leur logement",
	'intitule_type12' => "Fondation du patrimoine ou fondation ou association qui affecte irrévocablement les dons à la Fondation du patrimoine, en vue de subventionner les travaux prévus par les conventions conclues entre la Fondation du patrimoine et les propriétaires des immeubles (article L. 143-2-1 du code du patrimoine)",
	'intitule_type13' => "Établissement de recherche public ou privé, d’intérêt général, à but non lucratif",
	'intitule_type14' => "Entreprise d’insertion ou entreprise de travail temporaire d’insertion (articles L. 5132-5 et L. 5132-6 du code du travail).",
	'intitule_type15' => "Associations intermédiaires (article L. 5132-7 du code du travail)",
	'intitule_type16' => "Ateliers et chantiers d’insertion (article L. 5132-15 du code du travail)",
	'intitule_type17' => "Entreprises adaptées (article L. 5213-13 du code du travail)",
	'intitule_type18' => "Agence nationale de la recherche (ANR)",
	'intitule_type19' => "Société ou organisme agréé de recherche scientifique ou technique (dons effectués par les entreprises)",
	'intitule_type20' => '@date@',

	'liens_vers_justificatifs' => 'Liens vers les reçus',

);

?>