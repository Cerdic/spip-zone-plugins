#!/bin/sh
# Script retrouvant le traducteur de chaque commit fait au nom de Salvatore (ou autre).
# La derniere passe repose sur un fichier trad-mails.txt donnant leur mail,
# chacune de ses lignes devant etre de la forme: nom%mail

PREFIX=/tmp/ana-svn-${PWD##*/}
USURPATEUR=salvatore
RM="wc -l"
ETAPE=1

while getopts "de:" arg 
do
	if [ $arg == 'd' ]
	then RM="rm"
	elif [ $arg == 'e' ]
	then ETAPE=$((OPTARG+0))
	fi
done

if [ $ETAPE -eq 1 ]
then
echo $ETAPE. Recuperer les logs >&2
SIZE=$(svn up|tr -d "."|cut -d' ' -f3)
if [ -n "$SIZE" ]
then OPT=" -l$SIZE"
else OPT=''
fi
svn log -v $OPT > $PREFIX-1.txt
ETAPE=$((ETAPE+1))
fi

if [ $ETAPE -eq 2 ]
then
echo $ETAPE. Ne prendre que les envois de $USURPATEUR >&2
awk "{if ( /^r/ ) ok=(/^r[0-9]+ [|] $USURPATEUR@/); if (ok) print;}" $PREFIX-1.txt > $PREFIX-2.txt
ETAPE=$((ETAPE+1))
fi

if [ $ETAPE -eq 3 ]
then
echo $ETAPE. Mettre sur une seule ligne le numéro de commit et les fichiers  >&2
awk -F'[/ ]*' '{if (!$0) ok= 0; else {if (!ok) {if  ($0 == "Changed paths:") {print p; p=old; ok=1;} else old=$1} else p= p " " $NF;}}' $PREFIX-2.txt  > $PREFIX-3.txt
ETAPE=$((ETAPE+1))
fi

if [ $ETAPE -eq 4 ]
then
echo $ETAPE. Prendre les commit ayant le fichier XML et au moins un fichier php >&2
awk '/xml.*php/{print substr($1,2)}' $PREFIX-3.txt  > $PREFIX-4.txt 
ETAPE=$((ETAPE+1))
fi

if [ $ETAPE -eq 5 ]
then
echo "$ETAPE. Trouver les noms des traducteurs (eliminer Salvatore et autres infos)" >&2
cat $PREFIX-4.txt | while read i
do
	t=$(svn diff -c$i | 
	grep '^+.*<traducteur' |
	sed 's/^.*nom="//;s/".*$//'|
	grep -iv $USURPATEUR |
	sort -u |
	tr "\n" "%");
	echo "$i $t"
done > $PREFIX-5.txt
ETAPE=$((ETAPE+1))
fi

if [ $ETAPE -eq 6 ]
then
echo $ETAPE. Ne prendre que les commmit avec un seul traducteur >&2
awk -F% '{if (NF == 2) print $1}' $PREFIX-5.txt > $PREFIX-6.txt 
ETAPE=$((ETAPE+1))
fi

if [ $ETAPE -eq 7 ]
then
echo $ETAPE. Prendre leur mail >&2
if [ -f trad-mails.txt ]
then 
	cat $PREFIX-6.txt | while read numero traducteur
	do
			mail=$(grep "^$traducteur%" trad-mails.txt| awk -F% '{print $2}')
			if [ -n "$mail" ]
			then 
			    echo "$numero $mail"
			fi
	done
else
	echo trad-mails.txt absent >&2
fi
fi
$RM $PREFIX-?.txt
