#!/bin/bash


# variantes d'icones par statut
cd 'marqueurs/icones'
for file in *.png;
do
	# variation "publié en ligne"
	convert $file \( +clone -fill White -colorize 100%% -background Black -flatten -morphology Dilate Disk:2 -blur 0x0.5 -alpha Copy \
	    -fill '#91C100' -colorize 100%% \) +swap -composite \
	  '../icones-publie/'$file
			
	# variation "proposé a la publication"	
	convert $file \
		\( -clone 0 -fill '#FF893E' -draw "color 0,0 reset" \) -compose atop -composite \
		\( $file \) -compose screen -composite \
		\( +clone -fill White -colorize 100%% -background Black -flatten -morphology Dilate Disk:1 -blur 0x0.5 -alpha Copy \
		    -fill Black -colorize 100%% \) +swap -composite \
		\( $file -colorspace gray -negate \) -compose multiply -composite \
		'../icones-prop/'$file
	
	# variation "encours"
	convert $file \
		\( -clone 0 -fill '#FFFFFF' -draw "color 0,0 reset" \) -compose atop -composite \
		\( $file \) -compose screen -composite \
		\( +clone -fill White -colorize 100%% -background Black -flatten -morphology Dilate Disk:1 -blur 0x0.5 -alpha Copy \
		    -fill Black -colorize 100%% \) +swap -composite \
		\( $file -colorspace gray -negate \) -compose multiply -composite \
		'../icones-prepa/'$file
done

# variantes de champ de vision par opérateur (public/prive)
cd '../vues/'
for file in *.png;
do
	convert $file -set option:modulate:colorspace hsb -modulate 100,100,20 '../vues-prive/'$file
done


## Utils

## extraction du nom/extension
#filename=$(basename $file)
#extension=${filename##*.}
#filename=${filename%.*}

## ancienne version des cams en couleur pleine - pas très lisible/contrasté
#convert $file \
#	\( -clone 0 -fill '#91C100' -draw "color 0,0 reset" \) -compose atop -composite \
#	\( $file \) -compose screen -composite \
#	'../icones-enligne/'$file

## version "encerclée", un peu crado
#convert -size 24x24 canvas:none -fill red -draw 'circle 12,12 3,12' \
#	\( $file \) -compose atop -composite \
#	'../icones-prepa/'$file
## original