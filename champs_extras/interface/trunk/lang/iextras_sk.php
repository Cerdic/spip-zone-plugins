<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/iextras?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_associer' => 'riadiť toto pole',
	'action_associer_title' => 'Riadiť zobrazenie tohto doplnkového poľa',
	'action_desassocier' => 'zrušiť prepojenie',
	'action_desassocier_title' => 'Neovládať zobrazenie tohto doplnkového poľa',
	'action_descendre' => 'zostupne',
	'action_descendre_title' => 'Posunúť pole o jedno miesto nadol',
	'action_modifier' => 'upraviť',
	'action_modifier_title' => 'Upraviť parametre doplnkového poľa',
	'action_monter' => 'vzostupne',
	'action_monter_title' => 'Posunúť pole o jedno pole nahor',
	'action_supprimer' => 'odstrániť',
	'action_supprimer_title' => 'Úplne vymazať pole z databázy',

	// C
	'caracteres_autorises_champ' => 'Možné znaky: písmená bez diaktritiky, číslice, - a _',
	'caracteres_interdits' => 'Niektoré znaky, ktoré ste použili, nie sú vhodné pre toto pole.',
	'champ_deja_existant' => 'Rovnaké pole v tejto tabuľke už existuje.',
	'champ_sauvegarde' => 'Doplnkové pole bolo uložené!',
	'champs_extras' => 'Doplnkové polia',
	'champs_extras_de' => 'Doplnkové polia z(o): @objet@',

	// E
	'erreur_action' => 'Akcia @action@ neznáma.',
	'erreur_enregistrement_champ' => 'Problém pri vytvorení doplnkového poľa.',

	// I
	'icone_creer_champ_extra' => 'Vytvoriť nové doplnkové pole',
	'info_description_champ_extra' => 'Cette page permet de gérer des champs extras, 
						c\'est à dire des champs supplémentaires dans les tables de SPIP,
						pris en compte dans les formulaires d\'édition.', # NEW
	'info_description_champ_extra_creer' => 'Vous pouvez créer de nouveaux champs qui s\'afficheront alors
						sur cette page, dans le cadre «Liste des champs extras», ainsi que dans les formulaires.', # NEW
	'info_description_champ_extra_presents' => 'Enfin, si des champs existent déjà dans votre base de données,
						mais ne sont pas déclarés (par un plugin ou un jeu de squelettes), vous
						pouvez demander à ce plugin de les gérer. Ces champs, s\'il y en a,
						apparaissent dans un cadre «Liste des champs présents non gérés».', # NEW
	'info_modifier_champ_extra' => 'Upraviť doplnkové pole',
	'info_nouveau_champ_extra' => 'Nové doplnkové pole',
	'info_saisie' => 'Vstup:',

	// L
	'label_attention' => 'Veľmi dôležité vysvetlivky',
	'label_champ' => 'Názov poľa',
	'label_class' => 'Triedy CSS',
	'label_datas' => 'Zoznam hodnôt',
	'label_explication' => 'Vysvetlivky pre vstup',
	'label_label' => 'Menovka vstupu',
	'label_li_class' => 'Triedy CSS &lt;li&gt; nadradeného objektu',
	'label_obligatoire' => 'Je pole povinné?',
	'label_rechercher' => 'Vyhľadať',
	'label_rechercher_ponderation' => 'Dôležitosť vyhľadávania',
	'label_restrictions_auteur' => 'Podľa autora',
	'label_restrictions_branches' => 'Podľa vetvy',
	'label_restrictions_groupes' => 'Podľa skupiny',
	'label_restrictions_secteurs' => 'Podľa sektora',
	'label_saisie' => 'Typ vstupu',
	'label_sql' => 'Definícia SQL',
	'label_table' => 'Objekt',
	'label_traitements' => 'Automatizované spracovanie',
	'legend_declaration' => 'Deklarácia',
	'legend_options_saisies' => 'Možnosti vstupu',
	'legend_options_techniques' => 'Technické',
	'legend_restriction' => 'Obmedzenie',
	'legend_restrictions_modifier' => 'Upraviť vstup',
	'legend_restrictions_voir' => 'Zobraziť vstup',
	'liste_des_extras' => 'Zoznam doplnkových polí',
	'liste_des_extras_possibles' => 'Zoznam nekontrolovaných polí',
	'liste_objets_applicables' => 'Zoznam redakčných objektov',

	// N
	'nb_element' => '1 objekt',
	'nb_elements' => '@nb@ objektov',

	// P
	'precisions_pour_attention' => 'Pour quelque chose de très important à indiquer.
		À utiliser avec beaucoup de modération !
		Peut être une chaîne de langue «plugin:chaine».', # NEW
	'precisions_pour_class' => 'Ajouter des classes CSS sur l\'élément,
		séparées par un espace. Exemple : "inserer_barre_edition" pour un bloc
		avec le plugin Porte Plume', # NEW
	'precisions_pour_datas' => 'Certains types de champ demandent une liste des valeurs acceptées : indiquez-en une par ligne, suivie d\'une virgule et d\'une description. Une ligne vide pour la valeur par défaut. La description peut être une chaîne de langue.', # NEW
	'precisions_pour_explication' => 'Vous pouvez donner plus d\'informations concernant la saisie. 
		Peut être une chaîne de langue «plugin:chaine».', # NEW
	'precisions_pour_label' => 'Môže byť jazykový reťazec «plugin:chaine».',
	'precisions_pour_li_class' => 'Ajouter des classes CSS sur le &lt;li&gt; parent,
		séparées par un espace. Exemple : "haut" pour avoir toute la
		largeur sur le formulaire', # NEW
	'precisions_pour_nouvelle_saisie' => 'Umožňuje zmeniť typ vstupu, ktorý sa použije pre toto pole',
	'precisions_pour_nouvelle_saisie_attention' => 'Attention cependant, un changement de type de saisie perd les options de configuration de la saisie actuelle qui ne sont pas communes avec la nouvelle saisie sélectionnée !', # NEW
	'precisions_pour_rechercher' => 'Zaradiť toto pole do vyhľadávača?',
	'precisions_pour_rechercher_ponderation' => 'SPIP pondère une recherche dans une colonne par un coefficient de ponderation.
		Celui-ci permet de mettre en avant les colonnes les plus pertinentes (titre par exemple) par rapport à d\'autres qui le sont moins.
		Le coefficient appliqué sur les champs extras est par défaut 2. Pour vous donner un ordre d\'idée, notez que SPIP utilise 8 pour le titre, 1 pour le texte.', # NEW
	'precisions_pour_restrictions_branches' => 'Identifikátory vetiev, ktoré sa majú obmedziť (oddeľovač ":")',
	'precisions_pour_restrictions_groupes' => 'Identifikátory skupín, ktoré sa majú obmedziť (oddeľovač ":")',
	'precisions_pour_restrictions_secteurs' => 'Identifikátory sektorov, ktoré sa majú obmedziť (oddeľovač ":")',
	'precisions_pour_saisie' => 'Zobraziť vstup typu:',
	'precisions_pour_traitements' => 'Automaticky spracovať výsledok tagu #NOM_DU_CHAMP:',

	// R
	'radio_restrictions_auteur_admin' => 'Iba administrátori',
	'radio_restrictions_auteur_aucune' => 'Každý môže',
	'radio_restrictions_auteur_webmestre' => 'Iba webmasteri',
	'radio_traitements_aucun' => 'Žiadne',
	'radio_traitements_raccourcis' => 'Spracovať klávesové skratky SPIPu (vlastné)',
	'radio_traitements_typo' => 'Spracovať len typografiu (typo)',

	// S
	'saisies_champs_extras' => 'Z "doplnkových polí"',
	'saisies_saisies' => 'Zo "vstupov"',
	'supprimer_reelement' => 'Vymazať toto pole?',

	// T
	'titre_iextras' => 'Doplnkové polia',
	'titre_page_iextras' => 'Doplnkové polia',

	// V
	'veuillez_renseigner_ce_champ' => 'Prosím, vyplňte toto pole!'
);

?>
