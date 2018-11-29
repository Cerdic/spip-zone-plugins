
Plugin de chatbox privées auteur 2 auteur connecté


INSTALLATION

Fonctionne avec les blocs dépliables, option cookie activée
#BLOC_TITRE{314} <i class="fas fa-comments"></i>
#BLOC_DEBUT
<INCLURE{fond=fragment_chatbox2}>
<div class="blocs_title">Chatbox Show||Chatox Hide</div>
#BLOC_FIN

Les messages sont enregisté dans la table spip_messages, cela changera peut-être, ou pas...

TODO

- Passer les boites en open new window individuelle pour éviter qu"elles tournent toutes en fond
- Pouvoir attacher un document à un message spip en première intention (avant qu'il ne soit enregistré) mais il faudrait débugger l'organiseur privé qui le propose alors que cela ne fonctionne pas. Il y a un ticket à ce sujet déposé par BB.
- Gestion de chatbox par groupe d'auteurs