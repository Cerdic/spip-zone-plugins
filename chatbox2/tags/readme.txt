
Plugin de chatbox priv�es auteur 2 auteur connect�


INSTALLATION

Fonctionne avec les blocs d�pliables, option cookie activ�e
#BLOC_TITRE{314} <i class="fas fa-comments"></i>
#BLOC_DEBUT
<INCLURE{fond=fragment_chatbox2}>
<div class="blocs_title">Chatbox Show||Chatox Hide</div>
#BLOC_FIN

Les messages sont enregist� dans la table spip_messages, cela changera peut-�tre, ou pas...

TODO

- Passer les boites en open new window individuelle pour �viter qu"elles tournent toutes en fond
- Pouvoir attacher un document � un message spip en premi�re intention (avant qu'il ne soit enregistr�) mais il faudrait d�bugger l'organiseur priv� qui le propose alors que cela ne fonctionne pas. Il y a un ticket � ce sujet d�pos� par BB.
- Gestion de chatbox par groupe d'auteurs