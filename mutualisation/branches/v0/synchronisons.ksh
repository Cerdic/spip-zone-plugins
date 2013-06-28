#/!bin/bash
set -x

REPERTOIRE_SPIP_SOURCE="/home/ben/spip/" 
REPERTOIRE_SPIP_CIBLE="bennybox/spip/" 

FICHIER_MOT_DE_PASSE_RSYNC="/home/ben/pwd.txt"
USER_RSYNC=bennybenny
ADRESSE_RSYNC=343.343.34.43  ## 

REPERTOIRE_MYSQL_SOURCE="/home/ben/mysql" 
REPERTOIRE_MYSQL_CIBLE="bennybox/sql/" 
#JOUR_SEMAINE=OUI  ## les sauvegardes se trouvent dans un sous repertoire contenant le jour de la semaine 
#MASQUE_BASE="sc*"      ## ne sauvegarde que les bases qui commencent par sc par exemple  

if [ -f ./mes_variables_persos.ksh ]
then
. ./mes_variables_persos.ksh
fi 

##SYNCHRO DE SPIP 
cd ${REPERTOIRE_SPIP_SOURCE}
rsync -aztv \
--delete \
--exclude "*/tmp/*" --exclude "*/local/*" \
--password-file=$FICHIER_MOT_DE_PASSE_RSYNC  \
* \
rsync://${USER_RSYNC}@${ADRESSE_RSYNC}/${REPERTOIRE_SPIP_CIBLE} 

if [ -z "${JOUR_SEMAINE}" ]
then
  echo 'une seule sauvegarde'
else
  JOUR=`date +%w`
  echo "Sauvegarde tournante sur 7 jours, on envoie le jour $JOUR"
  REPERTOIRE_MYSQL_SOURCE="${REPERTOIRE_MYSQL_SOURCE}${JOUR}/" 
fi


if [ -z "${MASQUE_BASE}" ]
then
  echo 'on prend toutes les bases'
  MASQUE_BASE="*"
fi


cd ${REPERTOIRE_MYSQL_SOURCE}
##SYNCHRO DE MYSQL 
rsync -aztv \
--delete \
--password-file=$FICHIER_MOT_DE_PASSE_RSYNC  \
${MASQUE_BASE} \
rsync://${USER_RSYNC}@${ADRESSE_RSYNC}/${REPERTOIRE_MYSQL_CIBLE} 

