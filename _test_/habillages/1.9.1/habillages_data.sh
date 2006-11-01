#!/bin/bash
#
# Script de construction d'un repertoire regroupant tous les dossiers
# de la zone qui contiennent un fichier theme.xml.
BASEDIR=/home/franck/spip-zone
BACKUP=/home/franck/habillages/habillages-data

cd $BASEDIR
for i in `find -name 'theme.xml'`; do
mkdir -p $BACKUP/
cp -r `dirname $i` $BACKUP/
done 
exit
