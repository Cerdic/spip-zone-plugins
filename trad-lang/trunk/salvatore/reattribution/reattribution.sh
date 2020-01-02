#!/bin/sh
# Script de réattribution de commits de salvatore

if [ -r ../salvatore_id.sh ];then
	. ../salvatore_id.sh
fi

if [ -z $SALVATORE_USER ] || [ -z $SALVATORE_PASS ]; then
	printf "Vérifier les identifiants de Salvatore\n\n";
	kill "$$";
	exit 1
fi 

if [ -z $SVN_SERVEUR ]; then
SVN_SERVEUR="svn://zone.spip.org/spip-zone/"
printf "On commit sur le serveur $SVN_SERVEUR\n\n"
fi

# On a besoin d'un fichier reattribution.txt
if [ ! -r ./reattribution.txt ];then
	printf "Le fichier source de réattribution n'est pas là\n\n";
	kill "$$";
	exit 1
fi

FICHIER=./reattribution.txt

# On lit le fichier de réattribution ligne par ligne pour en sortir numéro de commit et auteur
while read commit mail
do
if [ -z $commit ]; then
# pas de commit? on considère comme vide
printf "Ligne vide\n"
else

COMMITEUR=$(env LC_MESSAGES=en_US.UTF-8  svn info -r $commit $SVN_SERVEUR |awk '/^Last Changed Author:/ { print $4 }')
if [ "$COMMITEUR" = "$mail" ]; then

printf "====
Commit : $commit\n\
Mail : $mail\n\
$COMMITEUR est déjà auteur\n\n"

# Le commiteur est différent, on modifie la propriété svn:author
else
printf "====
Commit : $commit\n\
Mail : $mail\n"
svn propset --revprop -r $commit svn:author $mail $SVN_SERVEUR --username $SALVATORE_USER --password $SALVATORE_PASS --no-auth-cache --non-interactive
fi
fi
done < ./reattribution.txt
exit 1