Ceci est un fichier de démonstration a réutiliser.

Un unique fichier pour récupérer les différentes données proposées dans les pages de http://www.bousai.ne.jp/eng/ qui mesure toutes les 10 minutes la radioactivité au Japon.

Il reste à afficher les données sur une carte… 

Merci à Fil qui a proposé le code de départ suivant

#CACHE{0}
#HTTP_HEADER{Content-Type: text/plain; charset=#CHARSET}
<?php

include_spip('inc/distant');

if ($a = recuperer_page('http://www.bousai.ne.jp/eng/speedi/pref.php?id=01')
AND $b = extraire_balises($a, 'table')
AND $b = extraire_balise($b[2], 'table')
AND $c = extraire_balises($b, 'tr')
) {
        foreach(array_slice($c,3) as $d) {
                $d = array_map('supprimer_tags', extraire_balises($d, 'td'));
                echo join("\t", $d)."\n";
        }
} 


Anne-lise Martenot contact@elastick.net
