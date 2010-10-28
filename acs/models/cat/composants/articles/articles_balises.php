<?php
/*
             ACS
         (Plugin Spip)
         Squelette Cat
    http://acs.geomaticien.org

Copyright Daniel FAIVRE, 2007-2010
Copyleft: licence GPL - Cf. LICENCES.txt in acs plugin dir
*/

/*
* +----------------------------------+
* Nom du Filtre : "Sommaire Tableau"
* +-------------------------------------+
* Fonctions de ce filtre :
*   Ce filtre permet de generer un sommaire de navigation dans les articles.
*   Le but est d'offrir un moyen simple et automatique pour fabriquer des
*   sommaires de navigation a l'interieur de vos articles, aussi bien sous forme
*   de tableau (option par defaut) que sous forme de liste.
*   En plus du sommaire de navigation, le filtre ajoute donc les ancres et liens
*   necessaires a une navigation aisee au sein de l'article.
*   Une option permet la numerotation automatique du sommaire et des intertitres,
*   une autre permet le masquage ponctuel des intertitres.
* +-------------------------------------+
* Integration de ce filtre dans le squelette :
*   Dans une boucle article, il faut placer ce filtre sur la balise "#TEXTE".
*   Par exemple : [(#TEXTE*|somm_table|propre)]
*   Important : prendre soin a mettre un asterisque "*" apres la balise "#TEXTE"
*   et de terminer l'integration par le filtre "propre", pour retrouver le formatage
*   typographique par defaut de Spip.
* +-------------------------------------+
* Utilisation de ce filtre dans le texte des articles et breves :
*   Il suffit de rediger votre article avec des intertitres
*   Entourez les intertitres des crochets, "[" et "]", pour les masquer.
* +-------------------------------------+
*/

function sommaire($texteOrig, $typeListe = false, $numAuto = true) {
// Fonction pour creer un sommaire sous forme d'une liste ou d'un tableau Spip.
// Si un intertitre existe, alors on analyse le texte fourni pour isoler
// tous les intertitres afin de pouvoir fabriquer le sommaire, avec des liens
// internes vers tous les intertitres et de liens de retour vers le sommaire.
// Le sommaire sera cree et positionne selon les parametres configures dans le composant Articles.

  // Test de l'existence d'intertitres
  $test = preg_match('#\{\{\{(.*?)\}\}\}#i', $texteOrig);

  // Si des intertitres existent, alors on genere le sommaire.
  if ($test) {

    // On isole les textes presents dans les balises "cadre" et "code".
    preg_match_all('#<cadre>(.*?)</cadre>#is', $texteOrig, $listeCadre);
    preg_match_all('#<code>(.*?)</code>#is', $texteOrig, $listeCode);
    // On place les resultats, avec les balises, dans des variables.
    $listeCadreTexte = $listeCadre[0];
    $listeCodeTexte = $listeCode[0];

    // On modifie le format des balises intertitre dans les balises "cadre" pour ne pas les traiter.
    foreach ($listeCadreTexte as $texteCadreOrig) {
      $texteCadreNew = preg_replace('#(\{\{)(\{.*?\})(\}\})#i','$1-$2-$3',$texteCadreOrig);
      $texteOrig = str_replace($texteCadreOrig,$texteCadreNew,$texteOrig);
    };

    // On modifie le format des balises intertitre dans les balises "code" pour ne pas les traiter.
    foreach ($listeCodeTexte as $texteCodeOrig) {
      $texteCodeNew = preg_replace('#(\{\{)(\{.*?\})(\}\})#i','$1-$2-$3',$texteCodeOrig);
      $texteOrig = str_replace($texteCodeOrig,$texteCodeNew,$texteOrig);
    };

    // Recuperation des tous les intertitres presents dans le texte nettoye.
    preg_match_all('#\{\{\{(.*?)\}\}\}#i', $texteOrig, $listeOrig);

    // On place le resultat a utiliser dans une variable.
    $listeTitresOrig = $listeOrig[1];

    // On verifie qu'il y reste des intertitres a traiter.
    if (count($listeTitresOrig) > 0) {

      // On initialise les autres variables.
      $newSomm = '';
      $esp = '&nbsp; &nbsp;';
      $nb = 1;

      // Boucle sur chaque element de la liste des intertitres originaux.
      foreach ($listeTitresOrig as $titreOrig) {
        $masquer = preg_match('#^\[(.*?)\]$#i', $titreOrig); // On test s'il faut masquer.
        $titreClean = rtrim(trim($titreOrig, '[#'),'-]'); // On supprime les eventuels indesirables.
        $titreClean = ucfirst($titreClean); // On met la premiere lettre en majuscule.
        $titreMin = strtolower($titreClean); // On convertit en minuscules.

        // On insere la numerotation automatique si elle est demandee.
        if ($numAuto) { $titreClean = $nb.'. '.$titreClean; };

        // On fabrique la liste ou le tableau et on place les ancres et liens des intertitres.
        if ($typeListe) {
          // On fabrique le sommaire sous forme de liste.
          $newSomm = $newSomm.'- [{{<html>'.$titreClean.'</html>}}->#inter'.$nb.']'."\n";
        } else {
          // On fabrique le sommaire sous forme de tableau.
          $newSomm = $newSomm.'|['.$esp.'{{<html>'.$titreClean.'</html>}}'.$esp.'->#inter'.$nb.']|'."\n";
        };
        // On insere l'ancre et l'intertitre, ou l'ancre seulement s'il faut masquer l'intertitre.
        if ($masquer) {
          $titreNew = '[inter'.$nb.'<-]'."\n";
        } else {
          $titreNew = '[inter'.$nb.'<-]'."\n".'{{{[<html>'.$titreClean.'</html>->#somm]}}}';
        }
        // On remplace les intertitres par d'autres avec une ancre et un lien vers le sommaire.
        $texteOrig = str_replace('{{{'.$titreOrig.'}}}', $titreNew, $texteOrig);
        $nb++;
      };
      // On insere l'ancre et l'intertitre, ou l'ancre seulement s'il faut masquer l'intertitre.
      if ($masquer) {
        $titreSommNew = '[somm<-]'."\n";
      } else {
        $titreSommNew = '[somm<-]<html><div class="titresommaire bsize"></html>'._T('acs:articles_sommaire')."<html></div></html>";
      }
      // On ajoute le nouveau sommaire.au texte original
      $texteOrig = '<html><div class="sommaire"></html>'.$titreSommNew.'<html><div class="nsize"></html>'.$newSomm.'<html></div></div></html>'.$texteOrig;
    };
    // On remet les balises intertitres dans les balises "cadre" et "code" a leur format initial.
    $texteOrig = preg_replace('#\{\{-\{(.*?)\}-\}\}#i','{{{$1}}}',$texteOrig);

    // On efface tous les eventuels intertitres vides.
    $texteOrig = str_replace('{{{}}}', '', $texteOrig);
  };
  // Retour du texte avec le sommaire ou le texte original a defaut.
  return $texteOrig;
}

?>