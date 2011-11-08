#!/bin/sh

# Script prenant en argument un nom N et une liste de fichiers,
# et remplacant dans ces fichiers les expressions PHP de la forme
# _L('C\'est une cha&icirc;ne')
# par
# _T('N:c_est_une_chaine')
# et produit un fichier de langue N.php contenant
# array( 'c_est_une_chaine' => 'C\'est une cha&icirc;ne' )

# Limitations:
# 1. il ne doit y avoir qu'un seul _L par ligne
# 2. l'argument de _L ne doit pas contenir les signes ) et |

echo "Ce script Shell est un prototype d'un service fourni par Langonet v 0.7:"
echo "http://www.spip-contrib.net/LangOnet-Presentation-generale"
echo "Utiliser plutot Langonet, qui traite beaucoup plus de cas"
echo "et produit des items plus intuitifs et ergonomiques."
echo

if [ $# -lt 2 ]
then echo usage: $0 nom file1 file2 ... fileN
exit 1
fi
e=/tmp/e.sed
c=/tmp/c.sed
f=/tmp/l.txt
t=/tmp/n.txt
dest=$1
shift
# Extraire les libelles
grep -h "_L *('" $* | \
	sed "s/^.*_L *('/'/;s/').*$/'/g" | \
	sort -u > $f
# Reperer les entites HTML
# construire le sed-script remplacant les entites par leur premiere lettre:
# &eacute --> e etc mais on traite a part &nbsp;
echo 's/&nbsp;/_/g'  > $e
tr " " "\n" < $f | \
	grep '&'| \
	sed -e 's/^.*\&/\&/g;s/;.*$/;/' | \
	sort -u | \
	sed 's/^&\(.\)\(.*\)$/s@\&\1\2@\1@g/' >> $e

# Remplacer par "_"
# les balises, les sequences de non alpha-numeriques et ' si precede de \
echo "s@<[^>]*>@_@g" >> $e
echo "s@[^a-zA-Z0-9'\\]@_@g" >> $e
echo "s@\\\\\\\\'@_@g" >> $e
# Eliminer les repetitions de _
echo "s@\([^_]\)__*\([^_]\)@\1_\2@g" >> $e
# Eliminer les derniers caracteres si non alph-num
echo "s@_'@'@g" >> $e

# Lancer le sed-script obtenu et abandonner les majuscules
sed -f $e $f | tr '[A-Z]' '[a-z]' > $t

# Construire le fichier de langues
cat > $dest.php <<-EOF
<?php

	// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

	\$GLOBALS[\$GLOBALS['idx_lang']] = array(

EOF
paste -d, $t $f |sed "s/',/' => /;s/'$/',/" >> $dest.php
echo '); ?>' >> $dest.php

# Construire le script sed remplacant les _L par _T
sed 's@^\(.*\)$@s|_L(\1)@' $f > $f.tmp
sed "s@^.\(.*\).\$@_T('$dest:\1')|g@" $t > $t.tmp
paste -d'|' $f.tmp $t.tmp  | sed 's/\\/./g'> $c
rm $f.tmp $t.tmp

# Appliquer le sed script sur les fichiers de langues
for i in $*
do
sed -f $c $i > /tmp/x.php
php /tmp/x.php > /dev/null
if [ $? -eq 0 ]
then
	mv /tmp/x.php $i
else	
    echo Fichier $i inexploitable. Revoir ses appels a _L
fi
done

