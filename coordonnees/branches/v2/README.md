
# Coordonnées pour SPIP 3
Ce fichier documente certains aspects et fonctions du plugin qui ne sont pas encore abordés dans [la documentation officielle](https://contrib.spip.net/Plugin-Coordonnees).
Ces informations sont valables à partir de la version 2.2.2.


## Typage des liaisons

Les tables de liens des objets gérés par le plugin (`adresse`, `email` et `numero`) possèdent une colonne supplémentaire : `type`.
Ce champ sert à qualifier le lien entre une coordonnée et un objet éditorial : par exemple, une adresse liée à auteur avec un lien typé `home` signifie qu'il s'agit de son adresse personnelle.
Il s'agit d'une clé primaire, il est donc possible de lier plusieurs fois une coordonnée au même un objet éditorial en utilisant des types différents.
**A noter** : ce principe est repris par [le plugin `Rôle` de matthieu Marcillaud](https://contrib.spip.net/Des-roles-sur-des-liens) qui explique en détail la problématique.


### Liste des types de liaisons

Les types proposés par le pugin sont les codes normalisés du [format vCard](https://fr.wikipedia.org/wiki/VCard).
La fonction `coordonnees_lister_types_coordonnees()` retourne la liste complète des types disponibles pour chaque coordonnée, et les chaînes de langue.
On peut éventuellement s'en servir dans les squelettes, mais on préfèrera utiliser les filtres correspondants pour chaque genre de coordonnée :
    [(#EVAL{null}|coordonnees_lister_types_adresses)]
Ce filtre permet également de récupérer la chaîne de langue d'un type en particulier :
    [(#TYPE|coordonnees_lister_types_adresses)]
Les saisies `#SAISIE{type_xxx}` s'en servent de ce filtre pour récupérer les données du sélecteur.
Dans le formulaire d'édition d'une coordonnée, cette saisie n'apparaît que lorsque la paramètre `associer|objet` est donné (cette saisie n'ayant pas de sens si la coordonnée n'est pas liée à un objet).


### Pipeline types_coordonnees

Les autres plugins peuvent compléter (ou altérer) la liste des types en se servant de la pipeline `types_coordonnees`.
Par exemple, le plugin `Commandes` s'en sert pour rajouter aux adresses les 2 types `livraison` et `faturation`.

Dans `paquet.xml` :

    <pipeline nom="types_coordonnees" inclure="commandes_pipelines.php" />

Dans `commandes_pipelines.php` :

    function commandes_types_coordonnees($liste) {
        $types_adresses = $liste['adresse'];
        if (!$types_adresses or !is_array($types_adresses)) $types_adresses = array();
        // on définit les couples types + chaînes de langue à ajouter
        $types_adresses_commandes = array(
            'livraison' => _T('commandes:type_adresse_livraison'),
            'facturation' => _T('commandes:type_adresse_facturation')
        );
        // on les rajoute à la liste des types des adresses
        $liste['adresse'] = array_merge($types_adresses, $types_adresses_commandes);
        return $liste;
    }

### API `editer_liens` et limitations

On peut se servir de [l'API d'édition de liens](https://www.spip.net/fr_article5477.html) pour lier ponctuellement une coordonnée à un objet.
Mais il y a une limitation : l'API ne permet de faire qu'un seul lien d'objet à objet. On ne peut pas lier 2 fois une coordonnée au même objet, avec 2 types de liaison différents.
Ainsi, avec le code suivant :

    include_spip('action/editer_liens');
    objet_associer(array('adresse'=>$id_adresse), array('commande'=>$id_commande), array('type'=>'facturation'));
    objet_associer(array('adresse'=>$id_adresse), array('commande'=>$id_commande), array('type'=>'livraison'));

Le 2ème lien va écraser le premier.
Dans ce cas, il faut procéder sans l'API :

    foreach(array('facturation','livraison') as $type){
        sql_insertq( 'spip_adresses_liens', array(
            'id_adresse' => $id_adresse,
            'objet' => 'commande',
            'id_objet' => $id_commande,
            'type' => $type
            )
        );
    }
