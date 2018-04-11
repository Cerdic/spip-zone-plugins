<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2018-03-31 17:49:22
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$data = array (
  'fabrique' => 
  array (
    'version' => 6,
  ),
  'paquet' => 
  array (
    'prefixe' => 'prestations',
    'nom' => 'Prestations',
    'slogan' => '',
    'description' => 'Lister des choses à faire dans un projet et possiblement leur prix.',
    'logo' => 
    array (
      0 => '',
    ),
    'version' => '1.0.0',
    'auteur' => 'Les Développements Durables',
    'auteur_lien' => 'https://www.ldd.fr',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.2.0;3.2.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configuration des livrables',
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => '',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Prestations',
      'nom_singulier' => 'Prestation',
      'genre' => 'feminin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => 'on',
      'table' => 'spip_prestations',
      'cle_primaire' => 'id_prestation',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'prestation',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '10',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Type de prestation',
          'champ' => 'id_prestations_type',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'prestations_types',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Prix unitaire HT',
          'champ' => 'prix_unitaire_ht',
          'sql' => 'decimal(20,6) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Quantité',
          'champ' => 'quantite',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        4 => 
        array (
          'nom' => 'Unité',
          'champ' => 'id_prestations_unite',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'prestations_unites',
          'explication' => '',
          'saisie_options' => '',
        ),
        5 => 
        array (
          'nom' => 'TVA',
          'champ' => 'taxe',
          'sql' => 'decimal(4,4) not null default 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        6 => 
        array (
          'nom' => 'Objet du parent',
          'champ' => 'objet',
          'sql' => 'varchar(25) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        7 => 
        array (
          'nom' => 'Identifiant du parent',
          'champ' => 'id_objet',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        8 => 
        array (
          'nom' => 'Rang',
          'champ' => 'rang',
          'sql' => 'int(11) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Prestations',
        'titre_objet' => 'Prestation',
        'info_aucun_objet' => 'Aucune prestation',
        'info_1_objet' => 'Une prestation',
        'info_nb_objets' => '@nb@ prestations',
        'icone_creer_objet' => 'Créer une prestation',
        'icone_modifier_objet' => 'Modifier cette prestation',
        'titre_logo_objet' => 'Logo de cette prestation',
        'titre_langue_objet' => 'Langue de cette prestation',
        'texte_definir_comme_traduction_objet' => 'Cette prestation est une traduction de la prestation numéro :',
        'titre_\\objets_lies_objet' => 'Liés à cette prestation',
        'titre_objets_rubrique' => 'Prestations de la rubrique',
        'info_objets_auteur' => 'Les prestations de cet auteur',
        'retirer_lien_objet' => 'Retirer cette prestation',
        'retirer_tous_liens_objets' => 'Retirer toutes les prestations',
        'ajouter_lien_objet' => 'Ajouter cette prestation',
        'texte_ajouter_objet' => 'Ajouter une prestation',
        'texte_creer_associer_objet' => 'Créer et associer une prestation',
        'texte_changer_statut_objet' => 'Cette prestation est :',
        'supprimer_objet' => 'Supprimer cette prestation',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de cette prestation ?',
      ),
      'liaison_directe' => '',
      'table_liens' => '',
      'afficher_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'saisies' => 
      array (
        0 => 'objets',
      ),
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
    ),
    1 => 
    array (
      'nom' => 'Types de prestation',
      'nom_singulier' => 'Type de prestation',
      'genre' => 'masculin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => '',
      'table' => 'spip_prestations_types',
      'cle_primaire' => 'id_prestations_type',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'prestations_type',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Prix unitaire HT',
          'champ' => 'prix_unitaire_ht',
          'sql' => 'decimal(20,6) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Unité',
          'champ' => 'id_prestations_unite',
          'sql' => 'bigint(21) NOT NULL DEFAULT 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'prestations_unites',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Taxe',
          'champ' => 'taxe',
          'sql' => 'decimal(4,4) not null default 0',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => 'titre',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Types de prestation',
        'titre_objet' => 'Type de prestation',
        'info_aucun_objet' => 'Aucun type de prestation',
        'info_1_objet' => 'Un type de prestation',
        'info_nb_objets' => '@nb@ types de prestation',
        'icone_creer_objet' => 'Créer un type de prestation',
        'icone_modifier_objet' => 'Modifier ce type de prestation',
        'titre_logo_objet' => 'Logo de ce type de prestation',
        'titre_langue_objet' => 'Langue de ce type de prestation',
        'texte_definir_comme_traduction_objet' => 'Ce type de prestation est une traduction du type de prestation numéro :',
        'titre_\\objets_lies_objet' => 'Liés à ce type de prestation',
        'titre_objets_rubrique' => 'Types de prestation de la rubrique',
        'info_objets_auteur' => 'Les types de prestation de cet auteur',
        'retirer_lien_objet' => 'Retirer ce type de prestation',
        'retirer_tous_liens_objets' => 'Retirer tous les types de prestation',
        'ajouter_lien_objet' => 'Ajouter ce type de prestation',
        'texte_ajouter_objet' => 'Ajouter un type de prestation',
        'texte_creer_associer_objet' => 'Créer et associer un type de prestation',
        'texte_changer_statut_objet' => 'Ce type de prestation est :',
        'supprimer_objet' => 'Supprimer ce type de prestation',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de ce type de prestation ?',
      ),
      'liaison_directe' => '',
      'table_liens' => '',
      'afficher_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => 'administrateur',
        'objet_voir' => '',
        'objet_modifier' => 'administrateur',
        'objet_supprimer' => 'administrateur',
        'associerobjet' => 'administrateur',
      ),
    ),
    2 => 
    array (
      'nom' => 'Unités',
      'nom_singulier' => 'Unité',
      'genre' => 'feminin',
      'logo' => 
      array (
        0 => '',
        32 => '',
        24 => '',
        16 => '',
        12 => '',
      ),
      'logo_variantes' => '',
      'table' => 'spip_prestations_unites',
      'cle_primaire' => 'id_prestations_unite',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'prestations_unite',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Titre',
          'champ' => 'titre',
          'sql' => 'text NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => '',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Unités',
        'titre_objet' => 'Unité',
        'info_aucun_objet' => 'Aucune unité',
        'info_1_objet' => 'Une unité',
        'info_nb_objets' => '@nb@ unités',
        'icone_creer_objet' => 'Créer une unité',
        'icone_modifier_objet' => 'Modifier cette unité',
        'titre_logo_objet' => 'Logo de cette unité',
        'titre_langue_objet' => 'Langue de cette unité',
        'texte_definir_comme_traduction_objet' => 'Cette unité est une traduction de la unité numéro :',
        'titre_\\objets_lies_objet' => 'Liés à cette unité',
        'titre_objets_rubrique' => 'Unités de la rubrique',
        'info_objets_auteur' => 'Les unités de cet auteur',
        'retirer_lien_objet' => 'Retirer cette unité',
        'retirer_tous_liens_objets' => 'Retirer toutes les unités',
        'ajouter_lien_objet' => 'Ajouter cette unité',
        'texte_ajouter_objet' => 'Ajouter une unité',
        'texte_creer_associer_objet' => 'Créer et associer une unité',
        'texte_changer_statut_objet' => 'Cette unité est :',
        'supprimer_objet' => 'Supprimer cette unité',
        'confirmer_supprimer_objet' => 'Confirmez-vous la suppression de cette unité ?',
      ),
      'liaison_directe' => '',
      'table_liens' => '',
      'afficher_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'autorisations' => 
      array (
        'objet_creer' => 'administrateur',
        'objet_voir' => '',
        'objet_modifier' => 'administrateur',
        'objet_supprimer' => 'administrateur',
        'associerobjet' => 'administrateur',
      ),
    ),
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAYAAACtWK6eAAAABHNCSVQICAgIfAhkiAAAD3RJREFUeJzt3XewHWUZx/HvvWmkmRCaRNAgCGiQGoKKIiAiKoLiiKKIXcQCFppDEQujiCOCIlhQREFRrNgLdhkEAZEOkpACMZAEEiCShHv848kZ7t19dvfdd9+z5+L8PjP7x71z9t3nPWfL2xdERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERETkiW2g3wFkTAK2BjYGJgMTgQeA1cA8YHH/QuuJqcDTgU2w/I4HVmD5vQu4t3+h9cQ0LL8bAVPW/+8B4EEsr0sapj8FOArYB9gA+DNwDrCsYbp983QsQ98D7gaGgE7J9jDwF+BMYD9gbPshN7I18AHgB9jFXpbXDrAK+CPwSWBvYEzrETezPXAc8GPsAqjK73+AXwEfA3areazNgFucNBcD2zXMR6smAu8E/kr1BVG1LQPOBZ7Vag7qmYrdBK6lWV47wFLgLOAZreagnunA+4EbaZ7f+cCngJkVxxyL3UiK0vl7orz11CTgBOwx2vSLy25DwOXAjq3lptoU4BSsCJE6v48Bl2F36NFiOnA68BDp8/so8GVgsODYHw9I49kJ85rcQdjdIPUX550452Nl3X46lLAiRdNtLfZEmdxOtgq9Cbif3ub1+IJj74f97lX7vywmY72upE8GzgPeGPDZecDVwG3AIqziNrQ+jelYOXI2MBeYUJHWYuBw4A8xQTcwHbgAOCTgs3cA/8Dyew+PP2mmADOAbYEdgN2BcRVpzQMOA66KijreJsBFwAEBn70FuA64FatrrFj//+nrt+2xu/zO5PP7C+Dl2Pcz3JOB67H6R5XZwM0Bn2vNM7CAyq7q67Dy6qwa6U7EfpBvUv44Xwsc2zwbwXbATvqy/F4FvAfYoka6k4FXAN/BWrfKiiHvSpCPUHOABSXxDGENKu8ENq+R7jTgNcD3sd9wEdaqmTUI/Lbk+MO3v9XKWQt2xe4SRQH/CtgrwXFmAKdhd6OiY51Dcdk1ledXxPATYI8Ex9kUa9FaVXKsT9D7ksFLSmIYwlold0lwnC2xG4/n1ILjZ7fVjK66KbtixSMv2HnY3TC1TYFvFByzg1XwenXSvAB4pOC4t2Fl5NS2wE7Covye2YNjdh2APa284/4L+z56bS9gXUEM2e0tLcQTbBuKW6m+Czypx8c/hOI7+ek9ON6zS473dazlrpeOoLiYWVSpbWKPkuN9geq6YQqbYMWukIvjOy3EE2wycBN+oCe3GMe22JPKi+PQhMfZEPi3c4zHgGMSHqfKTlgl34sjpPIcajP8zs11wNsSHqfMAFZcDbk4bqf3N+RaLsQP9L19iGUmVrzJxrIS681O4YdO+o9hd/W2bYWNRMjGcz/VnWwhBoDfOemvJazFLpUTnBi8bTVp6kDJHIgf6Cl9jGkW/h3vCprXR17npNsBjm6YbhPPxO+L+HGCtI900h0C3pwg7VBzgTVOHN7WZmtepYnYwDqvztFvz8X/Ut/UIM2p+PWsrzaKNI398TvNXtkgzY3xRwN8tlGk9WxIcbF5NJ53IxxLPsj59L83u+vD+PHFVihPc9K7md5XyEN9Cj++2IGOZznpXYONPG7DAH5x1tvuZPScd4ANKfYqiAf1M6iMcVjzYzbGoyLSehJWj8kWNfZOEWgiRU/010ektRnw30w667BOwrZ8gLCLYw3wnBbjCvJW8oH+pq8R+fYnH+dN1K+LHO2kc1m6MJN5Dfk4r4xI5xQnna8lijHEHIr7XLJbP+t/hbwhxnv3M6ASV5GPtW6nljeEe9eEMaYyiD/Mp06P8gD5lrF1WDN6G6bhN6N72+WMvsl/PI38fI5/Jkx/kLTl+teT/2LPrrH/9s7+f0oYH9i8hlSdbUeRj/djNfbfw9n/p4liC3GJc3xvW4DNUMwaC7y4lUgLvIN8sB9qmOZG2I94E9bG3gGWY8MqXtQw7Unkh8DMq7H/8eTz+/aGMYHdaM7G7pbdG849WL9Sk7b8GeTrD9fX2P908vmt6midSJqh9+92jl233vFJ2h/NPcK3yQfcpBPuQMoH/HWwoQNNniqXOWluGbhvtgd3CBv20MS7yJ/Ew7fHsHFVsS1Qv3bSmx64b7b4vIbH55Fn7YL1L3XHR91IfAfizpSPWh6+HVeQxkuwvD6KNSRFazKne27m7wXYXTDGy4EfUX0ivBZ7yrwU+zHqugJ4deZ/JwA3BOy7Z+bvG4H7ImLoOprqIt4g1ow+DRs2XtcVjCxmDAInYcPyq+ye+fvv2DisrN2woubwG9dsbLj6BVg+HwmMdypwKWEn9c+Bzzj/n4nNURnEmqJ3ov15MkwgP5oytjVnBlaMCrljdLcTI481t+ZxyrYLImMAO4G6RcjQLeaOvF/NY5RtZxUc428V+91I+JoBFwfGshB/jsgY4PeZz7Y1TmyEZ5EPOna07GlOWlXbCuIendMijlW0NRktG1oBHb6FPOWytow4TtHmDd/YKHDfh6kedv62wLTWUtz6ONv5fJ2GiWT2dAKJ6XgD++FjfrCoOcbkO/pitzdEHn9cgxi2qXmsQZqvHNPdvHk8dS/Ai/DrMTtgF1FIGieV5Hdz5/NfLvl8pdiZdlOd/62KSGeA+DWLYpf6iYnTszJyvy3wv78QdfM8hJ14KXj5XUS9xd7eiK07MLw/ZjJW7whpfPkN1jpVpDtYc7hGrWqxF4i3iEBMpXlcQVohYluz1kTulxWTX2jWChezby/z26F+0Xp7rNJ85Pq/P0/Yhb8Eu8CGSj6zFmu9Gq7R4oKxF4jXmhFzpa4hviVoUeR+sXfvrNg7U3fsWoyYPBc1zdZVlN9zsXnwdfK0AbY805WETYkdwlap+U/F5yaRvyAalRhiLxDvcev1Zob4XeR+v4/YZyzpZpnF5ncFtspiXauw4kkdU0k36rYovx1szNb+VJ/AWaGDCz9B2Hkyw/lfXy6Qhc7/YpfDPC9in59Rrxe8ayvii3RZTZb/jMnzBVjHVx0px01V5fe32ODC1MNv/kR4S5SX37ubHDy2fLYUuxNuOOx/syPT+jPWuhE6VXUl8UNavBiX4hcZs2Yx8oYSm1+wYSRvxpYLCnE3trxmXV6MSwjruNuKkQMAQ/K7CNgXe6KcTPPFtu/DxtBl6xVFvBhvaxhDtDpDEapsgD0Vqpr4HqTZMjqfc9IMXa/qmsx+K2n2NNoUW1mxKs8LKV4bqsrXnPRC1/O9PbPfEuqNmN0Hf65Q6DZE/SWimgwlSu6jTjBN1rwaA3wQf151d3HqpkWG7BDwhwk/yc924mq6+N0krNnS6wNYh53gIctqegaxi2t4mksJP8kvdGLauWYMmwK/dNIJ2cqacz3jyY/IuLNmGkm9kHymUqxDNB4bP3QM1il0BGnuAruSj7fO8G1vYYovJYgLrIXoYOwGcSK2KIQ3lKKOvcnHe0mN/d/g7B+zIN0A9luGLrjQwZqB6z6dD3bSSfX7RBlD/hG6muYjXHvli+S/wDqr7k0gP1z+AdI1G6f2LfL5zQ7ULDON/Ejje4kfHbsXYQu+LafeWs1dlztp9XU+CNhoymxQvVjBsKnNyQ+hfojwod9dXyWf3xPShZnM1uQHQy7D5mvU4ZXpY4cUgT0Vy+qaQ8StwLIT+SE1ixgFb+Talvyo3pWkWawspfPI/xjnR6Szs5POUvz2937yRsWeEZGOV4xeSLPOx26Ry5trHruU0E+dtE5tEGNSl5IP7tt9jWik3cmvFbWW+Ap/dhJS7MXWK/uQv5uupt4rF4bz5vJ/unmY7M7I1VeuJq5T06t7PMjILoi+2gF/bsNh/QxqvSn4L3b8SoM055A/AYewSV/9tiH+m7y8iUWh9nXSW4c9XZqahi309gD2Qte6noz/Jq+PJogtqc+SD3IlzTrTmhrAn3exnHovdPF83Un3fuJ+5FTG4FdU76X53fQHTrqLgac0TBfsd4oZlTAemzGZjesu6te1em4q/p1rIf3rqDnDiadDmvdFzMBffvQOrO2/H8534ulQr+WqyEz89QJuoD9FmQH8VrohbDr2qDQXv+I1j/bWUgL78k5z4uhg9aVUXoT/EpebgacmPE6VMfhN2E2LklkH40++uoF2G2XG4rcmNi1KtuJ9+IEvIc0r16pMoXg6682k77M4ueBYC8gvatEL07HV270YrqHhih6OMwuOdSftvOZsE/xGkg42qDHVQNSeKirarAM+Qu8WPZ6LXyHvnrC9uKsPYL213jEfxZalaTRhp8QL8Nfg7WDjqGKHqJQZpPgG9Aj2LphevQ9yf4o7Gv/FKGq1qjKAzRLzMtLBXgN8IOmWipyJlb+L3pW9gPhpvSHGUPzyoA622mTKHt2nYe9jLJprfgdxPdGhxuF3IHa3q0n7nsJt8LsSuttNpGksaN0plC8YcC32no7YYs8crAOwbOG1tuoDAxQXP7rbldjQ7dgpt8/DBi+WjWm6Fmv67LUxFDcKdLc/YotoxxTzBrAi+cWUv6zzL4y+TtpaXkX1aokPYeXoo7EX3ngZHodV9A/BXuvsvV4tu32X9sdJHU75u9s7WCfW97H3pc/FH+4yHntj1KFYBTxkEecLaf8dJUdSfoPqYL//pes/Owd/RucErD/tMKzIOr8izQ5WSmnjpaE9NwtbjaIqw9mL5l6sBWw5xcUnb1tOf1/9ux02CaxOfldhAz/nY/HXWapnKf3tlN0RK1bVye9KLL93U3/RwMXYjff/zmsJf41WzLYWWwOpjSJGlUFsYevsfIyU26PYXbTpsPgUxmIV9KJXgKfYVmPNuKN19HQS47Cppt57NmK3h7C6SJM54r0yETtxbiVdfh/Eipmz2stGsCnYnJbQ93qEbCuwC6Pp6IcnnDnYeq9FTbNVF8VPsIut7pD1fhjAxi6dQ9xTdCU23ONwnhh30EFsavR5xD1Fl2EtZbGV/GRGy1t5ZmJzw7fDKuPTsRNhAvZoXc7jq8f/A2s6jV24bTTYAmuV2habu7ExVsEej/UnLMPyexdWvr+B8IULRqNZ2BI/3fxuxONrWK3CbgDzscaX67H8li0QJyIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIhLof21wJMDRPxuEAAAAAElFTkSuQmCC',
        ),
      ),
    ),
    'objets' => 
    array (
      0 => 
      array (
        'logo' => 
        array (
          0 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAYAAACtWK6eAAAABHNCSVQICAgIfAhkiAAAD3RJREFUeJzt3XewHWUZx/HvvWmkmRCaRNAgCGiQGoKKIiAiKoLiiKKIXcQCFppDEQujiCOCIlhQREFRrNgLdhkEAZEOkpACMZAEEiCShHv848kZ7t19dvfdd9+z5+L8PjP7x71z9t3nPWfL2xdERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERERETkiW2g3wFkTAK2BjYGJgMTgQeA1cA8YHH/QuuJqcDTgU2w/I4HVmD5vQu4t3+h9cQ0LL8bAVPW/+8B4EEsr0sapj8FOArYB9gA+DNwDrCsYbp983QsQ98D7gaGgE7J9jDwF+BMYD9gbPshN7I18AHgB9jFXpbXDrAK+CPwSWBvYEzrETezPXAc8GPsAqjK73+AXwEfA3areazNgFucNBcD2zXMR6smAu8E/kr1BVG1LQPOBZ7Vag7qmYrdBK6lWV47wFLgLOAZreagnunA+4EbaZ7f+cCngJkVxxyL3UiK0vl7orz11CTgBOwx2vSLy25DwOXAjq3lptoU4BSsCJE6v48Bl2F36NFiOnA68BDp8/so8GVgsODYHw9I49kJ85rcQdjdIPUX550452Nl3X46lLAiRdNtLfZEmdxOtgq9Cbif3ub1+IJj74f97lX7vywmY72upE8GzgPeGPDZecDVwG3AIqziNrQ+jelYOXI2MBeYUJHWYuBw4A8xQTcwHbgAOCTgs3cA/8Dyew+PP2mmADOAbYEdgN2BcRVpzQMOA66KijreJsBFwAEBn70FuA64FatrrFj//+nrt+2xu/zO5PP7C+Dl2Pcz3JOB67H6R5XZwM0Bn2vNM7CAyq7q67Dy6qwa6U7EfpBvUv44Xwsc2zwbwXbATvqy/F4FvAfYoka6k4FXAN/BWrfKiiHvSpCPUHOABSXxDGENKu8ENq+R7jTgNcD3sd9wEdaqmTUI/Lbk+MO3v9XKWQt2xe4SRQH/CtgrwXFmAKdhd6OiY51Dcdk1ledXxPATYI8Ex9kUa9FaVXKsT9D7ksFLSmIYwlol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',
          ),
          32 => 
          array (
            'extension' => 'png',
            'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4gMSFSMUlHY3agAAAiJJREFUWMPt1UuIjXEYBvBfE5OmSZMiFi5lI0kksZNCqAkLyUqkKCWlyEI2ipVkITZWItQUNhYoMsgtSS5FxuRaInJnZmyeU/8+5zA7FufZfOf7v9/7/N/L876HJppooon/FMOxDGfxEDewE+MH4duO+TiN+7iFPRgX+wL0YGUjgrl4jH68w1O8xg/8DFlLA9/ZCbgfH9CLF/G9mMTeYADX6hEsxjc8wYqKbQq64nyyju9MfMnFGyq2SXlejX8/llYJRuA57iTSRtgSkrWV82f4iFEN/PbGbwBH632wJgQj/9LjVtzFo/Qb1ierKQ18ZqV9A2lBa/WDITiC7opxNd6nj3MqVfiKJSE/UxDvxrGiEu0RXa30M3EKE8sARkaxBwqBteeSWtnuYVhs8/G9sA3gXGy19558d7A425VJ+olOxWWtaEuEZalLtQ9NpYSgih95duU5PpnWtHIJ2zAh7y3Vvl6ISssWbA/xp8pUbEx1lqc15zOqbSHegr4i87dFyZcm0anVDHZlBEcMQoRXovqOnG0O6djiuxkJsj8B17I+nP3y27RMjkP3HxYNrArppuKsLSJ8ld/V8athXiqzvxH55pTsdtRdXc/7cvmVOkF2Rhu9WFSxtWBrEnxQVK4u1uFzIn2Gm1mvnxPc8WL+q1iIl/HtxWVcjz76skFHD+bPaAx2ZNl8CMEJTC8moRE6sj8uJoiHOIRp9RZQE0000cQ/xy9UaJkprJJltAAAAABJRU5ErkJggg==',
          ),
        ),
      ),
      1 => 
      array (
      ),
      2 => 
      array (
      ),
    ),
  ),
);
