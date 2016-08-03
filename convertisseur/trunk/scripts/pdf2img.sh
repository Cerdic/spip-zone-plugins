#!/bin/sh

# Conversion d'un PDF en images
pdf="$1"
nom=${pdf%.*}
nom=${nom##*/}
nom=${nom/\//}

dest=${2-0} # 0 par défaut
shave="${3-0}"

# cas d'un pdf multipages avec format des pages indiqué en dest : pdf_%02d.jpg
regex="\.jpg$"

if [[ "$dest" =~ $regex ]] ; then 
	fichier_dest="$dest"
	repdest=${dest%/*}
	format=" ($repdest)"
fi
[[ "$dest" =~ $regex ]] || fichier_dest="$dest/$nom.jpg"

#Si pas de dest
if [[ $dest == 0 ]] ; then
	fichier_dest=${pdf/.pdf/.jpg}
	repdest=${pdf%/*}
	format=" ($repdest)"
fi

echo "Conversion (shave $shave) de $pdf dans $fichier_dest $format"

#exit

# infos sur le pdf : identify -format "%[pdf:HiResBoundingBox]" testpdf.pdf
# Multipages ? "$2/$nom-%04d.jpg"
# rogner : -shave 40x40
# redimensionner : -resize 1500

nb_pages=$(pdfinfo "$pdf" | grep Pages | awk '{print $2}')

# une seule page
convert -verbose -colorspace RGB -resize 1500 -interlace none -density 300 -background white -alpha remove -quality 80 -shave "$shave" "$pdf" "$fichier_dest"

# si on a affaire a un fichier multi pages on renumérote +1 pour ne pas démarrer à 0
find "$repdest" -iname "*0.jpg" | while read f ; do
	rep="${f%/*}"
	# lister les fichiers à l'envers et les décaler de 1
	find "$repdest" -iname "*.jpg" | sort -r | while read i ; do
		page=$(echo "$i" | grep -Eo "\d+\.jpg" | grep -Eo "\d+")
		pagep=$(echo $page | sed 's/^0//')
		pagep=$((pagep+1))
		((pagep<10)) && pagep="0$pagep"
		image=$(echo $i | sed "s/${page}.jpg/${pagep}.jpg/")
		mv "$i" "$image"
	done
done
