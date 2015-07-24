# Ajouter un type de coordonnées "réseaux sociaux"

Permet d'ajouter à un objet SPIP un ou plusieurs réseaux sociaux.
Parmi les réseaux sociaux indispensables pour le projet : facebook, twitter
Réseaux sociaux à implémenter en priorité : seenthis, linkedin
Autres réseaux sociaux envisagés : viadeo, instagram, google+

## Méthodologie

- ajouter les 2 tables dans /base :
[x] spip_rezos
[x] spip_rezos_liens

- créer les tables manquantes si besoin
[x] modif de traitement dans coordonnees_administration.php

- ajouter les fichiers d'actions dans /actions :
[x] d'édition, 
[x] d'association, 
[x] de dissociation et 
[x] de suppression

- creer les fonctions :
[x] insert_rezo
[x] revisions_rezos
[x] action_editer_rezo_dist
[x] action_supprimer_rezo_dist
[x] action_associer_rezo_dist
[x] action_dissocier_rezo_dist

- créer les chaines de langue 
[x] coordonnees:ajouter_rezo
[x] coordonnees:ajouter_rezo_court
[x] coordonnees:bouton_dissocier_rezo
[x] coordonnees:info_1_rezo
[x] coordonnees:info_aucun_rezo
[x] coordonnees:info_gauche_rezo
[x] coordonnees:info_nb_rezos
[x] coordonnees:item_nouveau_rezo
[x] coordonnees:label_type_reseau
[x] coordonnees:logo_rezo
[x] coordonnees:modifier_rezo
[x] coordonnees:nouveau_rezo
[x] coordonnees:placeholder_titre_rezo
[x] coordonnees:rezo
[x] coordonnees:rezos
[x] coordonnees:supprimer_rezo
[x] coordonnees:type_rezo_facebook
[x] coordonnees:type_rezo_google+
[x] coordonnees:type_rezo_instagram
[x] coordonnees:type_rezo_linkedin
[x] coordonnees:type_rezo_seenthis
[x] coordonnees:type_rezo_twitter
[x] coordonnees:type_rezo_viadeo

- Ajouter les formulaires
[x] editer_rezo.html
[x] editer_rezo.php

- définir les autorisations
[x] associerrezo
[x] autoriser_rezo_creer_dist
[x] autoriser_rezo_voir_dist
[x] autoriser_rezo_modifier_dist
[x] autoriser_rezo_supprimer_dist
[x] autoriser_associerrezos_dist (pas de souci avec le singulier/pluriel ?)


- Créer les images
[x] rezos-24.png
[x] rezos-16.png
[x] rezos-32.png
balise_img{'rezo:icone_supprimer_rezo'}

- créer les images de réseaux sociaux
[x] type_rezo_dailymotion.png
[x] type_rezo_diaspora.png
[x] type_rezo_facebook.png
[x] type_rezo_flickr.png
[x] type_rezo_google+.png
[x] type_rezo_instagram.png
[x] type_rezo_linkedin.png
[x] type_rezo_pinterest.png
[x] type_rezo_seenthis.png
[x] type_rezo_storify.png
[x] type_rezo_twitter.png
[x] type_rezo_viadeo.png
[x] type_rezo_vimeo.png
[x] type_rezo_weibo.png
[x] type_rezo_youtube.png

- creer les saisies
[x] type_rezo

- creer les inclure
[x] inclure/rezos.html
[x] prive/objets/contenu/rezo.html
[x] prive/objets/infos/rezo.html
[x] prive/objets/listes/rezos.html
[x] prive/objets/listes/rezos_lies.html
[x] prive/squelettes/contenu/utilisations_rezos.html

- définir les class css
[ ] class=rezo (voir prive/objets/infos/rezo.html)
[ ] class=rezos (voir <div class="liste-objets rezos caption-wrap"> dans prive/objets/listes/rezos.html)

- définir dautres trucs 
[ ] logo_type_rezo (voir <div>[(#TYPE|logo_type_rezo) ][(#TYPE|coordonnees_lister_types_rezos)]</div> dans prive/objets/listes/rezos_lies.html)
[ ] coordonnees_lister_types_rezos
[ ] coordonnees_lister_types_rezos (dans la saisie)

- A faire pour plus tard
[ ] intégrer les microformats afin de pouvoir créer automatiquement les liens "ajouter comme ami" ou "suivre sur facebook", etc.

- Bugs
[ ] lorsqu'on ajoute un rezo on a bien les 3 champs : type, titre et rezo; 
    mais lorsqu'on l'édite depius la page ?exec=rezo_edit&id_rezo=2 on a seulement titre et rezo (manque liste déroulante 'type')
    en revanche si édité depuis la page ?exec=organisation alors RAS
[ ] bug identique pour les numéros édités depuis la page ?exec=numero_edit&id_numero=4
[ ] corriger la hierarchie sur les pages ?exec=numero_edit&id_numero=4
    on devrait avoir coordonnées > numéros > le numéro de toto; là il manque 'coordonnees';
[ ] le champ 'rezo' n'est pas obligatoire
