# Commandes d'abonnements

Un début de plugin qui fournit des outils clé-en-main pour permettre à vos visiteurs de commander un abonnement en ligne sans rien avoir à coder !

## Fonctionnement

Le plugin fournit donc un formulaire de commande ergonomique (pas finalisé ya des styles en dur encore) qui se base sur les offres publiées et quelques champs supplémentaires ajoutés (renouvellement auto, montant personnalisable, etc).

Dans chaque offre, on peut dire que le montant est personnalisable, et dans ce cas les visiteurs auront un champ libre en plus pour proposer un montant supérieur.

Le résultat du choix est gardé en session, un peu comme pour un panier mais pas tout à fait pareil (car un seul élément mais avec des infos précises, pas juste l'id). Ensuite le plugin doit revenir à la charge une fois qu'on est sûr que nos visiteurs sont bien inscrits et connectés avec un compte qui a toutes les infos que l'on désire. Donc après l'inscription, ou après l'édition de son profil etc. Cette partie n'est pas terminée et doit être configurable ou doit savoir gérer plusieurs cas (inscription3 ou autre méthode de profil). Pour l'instant ça s'inscrit après les formulaires « editer_auteur », « inscription » et « editer_profil » pour montrer, et ça crée donc la Commande final à partir des infos que l'on avait gardé en session.

## Mise en route

Exemple avec un tunnel de commande composé de 3 étapes :

1. Choix de l'offre d'abonnement
2. Saisie des informations du compte
3. Paiement

### 1. Choix de l'offre d'abonnement

Il suffit d'inclure le formulaire fournit par le plugin en indiquant une redirection vers l'étape suivante.

```
#FORMULAIRE_COMMANDER_ABONNEMENT{#URL_PAGE{commander_compte}}
```
Le choix de l'offre, le montant et le renouvellement sont gardés en session dans la clé `commande_abonnement`.
La commande n'a pas encore été créée.

### 2. Informations du compte

Cette étape permet à l'utilisateur⋅ice de saisir ou mettre à jour les informations de son compte : nom, email, adresses, etc.
Il faut détecter si la personne est connectée, et proposer en fonction soit les formulaires d'inscription, soit un formulaire de connection suivi d'un formulaire pour mettre à jour ses informations.

Cela dépend des méthodes que vous utilisez pour l'inscription et la gestion des informations des utilisateur⋅ices : soit avec les choses de base de SPIP, soit avec des plugins comme Profils ou Inscription3.

Prenons un exemple simple avec les formulaire de login et « editer_auteur » de SPIP.
Cela pourrait ressembler à ça :

```
[(#SESSION{id_auteur}|oui)
<h2>Mettez à jour vos informations</h2>
#FORMULAIRE_EDITER_AUTEUR{#SESSION{id_auteur}, #URL_PAGE{commander_payer}
]

[(#SESSION{id_auteur}|non)
<h2>J'ai déjà un compte :</h2> 
#FORMULAIRE_LOGIN{#SELF}

<h2>M'inscrire :</h2> 
#FORMULAIRE_INSCRIPTION{#URL_PAGE{commander_payer}}
]

```
**nb** : Pour simplifier, cet exemple n'utilise pas [les optimisations possible en place de la balise `#SESSION`](https://contrib.spip.net/4611)


### 3. Paiement

Voilà, la commande a été créée automatiquement à la suite de l'étape 2 : elle comprend le montant personnalisé éventuel, le renouvellement, etc.
Ne reste qu'à appeler le formulaire de paiement avec [Bank](https://plugins.spip.net/Bank) par exemple :

```
#FORMULAIRE_PAYER_ACTE{...}
```