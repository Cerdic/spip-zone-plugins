<?php
/**********************************************************************************
 * @Subject French resources: this is a SPIP 2 or 3 language file
 * @package pgn4spip plugin to embed pgn4web chessboard in a SPIP 2.x or 3.x article
 * @version 2.61.0
 * @copyright copyright (c) 2012 Matt Chesstale 
 * @license GNU General Public License version 3
 **********************************************************************************/

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Only lowercase in the key. In the value, french accents must be in HTML format &...;
'ok'			=> 'OK',
'reinit'		=> 'Remettre les valeurs par d&eacute;faut',
'config_reinit'	=> 'La configuration a &eacute;t&eacute; remise avec les valeurs par d&eacute;faut',

'boxtitle'		=> 'Configuration g&eacute;n&eacute;rale de l\'&eacute;chiquier et du PGN',

'squareclick'	=> <<<EOT
Chaque case de l'&eacute;chiquier a une infobulle et une fonction.<br />
Pour inverser l'&eacute;chiquier, cliquer sur la case <b>e7</b> ou appuyer sur touche "<b>f</b>".
EOT
,
'module'		=> 'Module',
'horizontal'	=> 'horizontal',
'vertical'		=> 'vertical',

'auto'			=> 'Hauteur automatique',
'manual'		=> 'manuelle : ',
'pixels'		=> ' pixels',

'chessboard'	=> 'Echiquier',
'square'		=> 'Case : ',
'white'			=> 'blanche',
'black'			=> 'noire',

'piece'			=> 'Taille pi&egrave;ce :',
'default'		=> 'auto',
'font'			=> 'Police :',
'alpha'			=> 'alpha',
'merida'		=> 'merida',
'uscf'			=> 'uscf',
'svgchess'		=> 'svg',

'focus'			=> 'Focus',
'border'		=> 'Bord',
'background'	=> 'Fond',

'pgn'			=> 'PGN',
'pgnheader'		=> 'Ent&ecirc;te',
'pgnmove'		=> 'Coup',
'pgncomment'	=> 'Commentaire',
'pgnfocus'		=> 'Focus',
'pgndelay'		=> 'D&eacute;lai (ms)',

'showmoves'		=> 'Notation',
'figurine'		=> 'figurine',
'text'			=> 'texte',
'puzzle'		=> 'puzzle',
'hidden'		=> 'cach&eacute;e',
'live'			=> 'live',

'ctrlbrowse'	=> 'Bouton',
'ctrlarrow'		=> 'Fl&egrave;che',
'ctrlbckgrnd'	=> 'Fond',

'custom'		=> 'plat',
'standard'		=> '3D',

'newline'		=> 'bloc',
'inline'		=> 'suite',
'hidden2'		=> 'cach&eacute;',

'focusborder'	=> 'bord',
'focussquare'	=> 'case',

'liverate'		=> 'live (mn)',

'autoplay'		=> 'Jeu auto :',
'none'			=> 'aucun',
'game'			=> '1 fois',
'loop'			=> 'boucle',

'conflocalpgn'	=> <<<EOT
<legend>Configuration <i>particuli&egrave;re</i> d'un PGN dans un article SPIP</legend>
<fieldset><legend>Syntaxe</legend>
<b>[pgn</b> <i>prm1</i><b>=</b><i>valeur1</i> <i>prm2</i></i><b>=</b><i>valeur2</i> ...</i><b>]</b> <i>notation alg&eacute;brique <a href="http://fr.wikipedia.org/wiki/Portable_Game_Notation" target="_blank">PGN</a> US</i> <b>[<font color="red">/</font>pgn]</b>
</fieldset>
<br />
Liste des <a href="../plugins/pgn4spip/pgn4web/board.html?help=true" target="_blank">param&egrave;tres</a> optionnels (" | " = "ou"; [] = par d&eacute;faut; () = initiale) :
<table cellspacing="5">
<tr><td><b><u>f</u></b>rame<b><u>H</u></b>eight=[""] | <i>nombre</i><br />
		fh=[""] | <i>nombre</i><br />
		<b>s</b>quare<b>S</b>ize=<i>nombre</i><br />
		<b>i</b>nitial<b>V</b>ariation=<i>nombre</i>
	</td>
	<td><b>l</b>ayout=[<b>h</b>]orizontal | (<b>v</b>)ertical<br />
		<b>i</b>nitial<b>G</b>ame=[<b>f</b>]irst | (<b>l</b>)ast | (<b>r</b>)andom | <i>nombre</i><br />
		<b>p</b>iece<b>S</b>ize=[<b>d</b>]efault | <i>nombre</i><br />
		<b>a</b>utoplay<b>M</b>ode=[<b>n</b>]one | (<b>g</b>)ame | (<b>l</b>)oop
	</td>
</tr>
<tr><td colspan="2"><b>m</b>oves<b>D</b>isplay=[<b>f</b>]igurine | (<b>t</b>)ext | (<b>p</b>)uzzle | (<b>h</b>)idden | (<b>l</b>)ive<br />
					<b>i</b>nitial<b>H</b>alfmove=[<b>s</b>]tart | (<b>e</b>)nd | (<b>r</b>)andom | (<b>c</b>)omment | <i>nombre</i><br />
					<b>p</b>iece<b>F</b>ont=[<b>d</b>]efault | (<b>a</b>)lpha | (<b>m</b>)erida | (<b>u</b>)scf | (<b>s</b>)vgchess
	</td>
</tr>
</table>
Exemple dans le corps d'un article SPIP consacr&eacute; &agrave; la <a href="http://fr.wikipedia.org/wiki/D%C3%A9fense_Alekhine" target="_blank">d&eacute;fense Alekhine</a> :<br />
<textarea rows=6 cols=55>
Avant
[pgn frameHeight=500 squareSize=46 pieceSize=40 pf=m
 movesDisplay=figurine layout=vertical am=loop]
1. e4 Nf6 2.e5 Nd5 3.c4 Nb6
[/pgn]
Apr&egrave;s
</textarea>
EOT
);
?>