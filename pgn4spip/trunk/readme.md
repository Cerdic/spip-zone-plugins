Le plug-in pgn4spip affiche l'échiquier et la partie de jeu d'échecs
saisie au format PGN à l'intérieur d'un article entre les balises [pgn] et [/pgn]
ou [PGN] et [/PGN].

Usage dans le corps d'un article :

Avant
[pgn] 1. e4 Nf6 [/pgn]
Après

pgn4spip est l'interface pour SPIP de pgn4web de Paolo Casaschi
http://pgn4web.casaschi.net/home.html

Démo en ligne
http://pgn4web.casaschi.net/demo.html

pgn4spip a été développé par Matt Chesstale en PHP, Javascript, HTML, CSS, CFG et SPIP.
matteo.chesstale@gmail.com
Licence : GNU GPL 3.0 (c) 2012

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

En cas d'usage de la lame "Améliorations des textes : Guillemets typographiques" du  plugin Couteau Suisse, pgn4spip doit être activé APRES le Couteau Suisse.

________________

1. Installation du plug-in

Depuis la version 3.0 du plugin pgn4spip s'installe comme tous les plugins, en auto via svp (après avoir créé un dossier plugins/auto/)
Il n'est plus utile d'installer la lib et les 11Mo de pgn4web. Une version allégée de pgn4web est incluse dans le plugin.

Attention : toutes les tailles d'échiquier ne sont plus disponibles.

________________

2. Configuration du plugin

http://monsite.tld//ecrire/?exec=configurer_pgn4spip


La configuration peut être facultativement testée en anglais en ligne dans
http://pgn4web.casaschi.net/board-generator.html

________________

3. Usage avancé

Le plugin peut être optionnellement configuré dans les paramètres de la balise [pgn]
à l'intérieur d'un article. Voir l'aide lors de la configuration du plugin.

doc\pgn4web parameters.txt
http://localhost/spip/plugins/pgn4spip/pgn4web/board.html?help=true

Exemple dans le corps d'un article :

Avant
[pgn height=500 autoplayMode=loop] 1. e4 Nf6 [/pgn]
Après

Fichier texte exemple de test à copier-coller dans un article SPIP 2 ou SPIP 3 :

Test01 Symbol eval.txt : variations, évaluations symboles "avec l'idée de"
Test02 MultiPgn.txt    : plusieurs parties de jeu d'échecs
Test03 Puzzle Fen.txt  : deux problèmes d'échecs
Test04 Puzzle URL      : problèmes d'échecs via l'URL du fichier .pgn
Test05 Puzzle Doc.txt  : problèmes d'échecs dans fichier .pgn attaché à l'article
Test06 Comment.txt     : commentaires dans une partie d'échecs
Test07 Puzzle Docs     : le fichier .pgn est attaché à l'article comme un document
Test08 Table 2 board   : deux balises pgn horizontalement dans une table
Test09 Live horizontal : diffusion en temps réel de la partie live.pgn mode horizontal
Test10 Live vertical   : diffusion en temps réel de la partie live.pgn mode vertical

Le répertoire test\ n'est pas nécessaire à l'exécution du plugin.

________________

4. Tailles de pièces disponibles :
Alpha : taille 30, squareSize 38
Merida : pieceSize 44, squareSize 56
USCF: taille 20, squareSize 22

Pour installer une taille alternative récupérer dans l'original de pgn4web la taille de police correspondante et la ranger dans le dossier correspondant pgn4spip/pgn4web/images.
Attention lors d'une prochaine mise à jour  (alpha, merida ou uscf)
________________

5. 1 Résolution de problèmes

Symptôme 1 : avec le Couteau Suisse activé, ou "guillemets typographiques" ou "ortho-typo"
A la place de l'échiquier, je vois l'entête du PGN avec éventuellement
quelques étranges guillemets ou même des figurines d'Echecs à l'intérieur des commentaires PGN.

Si ce problème se produit: désactiver pgn4spip puis réactiver pgn4spip.
Cela permet d'activer la fonction decorrection des guillemets de pgn4spip après celle des plugins d'amélioration des guillemets.
si le problème persiste, essayer à nouveau en vidant le cache de SPIP
entre la désactivation et la réactivation du plugin pgn4spip.

Ce problème n'est pas constaté sur les versions récentes de SPIP (3.2.7 et 3.3.0-dev) et celles des plugins de typographie.
____________

Symptôme 2 : les paramètres à l'intérieur de la balise <pgn> de document attaché ne sont pas pris en compte
Syntaxe erronée : <pgn1 | movesDisplay = puzzle bd = s>

Solution: ajouter option="..." et enlever chaque espace séparateur 
sauf après une valeur et le paramètre suivant.
<pgn1|option="movesDisplay=puzzle bd=s sS=35">

Voir test\Test05
________________

5. 2 Diffusion en live de la partie d'Echecs

test\Test09 et Test10 montrent l'usage du paramètre movesDisplay=live et refreshMinutes=0.25
qui gérent le rafraichissement périodique de l'échiquier à partir du fichier
pgnData=http://localhost/spip/plugins/pgn4spip/pgn4web/live/live.pgn
mis à jour à une autre fréquence grâce au simulateur 
spip\plugins\pgn4spip\pgn4web\live\live-simulation.sh

C'est un script Unix Bash que l'on peut faire fonctionner dans Windows avec le freeware c:\cygwin
Lire instructions dans test\Test09

spip\plugins\pgn4spip\test\MacroRunSimu.bat est un outil batch à personnaliser.
Indiquer le chemin complet du serveur web et le répertoire du plugin.
Il définit des macros doskey pour la ligne de commande "Command Prompt" de Windows
afin de lancer votre serveur web et le (s)imulateur.