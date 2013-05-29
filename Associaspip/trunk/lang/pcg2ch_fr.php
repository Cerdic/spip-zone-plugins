<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

$pc_norme = array(
	1, //0: min classe
	9, //1: max classe
	3, //2: min longueur code
);

// http://www.lpg-fiduciaire-de-suisse.ch/plan-comptable-pour-les-entreprises-suisses.html
// http://campus.hesge.ch/desjacqc/doc/ID/2011/plan_comptable.pdf
//
$GLOBALS[$GLOBALS['idx_lang']] = $pc_liste = array(

	1 => "Actif",
	10 => "Actifs circulants",
	100 => "Liquidités",
	1000 => "Caisse",
	1010 => "Compte postal",
	1020 => "Banque",
	110 => "Créances résultant prestations envers des tiers",
	1100 => "Créances envers des tiers suisses",
	1109 => "Ducroire",
	114 => "Autres créances à court terme envers des tiers",
	1140 => "Avances à court terme",
	1149 => "Provisions pertes s/autres créances envers tiers",
	116 => "Autres créances à court terme envers actionnaires",
	1160 => "Créances d'emprunt envers l'actionnaire X",
	1169 => "Provisions pertes s/autres créances actionnaires",
	117 => "Créances envers des institutions publiques",
	1170 => "TVA: déductible s/achats de matières et services",
	1171 => "TVA: déductible s/investissement et autres charges",
	1176 => "Impôt anticipé à récupérer",
	1177 => "Créances envers l'administration des douanes",
	1178 => "Créances envers la SUVA",
	119 => "Autres créances à court terme",
	1190 => "Créances envers des sociétés de virement",
	1192 => "Acomptes aux fournisseurs",
	120 => "Stocks de marchandises",
	1201 => "Stocks de marchandises A",
	1202 => "Stocks de marchandises B",
	1209 => "Corrections valeur s/stocks de marchandises",
	130 => "Actifs transitoires (de régularisation)",
	1300 => "Charges payées d'avance",
	1301 => "Produits à recevoir",
	14 => "Actifs immobilisés",
	149 => "Actions propres",
	1490 => "Actions propres",
	1499 => "Corrections valeur s/actions propres",
	150 => "Machines et appareils destinés à la production",
	1500 => "Machines et appareils",
	151 => "Mobilier et installations",
	1510 => "Mobilier d'exploitation",
	1511 => "Installations d'ateliers",
	1512 => "Installations d'entrepôts",
	1513 => "Mobilier de bureau",
	152 => "Machines de bureau, informatiques, communication",
	1520 => "Machines de bureau",
	1521 => "Infrastructures informatiques",
	1522 => "Systèmes de communication",
	1523 => "Systèmes à commande automatique",
	1524 => "Installations de sécurité",
	1525 => "Appareils électroniques de mesure et de contrôle",
	1526 => "Logiciels",
	153 => "Véhicules",
	1530 => "Automobiles",
	1531 => "Camionnettes",
	1532 => "Camions",
	1533 => "Véhicules spéciaux",
	154 => "Instruments et outillage",
	1540 => "Instruments et outillage",
	155 => "Installations de stockage",
	1550 => "Installations de stockage",
	1551 => "Entrepôts à hauts rayonnages",
	159 => "Autres immobilisations corporelles meubles",
	1590 => "Lingerie et habits de travail",
	1591 => "Moules et modèles",
	160 => "Bâtiments d'exploitation",
	1600 => "Bâtiments d'exploitation",
	1601 => "Entretien, réparation ou remplacementains",
	1609 => "Amortissement cumulé s/bâtiments d'exploitation",
	180 => "Frais de fondation, augmentation de capital",
	1800 => "Frais de fondation",
	1801 => "Frais d'augmentation de capital",
	1802 => "Frais d'organisation",
	185 => "Capital-actions non libéré",
	1850 => "Capital-actions non libéré",
	1859 => "Corrections de valeur s/capital-actions non libéré",

	2 => "Passif",
	20 => "Dettes à court terme",
	200 => "Dettes à court terme c/achats,prestations services",
	2000 => "Dettes c/achats de matières et marchandises",
	2001 => "Dettes c/prestations de services envers des tiers",
	2002 => "Dettes c/charges de personnel",
	2003 => "Dettes c/assurances sociales",
	2004 => "Dettes c/autres charges d'exploitation",
	2005 => "Dettes c/opérations de crédit-bail",
	203 => "Acomptes de clients",
	2030 => "Acomptes de clients",
	210 => "Dettes bancaires à court terme",
	2100 => "Dettes bancaires à court terme",
	211 => "Dettes c/chèques postaux, sociétés de virement",
	2110 => "Dettes envers les chèques postaux",
	2111 => "Dettes envers les sociétés de virement",
	214 => "Autres dettes financières à court terme à tiers",
	2140 => "Autres dettes financières à court terme à tiers",
	216 => "Dettes financières à court terme actionnaires",
	2160 => "Dettes financières à court terme c/actionnaire X",
	217 => "Dettes financières à court terme fonds prévoyance",
	2170 => "Dettes financières à court terme fonds prévoyance",
	218 => "Part à rembourser dettes financières à long terme",
	2180 => "Hypothèque à rembourser",
	2181 => "Prêt à rembourser",
	220 => "Dettes envers des institutions publiques",
	2200 => "TVA à payer",
	2205 => "AFC - TVA",
	2206 => "Impôt anticipé dû",
	2207 => "Droits de timbre dus",
	2208 => "Impôts directs dus",
	223 => "Dividendes et coupons d'obligations non encaissés",
	2230 => "Dividendes non encaissés de l'exercice",
	2231 => "Dividendes non encaissés des exercices précédents",
	2232 => "Coupons d'obligations non encaissés",
	230 => "Passifs de régularisation",
	2300 => "Charges à payer",
	2301 => "Produits encaissés d'avance",
	234 => "rovisions à court terme pour impôts",
	2340 => "Provisions pour impôts directs",
	2341 => "Provisions pour impôts indirects",
	24 => "Dettes à long terme",
	240 => "Dettes bancaires à long terme",
	2400 => "Dettes bancaires à long terme",
	242 => "Dettes résultant d'opérations de crédit-bail",
	2420 => "Dettes résultant d'opérations de crédit-bail",
	244 => "Dettes hypothécaires",
	2440 => "Hypothèques sur bâtiments d'exploitation",
	250 => "Emprunts à long terme à des tiers",
	2500 => "Emprunts à long terme à des tiers",
	256 => "Dettes à long terme envers des actionnaires",
	2560 => "Emprunts à long terme à des actionnaires",
	257 => "Dettes à long terme envers des institutions LPP",
	2570 => "Emprunts à long terme à des institutions LPP",
	260 => "Provisions réparation, assainissements, rénovation",
	2600 => "Provision pour réparations",
	2601 => "Provision pour assainissements",
	2602 => "Provision pour rénovations",
	263 => "Provisions résultant de ventes/prestations service",
	2630 => "Provisions pour travaux de garantie",
	28 => "Capitaux propres",
	280 => "Capital propre des entreprises raison individuelle",
	2800 => "Capital propre",
	2801 => "Capital propre du conjoint",
	281 => "Capital propre des sociétés de personnes",
	2810 => "Compte de capital, associé A",
	2811 => "Compte de capital, associé B",
	2812 => "Compte de commandite, commanditaire C",
	282 => "Capital social de la S.à.r.l",
	2820 => "Capital social de la S.à.r.l",
	284 => "Capital-actions et participation",
	2840 => "Capital-actions",
	2841 => "Capital-participation",
	285 => "Compte privé",
	2850 => "Prélèvements privés en espèces",
	2851 => "Prélèvements privés en nature",
	2852 => "Participations privées aux charges d'exploitation",
	2853 => "Valeur locative de l'appartement privé",
	2854 => "Primes d'assurance privées",
	2855 => "Cotisations privées à titre de prévoyance",
	2856 => "Impôts privés",
	288 => "Comptes pour immeubles et biens-fonds privés",
	2880 => "Immeuble privé A",
	290 => "Réserves légales",
	2900 => "Réserve générale",
	2901 => "Réserve pour actions propres",
	2903 => "Réserve de réévaluation",
	291 => "Autres réserves",
	2910 => "Réserves statutaires",
	299 => "Bénéfice ou déficit",
	2990 => "Bénéfice reporté / Déficit reporté",
	2991 => "Bénéfice de l'exercice / Déficit de l'exercice",

	3 => "Ventes de marchandises",
	32 => "Ventes brutes de marchandises",
	3210 => "Ventes brutes au comptant",
	3211 => "Ventes de détail brutes à crédit",
	3212 => "Ventes en gros brutes à crédit",
	3220 => "Ventes brutes TVA taux normal (TVA 8.00% net)",
	3221 => "Ventes brutes TVA taux réduit (TVA 2.50% net)",
	3222 => "Ventes brutes tva taux zéro (TVA 0.00% net)",
	33 => "Accessoires utilisés",
	3300 => "Autres matériaux directement incorporables",
	37 => "Consommations propres de marchandises",
	3720 => "Consommation propre",
	39 => "Déductions s/produits",
	3900 => "Escomptes",
	3901 => "Rabais et réductions de prix",
	3902 => "Remises",
	3903 => "Commissions de tiers",
	3904 => "Frais d'encaissement",
	3905 => "Pertes s/clients",
	3906 => "Différences de change",
	3907 => "Ports ou Fret",

	4 => "Charges liées aux marchandises",
	420 => "Charges de marchandises",
	4200 => "Achats de marchandises",
	4208 => "Variations de stocks",
	4209 => "Déductions obtenues s/achats",
	421 => "Charges de marchandises",
	4210 => "Achats de marchandises TVA taux normal (TVA 8.00% net)",
	4211 => "Achats de marchandises TVA taux réduit (TVA 2.50% net)",
	4212 => "Achats de marchandises TVA taux zéro (TVA 0.00% net)",
	4215 => "Achats de matériel d'emballage",
	4217 => "Charges directes d'achat",
	427 => "Charges directes d'achat s/marchandises",
	4270 => "Frets à l'achat",
	4271 => "Droits de douane à l'importation",
	4272 => "Frais de transport à l'achat",
	429 => "Déductions obtenues s/achats liés aux marchandises",
	4290 => "Escomptes",
	4291 => "Rabais et réductions de prix",
	4292 => "Remises",
	4293 => "Ristournes obtenues s/achats",
	4296 => "Différences de change",

	5 => "Charges liées aux employes",
	520 => "Charges de personnel",
	5200 => "Salaires",
	5205 => "Prestations des assurances sociales",
	527 => "Charges sociales",
	5270 => "AVS, AI, APG, assurance-chômage",
	5271 => "Caisse de compensation familiale",
	5272 => "Prévoyance professionnelle",
	5273 => "Assurance-accidents",
	5274 => "Assurance indemnités journalières en cas maladie",
	5279 => "Impôts à la source",
	528 => "Autres charges de personnel",
	5280 => "Recherche de personnel",
	5281 => "Formation et formation continue",
	5282 => "Indemnités effectives",
	5283 => "Indemnités de frais forfaitaires",
	5289 => "Autres charges de personnel",
	529 => "Prestations de travail de tiers",
	5290 => "Employés temporaires",

	6 =>"Charges d'exploitation et resultat financier",
	60 => "Loyers et accessoires",
	6000 => "Loyer",
	6030 => "Charges accessoires",
	6040 => "Nettoyage",
	6050 => "Entretien",
	6090 => "Charges de locaux comme prélèvements privés",
	61 => "Entretien, réparations, remplacements",
	610 => "Entretien, réparation ou remplacement de machines et outillage",
	6100 => "Entretien, réparation ou remplacement de machines",
	6102 => "Entretien, réparation ou remplacement d'outils matériel",
	613 => "Entretien, réparation ou remplacement d'installations de bureau",
	6130 => "Entretien, réparation ou remplacement du mobilier de bureau",
	6131 => "Entretien, réparation ou remplacement des machines de bureau",
	62 => "Charges de véhicules",
	620 => "Frais de véhicules",
	6200 => "Réparation, service et nettoyage",
	621 => "Frais de véhicules",
	6210 => "Carburants",
	622 => "Frais de véhicules",
	6220 => "Assurances et taxes",
	626 => "Frais de véhicules",
	6260 => "Charges de location pour voitures en crédit-bail",
	627 => "Frais de véhicules",
	6270 => "Charges de véhicules comme prélèvements privés",
	63 => "Assurances-choses, droits, taxes, et autorisations",
	630 => "Primes d'assurance",
	6300 => "Assurance pour dommages",
	631 => "Primes d'assurance",
	6310 => "Assurance responsabilité civile",
	633 => "Primes d'assurance",
	6330 => "Primes pour assurance-vie",
	6331 => "Primes pour cautionnement",
	636 => "Droits et taxes",
	6360 => "Droits",
	6361 => "Taxes",
	637 => "Autorisations et patentes",
	6370 => "Autorisations",
	6371 => "Patentes",
	64 => "Charges d'énergie et évacuation des déchets",
	640 => "Energie et déchets",
	6400 => "Force motrice",
	642 => "Energie et déchets",
	6420 => "Mazout",
	643 => "Energie et déchets",
	6430 => "Eau",
	646 => "Energie et déchets",
	6460 => "Evacuation de déchets",
	6462 => "Eaux usées",
	65 => "Charges administratives",
	650 => "Matériel de bureau, imprimés, photocopies",
	6500 => "Matériel de bureau",
	6501 => "Imprimés",
	6502 => "Photocopies",
	6503 => "Littérature technique",
	651 => "Téléphone, téléfax, Internet, frais de port",
	6510 => "Téléphone",
	6511 => "Téléfax",
	6512 => "Internet",
	6513 => "Frais de port",
	652 => "Cotisations, dons, cadeaux et pourboires",
	6520 => "Cotisations",
	6521 => "Dons et cadeaux",
	6522 => "Pourboires",
	653 => "Honoraires pour fiduciaire et conseil",
	6530 => "Honoraires pour fiduciaire",
	6531 => "Honoraires pour conseil",
	6532 => "Honoraires pour conseil juridique"
	654 => "Conseil d'administration, AG, OR",
	6540 => "Charges pour conseil d'administration",
	6541 => "Charges pour assemblée générale",
	6542 => "Charges pour organe de révision",
	655 => "Charges d'administration comme prélèvements",
	6550 => "Charges d'administration comme prélèvements privés",
	656 => "Locations en crédit-bail et locations de hard/soft",
	6560 => "Location en crédit-bail de matériel",
	6561 => "Location en crédit-bail de logiciels",
	6562 => "Location de matériel",
	657 => "Frais d'informatique",
	6570 => "Charges de licence/Update",
	6573 => "Disquettes, CD-Rom, cassettes, fournitures",
	6575 => "Frais de réseau",
	66 => "Publicité",
	660 => "Frais de publicité",
	6600 => "Publicité dans les journaux",
	661 => "Frais de publicité",
	6610 => "Imprimés publicitaires, matériel de publicité",
	6611 => "Articles de publicité, échantillons",
	662 => "Frais de publicité",
	6620 => "Vitrines, décoration",
	6621 => "Foires, expositions",
	664 => "Frais de publicité",
	6640 => "Frais de voyage",
	6641 => "Conseils à la clientèle",
	6642 => "Cadeaux à la clientèle",
	67 => "Autres charges",
	670 => "Informations économiques, poursuites",
	6700 => "Informations économiques",
	6701 => "Poursuites",
	671 => "Sécurité et surveillance",
	6710 => "Sécurité",
	6711 => "Surveillance",
	68 => "Résultat financier",
	680 => "Charges financières",
	6800 => "Charges financières pour crédit bancaire",
	6801 => "Charges financières pour emprunts",
	6802 => "Charges financières pour emprunts hypothécaires",
	6803 => "Intérêts moratoires",
	6804 => "Charges financières pour acomptes de clients",
	682 => "Charges financières",
	6820 => "Charges financières s/compte courant actionnaire A",
	683 => "Charges financières",
	6830 => "Charges financières pour financement LPP",
	684 => "Autres charges financières",
	6840 => "Frais de banque et des chèques postaux",
	6841 => "Frais de dépôt",
	6842 => "Pertes de change s/liquidités et titres",
	685 => "Produits financiers",
	685 => "Produits financiers",
	6850 => "Produits financiers s/avoirs postaux, bancaires",
	6851 => "Produits financiers s/avoirs à court terme",
	688 => "Produits financiers s/avoirs postaux, bancaires",
	6880 => "Produits financiers s/compte courant actionnaire",
	689 => "Autres produits financiers",
	6890 => "Produits financiers c/intérêts moratoires, escptes",
	6891 => "Produits financiers s/acomptes versés",
	6892 => "Gains de change sur liquidités et titres",
	69 => "Amortissement",
	692 => "Amortissements",
	6920 => "Amortissements s/machines et outillage",
	6921 => "Amortissements s/mobilier et installations",
	6922 => "Amortissements s/machines de bureau, informatique",
	6923 => "Amortissements s/véhicules",
	693 => "Amortissements",
	6930 => "Amortissements s/bâtiments d'exploitation",
	695 => "Amortissements",
	6950 => "Amortissements s/charges de fondation",

	7 => "Bénéfice sur vente d'actifs",
	79 => "Bénéfices s/immobilisations",
	7910 => "Bénéfices s/ventes d'équipements d'exploitation",
	7920 => "Bénéfices s/ventes d'immeubles",

	8 => "Résultat exeptionnel ou hors exploitation",
	800 => "Produits exceptionnels",
	8002 => "Réévaluations comptables",
	8004 => "Bénéfices exceptionnels s/aliénations actifs immob.",
	8005 => "Subventions obtenues",
	8006 => "Produits pour indemnités pour préjudices",
	801 => "Charges exceptionnelles",
	8011 => "Dotations exceptionnelles aux provisions",
	8012 => "Dotations exceptionnelles aux amortissements",
	8014 => "Pertes exceptionnelles s/aliénations actifs immob.",
	8015 => "Pertes exceptionnelles s/débiteurs",
	8016 => "Charges pour indemnités pour préjudices",
	85 => "RÉSULTAT HORS EXPLOITATION",
	850 => "Produits de l'immeuble hors exploitation",
	8500 => "Loyers des locaux",
	8501 => "Loyers des appartements",
	8502 => "Loyers des garages",
	8503 => "Loyer interne",
	851 => "Charges de l'immeuble hors exploitation",
	8510 => "Intérêts hypothécaires",
	8511 => "Entretien d'immeuble",
	8512 => "Droits, taxes, impôts fonciers",
	8513 => "Primes d'assurance",
	8514 => "Eau, eaux usées",
	8515 => "Ordures, évacuation des déchets",
	8516 => "Charges d'administration",
	870 => "Autres produits hors exploitation",
	8700 => "Honoraires pour expertises, conférences",
	8701 => "Jetons de présence",
	871 => "Autres charges hors exploitation",
	8710 => "Charges pour des activités hors exploitation",
	89 => "CHARGES D'IMPÔT",
	890 => "Impôts directs de l'entreprise",
	8900 => "Impôts sur le bénéfice",
	8901 => "Impôts sur le capital",
	8902 => "Impôts hors exercices",

	9 => "Compte de bilan et de résultat",
	90 => "Compte de résultat",
	900 => "Compte de résultat",
	9000 => "Compte de résultat",
	91 => "Bilan",
	910 => "Bilan",
	9100 => "Bilan d'ouverture",
	9101 => "Bilan de clôture",
	92 => "Utilisation du bénéfice",
	920 => "Utilisation du bénéfice",
	9200 => "Participation au bénéfice de l'associé X",
	9201 => "Participation au bénéfice de l'associé Y",
	99 => "Écriture de regroupements et de corrections",
	990 => "Écritures de regroupements",
	9900 => "Écritures de regroupements des débiteurs",
	9901 => "Écritures de regroupements des créditeurs",
	991 => "Écritures de corrections",
	9910 => "Écriture de correction",

);

?>