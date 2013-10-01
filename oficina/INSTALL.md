# Installer le plugin oficina

Télécharger le plugin

    svn checkout svn://zone.spip.org/spip-zone/_plugins_/oficina

et l'activer.

Installer `unoconv`. Sur Debian ou Ubuntu :

    sudo aptitude install unoconv

Configurer une régle de réécriture, en ajoutant la ligne suivante dans `.htaccess`

    RewriteRule ^(.*)$ spip.php?action=oficina_api_v1&signature=$1 [QSA,L]

# Tester

Aller sur la page principale et se connecter. Une clé sera affichée, disons que c'est `acc997357`.

Pour convertir le fichier http://nanolinenturkey.org/v1/bursaries.docx, aller à

    http://localhost/oficina/?email=user@server.tld&key=acc997357&url=http://nanolinenturkey.org/v1/bursaries.docx

Un formulaire permet de jouer avec les paramètres.

En interne, le fichier docx doit être téléchargé dans le répertoire :

    tmp/oficina/user@server.tld/

et les erreurs apparaissent dans :

    tmp/log/oficina.log

# Erreurs

Il peut y avoir des [problèmes](http://stackoverflow.com/questions/9259975/unoconv-not-working-while-trying-to-convert-throws-error-unable-to-connect-or) de [droits](http://stackoverflow.com/questions/13380340/error-in-unoconv-command-run-as-user?lq=1) produisant les logs suivants :

    Oct 01 20:04:54 127.0.0.1 (pid 11188) :Pub:info: errunoconv --format=html tmp/oficina/user@server.tld/acc997357.docx 2>&1: Error: Unable to connect or start own listener. Aborting.

Dans ce cas, on peut contourner le problème en fermant toute instance de Libreoffice et en lançant :

    unoconv --listener &

