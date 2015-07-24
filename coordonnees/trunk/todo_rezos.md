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
[ ] type_rezo_dailymotion.jpg
[ ] type_rezo_facebook.jpg
[ ] type_rezo_google+.jpg
[ ] type_rezo_instagram.jpg
[ ] type_rezo_linkedin.jpg
[ ] type_rezo_seenthis.jpg
[ ] type_rezo_twitter.jpg
[ ] type_rezo_viadeo.jpg
[ ] type_rezo_youtube.jpg

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

- intégrer les microformats afin de pouvoir créer automatiquement les liens "ajouter comme ami" ou "suivre sur facebook", etc.
