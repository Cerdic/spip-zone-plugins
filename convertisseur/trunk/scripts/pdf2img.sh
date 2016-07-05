#!/bin/sh

# Conversion d'un PDF en images
pdf="$1"
nom=${pdf%.*}
nom=${nom##*/}
nom=${nom/\//}

dest=${2-0} # 0 par défaut
shave="${3-0}"

regex="\.jpg$"

[[ "$dest" =~ $regex ]] && fichier_dest="$dest"
[[ "$dest" =~ $regex ]] || fichier_dest="$dest/$nom.jpg"

echo "conversion (shave $shave) de $pdf dans $fichier_dest"

# infos sur le pdf : identify -format "%[pdf:HiResBoundingBox]" testpdf.pdf
# Multipages ? "$2/$nom-%04d.jpg"
# rogner : -shave 40x40
# redimensionner : -resize 1500

nb_pages=$(pdfinfo "$pdf" | grep Pages | awk '{print $2}')

# une seule page
convert -verbose -colorspace RGB -resize 1500 -interlace none -density 300 -background white -alpha remove -quality 80 -shave "$shave" "$pdf" "$fichier_dest"

# si on a affaire a un fichier multi pages on renumérote +1 pour ne pas démarrer à 0
if [[ $nb_pages > 1 ]]
	then
		cd "$dest"
		ls -r| while read f ; do 

			p=$(echo $f | grep -Eo "(\d+).jpg" | grep -Eo "\d+" | sed -e 's/^0//g')
			pp=$((p+1))
			(($pp < 10)) && pp="0$pp"
			#pp=${pp/001/01}
			g=$(echo "${f/-$p.jpg/_$pp.jpg}" | sed -e 's/001/01/g')
			g=$(echo $g | sed -e 's/-0.jpg/_01.jpg/g' )	
			# echo "$f > $p > $pp > $g"
			mv "$f" "$g"
		
		done
fi
