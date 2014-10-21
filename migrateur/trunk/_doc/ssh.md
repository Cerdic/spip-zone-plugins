
# SSH

En cours de réflexions…

Ce n'est pas très simples pour plusieurs raisons.

1) On ne peut pas transmettre le mot de passe (de façon sécurisée).
   Soit il faut une clé entre serveurs, soit il faut le programme 'sshpass'

2) Il faut que les utilisateurs avec qui on se connecte aient les droits
   corrects sur les répertoires.

   Sinon il faut qu'ils soient sudoers, ce qui entraine encore d'autres
   contraintes (pour rsync entre autres)


## Établir une connexion SSH entre serveur, avec clé

Lorsque le site de distination n'est pas sur le même serveur
que le site source, il est nécessaire – si certaines actions tel que
rsync ou export SQL sont à réaliser – de passer par une connexion SSH.

Cette connexion SSH doit se dérouler sans demande de mot de passe, c'est
à dire par la création d'une clé ssh sur le sur le serveur destination.
Cette clé sera alors à autoriser ensuite sur le serveur source.
Effectivement, autrement, une demande de mot de passe gênerait nos scripts
(rsync & export SQL).

En dehors de cela, ou si vous réalisez ces actions manuellement depuis un
terminal, il y a moins d'utilité (pour le migrateur il s'entend) à créer
cette clé.

Pour créer la clé, voir par exemple 
- http://www.tecmint.com/ssh-passwordless-login-using-ssh-keygen-in-5-easy-steps/

Il est peut être nécessaire de faire redemander la passphrase à ssh :

    `unset SSH_AUTH_SOCK`



## Migrer d'un serveur distant en local sous ubuntu

1) Il faut que apache ait une clé publique en local

    sudo mkdir /var/www/.ssh
    sudo chown -R www-data:nobody /var/www/.ssh/
    sudo -u www-data ssh-keygen -t rsa

2) Il faut ajouter sa clé sur le serveur distant

    Se connecter au serveur avec l'utilisateur désiré et ajouter
    le contenu de /var/www/.ssh/id_rsa.pub (local) dans ~/.ssh/authorized_keys2
    sur 1 seule nouvelle ligne.

Dans les tests, en cas de réponse "Host key verification failed", voir
http://askubuntu.com/questions/45679/ssh-connection-problem-with-host-key-verification-failed-error.

Une solution est d'appeler SSH avec `-o StrictHostKeyChecking=no` systématiquement



## Rsync avec utilisateur sudoer

Rien n'est simple dans ce monde. Si l'utilisateur qui se connecte a des
droits de sudo, mais n'a pas directement accès aux fichiers sans passer
par sudo, alors il faut possiblement suivre cette stratégie :

- http://notepad.bobkmertz.com/2008/04/using-sudo-on-remote-rsync-session-via.html
- ou http://crashingdaily.wordpress.com/2007/06/29/rsync-and-sudo-over-ssh/

Ça consiste à ajouter à la fin de /etc/sudoers (username = nom de l'utilisateur désiré) :

    `username ALL= NOPASSWD:/usr/bin/rsync`

Puis de lancer rsync via :

    `rsync -av -e "ssh" --rsync-path="sudo rsync" user@server.remotehost.com:/source/ /dest`

Ce n'est pas génial mais ça dit que :

- l'utilisateur indiqué peut utiliser rsync en sudo sans taper son password
- puis d'utiliser un chemin spécifique pour le rsync sur le serveur (sudo rsync)
