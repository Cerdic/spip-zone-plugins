# Journal des changements

## Version 2.0.2

Refactoring : les identifiants ne sont plus stockés dans une table à part, mais directement dans les tables des contenus en ajoutant un champ `identifiant`.

Ce champ n'est ajouté qu'aux tables configurées, celles possédant nativement ce champ sont ignorées.

**Fonctions ajoutées :**

* `identifiants_lister_tables_identifiables()`
* `identifiants_lister_tables_natives()`
* `identifiants_lister_tables_utiles_manquantes()`
* `identifiants_repertorier_tables_natives()`
* `identifiants_adapter_tables()`
* `identifiants_nettoyer_tables()`
* `inc_identifiants_to_array_dist()` (Itérateur)

**Pipelines ajoutés :**

* `identifiants_pre_edition()`

**Fonctions renommées :**

* `identifiants_utiles()` → `identifiants_lister_utiles()`

**Fonctions supprimées :**

* `maj_identifiant_objet()`
* `tables_avec_identifiant()`

**Fonctions dépréciées :**

* `identifiant_objet()`

**Pipelines supprimés :**

* `formulaire_charger`
* `formulaire_traiter`
* `post_insertion`
* `optimiser_base_disparus`
* `declarer_tables_interfaces`
* `declarer_tables_auxiliaires`