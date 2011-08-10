<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault            *
 *  Copyright (c) 2010 Emmanuel Saint-James                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Le premier element indique un ancien membre
$GLOBALS['association_liste_des_statuts'] =
  array('sorti','prospect','ok','echu','relance');

$GLOBALS['association_styles_des_statuts'] = array(
	"echu" => "impair",
	"ok" => "valide",
	"prospect" => "prospect",
	"relance" => "pair",
	"sorti" => "sortie"
);


define('_DIR_PLUGIN_ASSOCIATION_ICONES', _DIR_PLUGIN_ASSOCIATION.'img_pack/');

function association_icone($texte, $lien, $image, $sup='rien.gif')
{
	return icone_horizontale($texte, $lien, _DIR_PLUGIN_ASSOCIATION_ICONES. $image, $sup, false);
}

function association_bouton($texte, $image, $script, $args='', $img_attributes='')
{
	return '<a href="'
	. generer_url_ecrire($script, $args)
	. '"><img src="'
	. _DIR_PLUGIN_ASSOCIATION_ICONES. $image 
	. '" alt=" " title="'
	. $texte
	. '" '
	. $img_attributes
	.' /></a>';
}

function association_retour()
{
	return bloc_des_raccourcis(association_icone(_T('asso:bouton_retour'),  str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']), "retour-24.png"));
}

function request_statut_interne()
{
	$statut_interne = _request('statut_interne');
	if (in_array($statut_interne, $GLOBALS['association_liste_des_statuts'] ))
		return "statut_interne=" . sql_quote($statut_interne);
	elseif ($statut_interne == 'tous')
		return "statut_interne LIKE '%'";
	else {
		set_request('statut_interne', 'defaut');
		$a = $GLOBALS['association_liste_des_statuts'];
		array_shift($a);
		return sql_in("statut_interne", $a);
	}
}

function association_ajouterBoutons($boutons_admin) {
		// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		$menu = "naviguer";
		$icone = "annonce.gif";
		if (isset($boutons_admin['bando_reactions'])){
			$menu = "bando_reactions";
			$icone = "annonce.gif";
		}
		$boutons_admin[$menu]->sousmenu['association']= new Bouton(
			_DIR_PLUGIN_ASSOCIATION_ICONES.$icone,  // icone
			_T('asso:titre_menu_gestion_association') //titre
			);
			
	}
	return $boutons_admin;
}
	

function association_mode_de_paiement($journal, $label)
{
	$sel = '';
	$sql = sql_select("code,intitule", "spip_asso_plan", "classe=".sql_quote($GLOBALS['association_metas']['classe_banques']), '', "code") ;
	while ($banque = sql_fetch($sql)) {
		$c = $banque['code'];
		$sel .= "<option value='$c'"
		. (($journal==$c) ? ' selected="selected"' : '')
		. '>' . $banque['intitule'] ."</option>\n";
	}

	return '<label for="journal"><strong>'
	  . $label
	  . "&nbsp;:</strong></label>\n"
	  . (!$sel 
	      ? "<input name='journal' id='journal' class='formo' />"
	      : "<select name='journal' id='journal' class='formo'>$sel</select>\n");
}

// affichage du nom des membres
function association_calculer_nom_membre($civilite, $prenom, $nom) {
	$res = ($GLOBALS['association_metas']['civilite']=="on")?$civilite.' ':'';
	$res .= ($GLOBALS['association_metas']['prenom']=="on")?$prenom.' ':'';
	$res .= $nom;
	return $res;
}

//Conversion de date
function association_datefr($date) { 
		$split = explode('-',$date); 
		$annee = $split[0]; 
		$mois = $split[1]; 
		$jour = $split[2]; 
		return $jour.'/'.$mois.'/'.$annee; 
	}

function association_verifier_date($date) {
	if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date)) return _T('asso:erreur_format_date');
	list($annee, $mois, $jour) = explode("-",$date);
	if (!checkdate($mois, $jour, $annee)) return _T('asso:erreur_date');
	return;
}
	
function association_nbrefr($montant) {
		$montant = number_format(floatval($montant), 2, ',', ' ');
		return $montant;
	}

/* prend en parametre le nom de l'argument a chercher dans _request et retourne un float */
function association_recupere_montant ($valeur) {
	if ($valeur != '') {
		$valeur = str_replace(" ", "", $valeur); /* suppprime les espaces separateurs de milliers */
		$valeur = str_replace(",", ".", $valeur); /* convertit les , en . */
		$valeur = floatval($valeur);
	} else $valeur = 0.0;
	return $valeur;
}

	//Affichage du message indiquant la date 
function association_date_du_jour($heure=false) {
		return '<p>'.($heure ? _T('asso:date_du_jour_heure') : _T('asso:date_du_jour')).'</p>';
	}
	
function association_flottant($s)
{
	return number_format(floatval($s), 2, ',', ' ');
}

function association_header_prive($flux){
	$c = direction_css(find_in_path('association.css'));
	return "$flux\n<link rel='stylesheet' type='text/css' href='$c' />";
}

function association_delete_tables($flux){
  spip_unlink(cache_meta('association_metas'));
}

function association_plan_comptable_complet() {
    /**
     * Plan comptable prenant en compte les nouvelles dispositions du règlement
     * N° 99-01 du 16 février 1999 relatif aux modalités d’établissement des comptes
     * annuels des associations et fondations.
     *
     * NB : Les associations peuvent limiter l’utilisation des comptes comptables
     * aux trois premières racines de chaque classe.
     * Ex : le compte 613 correspondant aux locations peut s’avérer suffisant pour y
     * inclure toutes les locations. Il n’est pas forcément obligatoire pour les
     * toutes petites associations de créer des sous-comptes.
     *
     * La structure des comptes de la classe 3 sera harmonisee avec celle adoptee
     * pour les comptes 60 et 70.
     * Les associations releveront le plus souvent, non pas de l’inventaire
     * permanent, mais de l’inventaire intermittent.
     */
    $pcc = array(
             1 => "Comptes de capitaux (fonds propres, emprunts et dettes assimilées)",
            10 => "Fonds associatif et réserves",
            102 => "Fonds associatif (sans droit de reprise)",
            1021 => "Valeur du patrimoine intégré",
            1022 => "Fonds statutaire (en application de l’art 11 loi 1901)",
            1023 => "Subventions d’investissement (non renouvelables)",
            1024 => "Apports sans droit de reprise",
            1025 => "Legs et donation avec contrepartie d’actifs immobilisés",
            1026 => "Subventions d’investissement affectées à des biens renouvelables",
            103 => "Fonds associatif (avec droit de reprise)",
            1031 => "Valeur des biens affectés (repris à la fin du contrat d’apport)",
            1032 => "Valeur des biens affectés (repris à la dissolution de l’association)",
            1033 => "Valeur des biens non affectés (repris à la fin du contrat d’apport)",
            1034 => "Apports avec droit de reprise",
            1035 => "Legs et donations avec contrepartie d’actifs immobilisés assortis d’une obligation ou d’une condition",
            1036 => "Subventions d’investissement affectés à des biens renouvelables",
            1039 => "Fond associatif avec droit de reprise inscrit au compte de résultat",
            105 => "Ecarts de réévaluation",
            1052 => "Ecarts de réévaluation (immobilisations non grevées d’un droit de reprise)",
            1053 => "Ecarts de réévaluation (immobilisations grevées d’un droit de reprise)",
            10531 => "Ecarts de réévaluation (immobilisations grevées d’un droit de reprise avant la dissolution de l’association)",
            10532 => "Ecarts de réévaluation (immobilisations grevées d’un droit de reprise à dissolution de l’association)",
            106 => "Réserves",
            1062 => "Réserves indisponibles",
            1063 => "Réserves statutaires ou contractuelles", #Notamment les réserves prévues dans les statuts des associations reconnues d’utilité publique
            1064 => "Réserves réglementés",
            1068 => "Autres réserves (dont réserves pour projet associatif)",
            10682 => "Réserves pour investissements",
            10683 => "Réserves de trésorerie (provenant du résultat)",
            10688 => "Réserves diverses",
            11 => "Report à nouveau - Eléments en instance d’affectation",
            110 => "Report à nouveau",
            115 => "Résultats sous contrôle de tiers financeurs",
            12 => "Résultat de l’exercice (excédent ou déficit)",
            120 => "Résultat de l’exercice (excédent)",
            129 => "Résultat de l’exercice (déficit)",
            13 => "Subventions d’investissement affectés à des biens non renouvelables",
            15 => "Provisions pour risques et charges",
            151 => "Provisions pour risques",
            1516 => "Provisions pour risques d’emploi",
            1518 => "Autres provisions pour risques",
            157 => "Provisions pour charges à répartir sur plusieurs exercices",
            1572 => "Provisions pour grosses réparations",
            16 => "Emprunts et dettes assimilées",
            164 => "Emprunts auprès des établissements de crédit",
            1641 => "Emprunts (à détailler)",
            167 => "Emprunts et dettes assorties de conditions particulières",
            1672 => "Titre associatif",
            168 => "Autres emprunts et dettes assimilées",
            1681 => "Autres emprunts (à détailler)",
            1685 => "Rentes viagères capitalisées",
            1687 => "Autres dettes (à détailler)",
            1688 => "Intérêts courus (à détailler)",
            18 => "Comptes de liaison des établissements (avec le siège social ou entre eux)",
            181 => "Apports permanents entre siège social et établissements",
            185 => "Biens et prestations de services échangés entre établissements et le siège social",
            186 => "Biens et prestations de services échangés entre établissement (charges)",
            187 => "Biens et prestations de services échangés entre établissement (produits)",
            19 => "Projet associatif et fonds dédiés",
            194 => "Fonds dédiés sur subventions de fonctionnement",
            195 => "Fond dédiés sur dons manuels affectés",
            197 => "Fonds dédiés sur legs et donations affectés",
            198 => "Excédent disponible après affectation au projet associatif",
            199 => "Reprise des fonds affectés au projet associatif",

            2 => "Comptes d’immobilisations",
            20 => "Immobilisations incorporelles",
            201 => "Frais d’établissement",
            2012 => "Frais de premier établissement",
            206 => "Droit au bail",
            208 => "Autres immobilisations incorporelles",
            21 => "Immobilisations corporelles",
            211 => "Terrains",
            212 => "Agencements et aménagements de terrains",
            213 => "Constructions",
            2131 => "Bâtiments",
            2135 => "Installations générales, agencements, aménagements des constructions",
            214 => "Constructions sur sol d’autrui",
            215 => "Installations techniques, matériel et outillage industriels",
            2151 => "Installations complexes spécialisées",
            2154 => "Matériel industriel",
            2155 => "Outillage industriel",
            218 => "Autres immobilisations corporelles",
            2181 => "Installations générales, agencements, aménagements divers", # Dans des constructions dont l’association n’est pas propriétaire.
            2182 => "Matériel de transport",
            2183 => "Matériel de bureau et matériel informatique",
            2184 => "Mobilier",
            2185 => "Cheptel",
            22 => "Immobilisations mises en concession",
            228 => "Immobilisations grevées de droits",
            229 => "Droits des propriétaires",
            23 => "Immobilisations en cours",
            231 => "Immobilisations corporelles en cours",
            2313 => "Constructions",
            2315 => "Installations techniques, matériel et outillage industriels",
            2318 => "Autres immobilisations corporelles",
            238 => "Avances et acomptes versés sur commandes d’immobilisations corporelles",
            26 => "Participations et créances rattachées à des participations",
            261 => "titres de participation",
            266 => "Autres formes de participation",
            267 => "Créances rattachées à des participations",
            269 => "Versements restant à effectuer sur titres de participation non libérés",
            27 => "Autres immobilisations financières",
            271 => "Titres immobilisés (droit de propriété)",
            2711 => "Actions",
            272 => "Titres immobilisés (droit de créance)",
            2721 => "Obligations",
            2722 => "Bons",
            2728 => "Autres",
            274 => "Prêts",
            2743 => "Prêts au personnel",
            2748 => "Autres prêts",
            275 => "Dépôts et cautionnements versés",
            2751 => "Dépôts",
            2755 => "Cautionnements",
            276 => "Autres créances immobilisées",
            2761 => "Créances diverses",
            2768 => "Intérêts courus (à détailler)",
            279 => "Versements restant à effectuer sur titres immobilisés non libérés",
            28 => "Amortissements des immobilisations",
            280 => "Amortissements des immobilisations incorporelles",
            2801 => "Frais d’établissement (même ventilation que celle du compte 201)",
            2808 => "Autres immobilisations incorporelles",
            281 => "Amortissements des immobilisations corporelles",
            2812 => "Agencements, aménagements des terrains (même ventilation que celle du compte 212)",
            2813 => "Constructions (même ventilation que celle du compte 213)",
            2814 => "Construction sur sol d’autrui (même ventilation que celle du compte 214)",
            2815 => "Installations techniques, matériel et outillage industriels (même ventilation que celle du compte 215)",
            2818 => "Autres immobilisations corporelles (même ventilation que celle du compte 218)",
            29 => "provisions pour dépréciation des immobilisations",
            290 => "Provisions pour dépréciation des immobilisations incorporelles",
            2906 => "Droit au bail",
            2908 => "Autres immobilisations incorporelles",
            291 => "Provisions pour dépréciation des immobilisations corporelles",
            2911 => "Terrains",
            296 => "Provisions pour dépréciation des participations et créances rattachées à des participations",
            2961 => "Tires de participation",
            2966 => "Autres formes de participation",
            2967 => "Créances rattachées à des participations (même ventilation que celle du compte 267)",
            297 => "Provisions pour dépréciation des autres immobilisations financières",
            2971 => "Titres immobilisés (droit de propriété) (même ventilation que celle du compte 271)",
            2972 => "Titres immobilisés (droit de créance) (même ventilation que celle du compte 272)",
            2974 => "Prêts (même ventilation que celle du compte 274)",
            2975 => "dépôts et cautionnements versés (même ventilation que celle du compte 275)",
            2976 => "Autres créances immobilisées (même ventilation que celle du compte 276)",

            3 => "Comptes de stocks et en-cours",
            31 => "Matières premières et fournitures",
            32 => "Autres approvisionnements",
            33 => "En-cours de production de biens",
            34 => "En-cours de production de services",
            35 => "Stocks de produits",
            37 => "Stocks de marchandises",
            39 => "Provisions pour dépréciation des stocks et en-cours",
            391 => "Provisions pour dépréciation des matières premières et fournitures",
            392 => "Provisions pour dépréciation des autres approvisionnements",
            393 => "Provisions pour dépréciation des en-cours de production de biens",
            394 => "Provisions pour dépréciation des en-cours de production de services",
            395 => "Provisions pour dépréciation des stocks de produits",
            397 => "Provisions pour dépréciation des stocks de marchandises",

            4 => "Comptes de tiers",
            40 => "Fournisseurs et comptes rattachés",
            401 => "Fournisseurs",
            4011 => "Fournisseurs – Achats de biens ou de prestations de services",
            404 => "Fournisseurs d’immobilisations",
            4041 => "Fournisseurs – achats d’immobilisations",
            4047 => "Fournisseurs d’immobilisations – retenues de garantie",
            408 => "Fournisseurs – factures non parvenues",
            4081 => "Fournisseurs – achats de biens ou de prestations de services",
            4084 => "Fournisseurs – achats d’immobilisations",
            409 => "Fournisseurs débiteurs",
            4091 => "Fournisseurs –avances et acomptes versés sur commandes",
            4096 => "Fournisseurs – Créances pour emballage et matériel à rendre",
            41 => "Usagers et comptes rattachés",
            411 => "Usagers (et organismes de prise en charge)",
            416 => "Créances douteuses ou litigieuses",
            418 => "Usagers – produits non encore facturés",
            419 => "Usagers créditeurs",
            42 => "Personnel et comptes rattachés",
            421 => "Personnel – rémunérations dues",
            422 => "Comités d’entreprise, d’établissement",
            425 => "Personnel – avances et acomptes",
            427 => "Personnel – oppositions",
            428 => "Personnel – Charges à payer et produits à recevoir",
            4282 => "Dettes provisionnées pour congés à payer",
            4286 => "Autres charges à payer",
            4287 => "Produits à recevoir",
            43 => "Sécurité sociale et autres organismes sociaux",
            431 => "sécurité sociale",
            437 => "Autres organismes sociaux",
            4372 => "Mutuelles",
            4373 => "Caisses de retraites et de prévoyance",
            4374 => "Caisse d’allocations de chômage - ASSEDIC",
            4378 => "Autres organismes sociaux – divers",
            438 => "Organismes sociaux – charges à payer et produits à recevoir",
            4382 => "Charges sociales sur congés à payer",
            4386 => "Autres charges à payer",
            4387 => "Produits à recevoir",
            44 => "Etat et autres collectivités publiques",
            441 => "Etat – Subventions à recevoir",
            4411 => "Subventions d’investissement",
            4417 => "Subventions d’exploitation",
            4419 => "Avances sur subventions",
            444 => "Etat – impôts sur les bénéfices",
            4445 => "Etat – impôt sur les sociétés (organismes sans but lucratif)",
            445 => "Etat – taxes sur le chiffre d’affaires",
            447 => "Autres impôts, taxes et versements assimilés",
            4471 => "Impôts, taxes et versements assimilés sur rémunérations (administration des impôts)",
            44711 => "Taxe sur les salaires",
            44713 => "Participation des employeurs à la formation professionnelle continue",
            44714 => "Cotisation pour défaut d’investissement obligatoire dans la construction",
            44718 => "Autres impôts, taxes et versements assimilés",
            4473 => "Impôts, taxes et versements assimilés sur rémunérations (autres organismes)",
            44733 => "Participation des employeurs à la formation professionnelle continue",
            44734 => "Participation des employeurs à l’effort de construction (versements à fonds perdu)",
            4475 => "Autres impôts, taxes et versements assimilés (administration des impôts)",
            4477 => "Autres impôts, taxes et versements assimilés (autres organismes)",
            448 => "Etat – charges à payer et produits à recevoir",
            4482 => "Charges fiscales sur congés à payés",
            4486 => "Autres charges à payer",
            4487 => "Produits à recevoir",
            45 => "Confédération, fédération, union,  associations affiliées",
            451 => "Confédération, fédération, associations affiliées – compte courant",
            455 => "sociétaires – comptes courants",
            46 => "Débiteurs divers et créditeurs divers",
            467 => "Autres comptes débiteurs ou créditeurs",
            468 => "Divers – Charges à payer et produits à recevoir",
            4686 => "Charges à payer",
            4687 => "Produits à recevoir",
            47 => "Comptes d’attente", #Sauf impossibilité, les opérations inscrites dans ces comptes sont reclassées en fin d’exercice parmi les comptes figurant au modèle de bilan.
            471 => "Recettes à classer",
            472 => "dépenses à classer et à régulariser",
            475 => "Legs et donations en cours de réalisation",
            48 => "Comptes de régularisation",
            481 => "Charges à répartir sur plusieurs exercices",
            4812 => "Frais d’acquisition des immobilisations",
            4818 => "Charges à étaler",
            486 => "Charges constatée d’avance",
            487 => "Produits constatés d’avance",
            49 => "Provision pour dépréciation des comptes de tiers",
            491 => "Provisions pour dépréciation des comptes d’usagers (et organismes de prise en charge)",
            496 => "Provision pour dépréciation des comptes de débiteurs divers",

            5 => "Comptes financiers",
            50 => "Valeurs mobilières de placement",
            503 => "Actions",
            5031 => "Titres cotés",
            5035 => "Titres non cotés",
            506 => "Obligations",
            5061 => "Titres cotés",
            5065 => "Titres non cotés",
            507 => "Bons du trésor et bons de caisse à court terme",
            508 => "Autres valeurs mobilières et créances assimilées",
            5081 => "Autres valeurs mobilières",
            5088 => "Intérêts courus sur obligations, bons et valeurs assimilées",
            51 => "Banques, établissements financiers et assimilés",
            511 => "Valeurs à l’encaissement",
            512 => "Banques",
            513 => "Caisse des dépôts et consignations",
            514 => "Chèques postaux",
            515 => "« Caisses » du trésor et des établissements publics",
            517 => "Autres organismes financiers",
            5171 => "caisse d’épargne",
            518 => "Intérêts courus",
            5186 => "Intérêts courus à payer",
            5187 => "Intérêts courus à recevoir",
            53 => "Caisse",
            531 => "Caisse du siège",
            532 => "Caisse des lieux d’activités",
            54 => "Régies d’avances et accréditifs",
            541 => "Régies d’avances",
            542 => "Accréditifs",
            58 => "Virements internes",
            581 => "Virements de fonds",
            59 => "Provisions pour dépréciation des comptes financiers",
            590 => "Provisions pour dépréciation des valeurs mobilières de placement",

            6 =>"Comptes de charges",
            60 => "Achats (sauf 603)",
            601 => "Achats stockés – matières premières et fournitures", # Structure laissée libre en vue de répondre à la diversité des actions entreprises par le secteur associatif.
            602 => "Achats stockés – Autres approvisionnements", # Structure laissée libre en vue de répondre à la diversité des actions entreprises par le secteur associatif.
            603 => "Variation des stocks (approvisionnement et marchandises)",
            604 => "Achats d’études et prestations de services", # Incorporés directement aux produits et prestations de service.
            606 => "Achats non stockés de matières et fournitures", # Structure laissée libre en vue de répondre à la diversité des actions entreprises par le secteur associatif.
            6061 => "Fournitures non stockables (eau, énergie,…)",
            6063 => "Fournitures d’entretiens et de petit équipement",
            6064 => "Fournitures administratives",
            6068 => "Autres matières et fournitures",
            607 => "Achats de marchandises",
            6071 => "Marchandises A",
            6072 => "Marchandises B",
            603 => "Variation des stocks (approvisionnements et marchandises)",
            6031 => "Variation des stocks de matières premières et fournitures",
            6032 => "Variation des stocks des autres approvisionnements",
            6037 => "Variation des stocks de marchandises",
            61 => "Services extérieurs",
            611 => "Sous-traitance générale", # Autre que sous-traitance incorporée directement aux produits fabriqués et inscrite au compte 604.
            612 => "Redevances de crédit-bail",
            6122 => "Crédit-bail mobilier",
            613 => "Locations",
            6132 => "Locations immobilières",
            6135 => "Locations mobilières",
            614 => "Charges locatives et de copropriété",
            615 => "Entretiens et réparations",
            6152 => "sur biens immobiliers",
            6155 => "sur biens mobiliers",
            6156 => "Maintenance",
            616 => "Primes d’assurance",
            6161 => "Multirisques",
            6162 => "Assurance obligatoire dommage-construction",
            6168 => "Autres assurances",
            617 => "Etudes et recherches",
            618 => "Divers",
            6181 => "Documentation générale",
            6183 => "Documentation technique",
            6185 => "Frais de colloques, séminaires, conférences",
            619 => "Rabais, remises, ristournes obtenus sur services extérieurs",
            62 => "Autres services extérieurs",
            621 => "Personnel extérieur à l’association",
            622 => "Rémunérations d’intermédiaires et honoraires",
            6226 => "Honoraires",
            6227 => "Frais d’actes et de contentieux",
            623 => "Publicité, publications, relations publiques",
            6231 => "Annonces et insertions",
            6233 => "Foires et expositions",
            6236 => "Catalogues et imprimés",
            6237 => "Publications",
            6238 => "Divers (pourboires, dons courants, ...)",
            624 => "Transports de biens et transports collectifs du personnel",
            6241 => "Transports sur achats",
            6243 => "Transports entre établissements",
            6247 => "Transports collectifs du personnel",
            6248 => "Divers",
            625 => "Déplacements, missions et réceptions",
            6251 => "Voyages et déplacements",
            6256 => "Missions",
            6257 => "Réceptions",
            626 => "Frais postaux et frais de télécommunications",
            627 => "Frais de tenu du compte bancaire",
            628 => "Divers",
            6281 => "Cotisations (liées à l’activité économique)",
            6284 => "Frais de recrutement du personnel",
            629 => "Rabais, remises et ristournes obtenus sur autres services extérieurs",
            63 => "Impôts, taxes et versements assimilés",
            631 => "Impôts, taxes et versements assimilés sur rémunérations (administration des impôts)",
            6311 => "Taxe sur salaires",
            6313 => "Participation des  employeurs à la formation professionnelle continue",
            6314 => "Cotisation pour défaut d’investissement obligatoire dans la construction",
            633 => "Impôts, taxes et versements assimilés sur rémunérations (autres organismes)",
            6331 => "Versement de transport",
            6333 => "Participation des employeurs à la formation professionnelle continue",
            6334 => "Participation des employeurs à l’effort de construction (versement à fonds perdu)",
            635 => "Autres impôts, taxes et versements assimilés (administration des impôts)",
            6351 => "Impôts directs",
            63512 => "Taxes foncières",
            63513 => "Autres impôts locaux",
            63518 => "Autres impôts directs",
            6353 => "Impôts indirects",
            6354 => "Droits d’enregistrement et de timbre",
            6358 => "Autres droits",
            637 => "Autres impôts, taxes et versements assimilés (autres organismes)",
            64 => "Charges de personnel",
            641 => "Rémunérations du personnel",
            6411 => "Salaires, appointements",
            6412 => "Congés payés",
            6413 => "Primes et gratifications",
            6414 => "Indemnités et avantages divers",
            6415 => "Supplément familial",
            645 => "Charges de sécurité sociale et de prévoyance",
            6451 => "Cotisations à l’URSSAF",
            6452 => "Cotisations aux mutuelles",
            6453 => "Cotisations aux caisses de retraites et de prévoyance",
            6454 => "Cotisations aux ASSEDIC",
            6458 => "Cotisations aux autres organismes sociaux",
            647 => "Autres charges sociales",
            6472 => "Versements aux comités d’entreprise et d’établissement",
            6475 => "Médecine du travail, pharmacie",
            648 => "Autres charges de personnel",
            65 => "Autres charges de gestion courante",
            651 => "Redevances pour concessions, brevets, licences, marques, procédés",
            6516 => "Droits d’auteur et de reproduction (SACEM)",
            6518 => "Autres droits et valeurs similaires",
            654 => "Pertes sur créances irrécouvrables",
            6541 => "Créances de l’exercice",
            6544 => "Créances des exercices antérieurs",
            657 => "Subventions versées par l’association",
            6571 => "Bourses accordées aux usagers",
            658 => "Charges diverses de gestion courante",
            6586 => "Cotisations (liées à la vie statutaire)",
            66 => "Charges financières",
            661 => "Charges d’intérêts",
            6611 => "Intérêts des emprunts et dettes",
            6616 => "Intérêts bancaires",
            6618 => "Intérêts des autres dettes",
            666 => "Pertes de change",
            667 => "Charges nettes sur cessions de valeurs mobilières de placement",
            67 => "Charges exceptionnelles",
            671 => "Charges exceptionnelles sur opérations de gestion",
            6712 => "Pénalités et amendes fiscales ou pénales",
            6713 => "Dons, libéralités",
            6714 => "Créances devenues irrécouvrables dans l’exercice",
            6717 => "Rappel d’impôts (autres qu’impôts sur les bénéfices)",
            6718 => "Autres charges exceptionnelles sur opérations de gestion",
            672 => "Charges sur exercices antérieurs (à reclasser)",
            675 => "Valeurs comptables des éléments d’actif cédés",
            6751 => "Immobilisations incorporelles",
            6752 => "Immobilisations corporelles",
            6756 => "Immobilisations financières",
            678 => "Autres charges exceptionnelles",
            68 => "Dotations aux amortissements, provisions et engagements",
            681 => "Dotations aux amortissements et aux provisions – charges d’exploitation",
            6811 => "Dotations aux amortissements des immobilisations incorporelles et corporelles",
            68111 => "Immobilisations incorporelles",
            68112 => "Immobilisations corporelles",
            6812 => "Dotations aux amortissements des charges d’exploitation à répartir",
            6815 => "Dotations aux provisions pour risques et charges d’exploitation",
            6816 => "Dotation aux provisions pour dépréciation des immobilisations incorporelles et corporelles",
            6817 => "Dotations aux provisions pour dépréciation des actifs circulants", # Autres que valeurs mobilières de placement.
            686 => "Dotations aux amortissements et aux provisions –Charges financières",
            6866 => "Dotations aux provisions pour dépréciation des éléments financiers",
            68662 => "Immobilisations financières",
            68665 => "Valeurs mobilières de placement",
            687 => "Dotations aux amortissements et aux provisions – charges exceptionnelles",
            6871 => "Dotations aux amortissements exceptionnels des immobilisations",
            6876 => "Dotations aux provisions pour dépréciations exceptionnelles",
            689 => "Engagements à réaliser sur ressources affectées",
            6984 => "Engagements à réaliser sur subvention attribuées",
            6895 => "Engagements à réaliser sur dons manuels affectés",
            6897 => "Engagements à réaliser sur legs et donations afffectés",
            69 => "Impôts sur les bénéfices",
            695 => "Impôts sur les sociétés des personnes morales non lucratives",

            7 => "Comptes de produits",
            70 => "Ventes de produits finis, prestations de services, marchandises",
            701 => "Ventes de produits finis",
            706 => "Prestations de services",
            707 => "Ventes de marchandises",
            708 => "Produits des activités annexes",
            7081 => "Produits des prestations fournies au personnel",
            7083 => "Locations diverses",
            7084 => "Mise à disposition de personnel facturée",
            7088 => "Autres produits d’activités annexes",
            709 => "Rabais, remises et ristournes accordées par l’association",
            71 => "Production stockée (ou déstockage)",
            713 => "Variation des stocks (en-cours de production, produits)",
            7133 => "Variation des en-cours de production de biens",
            7134 => "Variation des en-cours de production de services",
            7135 => "Variation des stocks de produits",
            72 => "Production immobilisée",
            74 => "Subvention d’exploitation",
            75 => "Autres produits de gestion courante",
            751 => "redevances pour concessions, brevets, licences, marques, procédés, droits et valeurs similaires",
            754 => "Collectes",
            756 => "Cotisations",
            757 => "Quote-part d’éléments du fonds associatif virée au compte de résultat",
            7571 => "Quote-part de subventions d’investissement (renouvelables) virée au compte de résultat",
            7573 => "Quote-part des apports virée au compte de résultat",
            758 => "Produits divers de gestion courante",
            7585 => "Contributions volontaires1",
            7586 => "Contributions volontaires2",
            7587 => "Contributions volontaires3",
            7588 => "Contributions volontaires4",
            76 => "Produits financiers",
            761 => "Produits des participations",
            762 => "Produits des autres immobilisations financières",
            7621 => "Revenus des titres immobilisés",
            7624 => "Revenus des prêts",
            764 => "Revenus des valeurs mobilières de placement",
            765 => "Escomptes obtenus",
            766 => "Gains de change",
            767 => "Produits nets sur cessions de valeurs mobilières de placement",
            768 => "Autres produits financiers",
            7681 => "Intérêts des comptes financiers débiteurs",
            77 => "Produits exceptionnels",
            771 => "Produits exceptionnels sur opérations de gestion",
            7713 => "Libéralités perçues",
            7714 => "Rentrées sur créances amorties",
            7715 => "Subventions d’équilibre",
            7717 => "Dégrèvements d’impôts (autres qu’impôts sur les bénéfices)",
            7718 => "Autres produits exceptionnels sur opérations de gestion",
            772 => "Produits sur exercices antérieurs (à reclasser)",
            775 => "Produits des cessions d’éléments d’actif",
            7751 => "Immobilisations incorporelles",
            7752 => "Immobilisations corporelles",
            7756 => "Immobilisations financières",
            777 => "Quote-part des subventions d’investissement virée au résultat de l’exercice",
            778 => "Autres produits exceptionnels",
            78 => "Reprises sur amortissements et provisions",
            781 => "Reprises sur amortissements et provisions (à inscrire dans les produits d’exploitation)",
            7811 => "Reprises sur amortissements des immobilisations incorporelles et corporelles",
            7815 => "Reprises sur provisions pour risques et charges d’exploitation",
            7816 => "Reprises sur provisions pour dépréciation des immobilisations incorporelles et corporelles",
            7817 => "reprises sur provisions pour  dépréciation des actifs circulants", # Autres que valeurs mobilières de placements.
            786 => "Reprises sur provisions (à inscrire dans les produits financiers)",
            7866 => "Reprises sur provisions pour dépréciation des éléments financiers",
            78662 => "Immobilisations financières",
            78665 => "Valeurs mobilières de placement",
            787 => "Reprises sur provisions (à inscrire dans les produits exceptionnels)",
            7876 => "reprises sur provisions pour dépréciations exceptionnelles",
            789 => "Report des ressources non utilisées des exercices antérieurs (à éclater)",
            79 => "Transferts de charges",
            791 => "Transferts de charges d’exploitation",
            796 => "Transferts de charges financières",
            797 => "Transferts de charges exceptionnelles",

            8 => "Traitement des contributions volontaires en nature",
            86 => "Répartition par nature de charges",
            860 => "Secours en nature, alimentaires, vestimentaires ...",
            861 => "Mise à disposition gratuite de biens, locaux, matériels, ...",
            862 => "Prestations",
            864 => "Personnel bénévole",
            87 => "Répartition par nature de ressources",
            870 => "Bénévolat",
            871 => "Prestations en nature",
            875 => "Dons en nature"
    );

    return $pcc;
}

// Raccourcis
// Les tables ayant 2 prefixes ("spip_asso_")
// le raccourci "don" implique de declarer le raccourci "asso_don" etc.

function generer_url_asso_don($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_don', "id=" . intval($id));
}

function generer_url_don($id, $param='', $ancre='') {
	return  array('asso_don', $id);
}

function generer_url_asso_membre($id, $param='', $ancre='') {
	return  generer_url_ecrire('voir_adherent', "id=" . intval($id));
}

function generer_url_membre($id, $param='', $ancre='') {
	return  array('asso_membre', $id);
}

function generer_url_asso_vente($id, $param='', $ancre='') {
	return  generer_url_ecrire('edit_vente', "id=" . intval($id));
}

function generer_url_vente($id, $param='', $ancre='') {
	return  array('asso_vente', $id);
}

function instituer_adherent_ici($auteur=array()){
	$instituer_adherent = charger_fonction('instituer_adherent', 'inc');
	return $instituer_adherent($auteur);
}
function instituer_statut_interne_ici($auteur=array()){
	$instituer_statut_interne = charger_fonction('instituer_statut_interne', 'inc');
	return $instituer_statut_interne($auteur);
}


// pour executer les squelettes comportant la balise Meta
include_spip('balise/meta');
// charger les metas donnees
$inc_meta = charger_fonction('meta', 'inc'); // inc_version l'a deja chargee
$inc_meta('association_metas'); 
?>
