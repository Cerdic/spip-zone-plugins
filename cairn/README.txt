
{{{Qui est CAIRN.INFO}}}

Cairn.info est né de la volonté de quatre maisons d'édition (Belin, De Boeck, La Découverte et Erès) ayant en charge la publication et la diffusion de revues de sciences humaines et sociales, d’unir leurs efforts pour améliorer leur présence sur l’Internet, et de proposer à d’autres acteurs souhaitant développer une version électronique de leurs publications, les outils techniques et commerciaux développés à cet effet.

http://www.cairn.info/a-propos.php



{{{A quoi sert ce plugin}}}

Une fois installé ce plugin crée une ?page=cairn, qui produit les fichiers XML au format CAIRN pour les x derniers numéros de notre revue.

Le squelette est spécifique à l'organisation de la revue vacarme, mais ils devraient être aisés à personnaliser en fonction de l'organisation de vos données.


{{{À noter}}}


Le fichier cairn.html contient des astuces de programmation.

Ainsi par exemple les lignes suivantes permettent de créer un répertoire <code>cairn/</code> contenant un fichier <code>.htaccess</code> :
<code>
[(#VAL{cairn}|mkdir)]
[(#VAL{cairn/.htaccess}|ecrire_fichier{"Deny from all"})]
</code>


Un peu plus loin dans le squelette on définit le nom du fichier d'export :
<code>
#SET{file,cairn/#GET{numero}/Vacarme#GET{numero}-#PAGE_DEBUT.xml}
</code>

Puis, on calcule un squelette, et on enregistre le résultat dans ce fichier (avec un contrôle d'erreur) :
<code>
[<b>(#GET{file}|ecrire_fichier{
	#INCLURE{fond=cairn/article,id_article,numero=#_meta:TITRE}
}|?{<span style='color:green'>sauvé</span>,<span style='color:red'>erreur</span>})</b>]
</code>

Astuce supplémentaire, on ne fait ce calcul que si le fichier XML n'existe pas encore, en entourant le code précédent par un :
<code>
	[(#GET{file}|file_exists|?{
		OK,
		(ici le code ci-dessus)
	})]
</code>


