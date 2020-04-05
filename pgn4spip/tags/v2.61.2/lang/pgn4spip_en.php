<?php
/**********************************************************************************
 * @Subject English resources: this is a SPIP 2 or 3 language file
 * @package pgn4spip plugin to embed pgn4web chessboard in a SPIP 2.x or 3.x article
 * @version 2.61.0
 * @copyright copyright (c) 2012 Matt Chesstale 
 * @license GNU General Public License version 3
 **********************************************************************************/

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Only lowercase in the key. In the value, french accents must be in HTML format &...;
'ok'			=> 'OK',
'reinit'		=> 'Reset to the default values',
'config_reinit'	=> 'The configuration has been reset to the default values',

'boxtitle'		=> 'Main configuration of the chessboard and the PGN',

'squareclick'	=> <<<EOT
Each square of the chessboard has a tooltip and a feature.<br />
To flip the chessboard, click to the square <b>e7</b> or press key "<b>f</b>".
EOT
,
'module'		=> 'Module',
'horizontal'	=> 'horizontal',
'vertical'		=> 'vertical',

'auto'			=> 'Automatic height',
'manual'		=> 'manual: ',
'pixels'		=> ' pixels',

'chessboard'	=> 'Chessboard',
'square'		=> 'Square: ',
'white'			=> 'white',
'black'			=> 'black',

'piece'			=> 'Piece size :',
'default'		=> 'auto',
'font'			=> 'Font:',
'alpha'			=> 'alpha',
'merida'		=> 'merida',
'uscf'			=> 'uscf',
'svgchess'		=> 'svg',

'focus'			=> 'Focus',
'border'		=> 'Border',
'background'	=> 'Background',

'pgn'			=> 'PGN',
'pgnheader'		=> 'Header',
'pgnmove'		=> 'Move',
'pgncomment'	=> 'Comment',
'pgnfocus'		=> 'Focus',
'pgndelay'		=> 'Delay (ms)',

'showmoves'		=> 'Notation',
'figurine'		=> 'figurine',
'text'			=> 'text',
'puzzle'		=> 'puzzle',
'hidden'		=> 'hidden',
'live'			=> 'live',

'ctrlbrowse'	=> 'Button',
'ctrlarrow'		=> 'Arrow',
'ctrlbckgrnd'	=> 'Background',

'custom'		=> 'flat',
'standard'		=> '3D',

'inline'		=> 'inline',
'newline'		=> 'newline',
'hidden2'		=> 'hidden',

'focusborder'	=> 'border',
'focussquare'	=> 'square',

'liverate'		=> 'live (mn)',

'autoplay'		=> 'Autoplay:',
'none'			=> 'none',
'game'			=> 'once',
'loop'			=> 'loop',

'conflocalpgn'	=> <<<EOT
<legend><i>Particular</i> configuration of a PGN in a SPIP article</legend>
<fieldset><legend>Syntax</legend>
<b>[pgn</b> <i>prm1</i><b>=</b><i>value1</i> <i>prm2</i></i><b>=</b><i>value2</i> ...</i><b>]</b> <i><a href="http://en.wikipedia.org/wiki/Portable_Game_Notation" target="_blank">PGN</a> algebraic chess notation </i> <b>[<font color="red">/</font>pgn]</b>
</fieldset>
<br />
List of optional <a href="../plugins/pgn4spip/pgn4web/board.html?help=true" target="_blank">parameters</a> (" | " = "or"; [] = by default; () = abbreviation) :
<table cellspacing="5"
<tr><td><b><u>f</u></b>rame<b><u>H</u></b>eight=[""] | <i>number</i><br />
		fh=[""] | <i>number</i><br />
		<b>s</b>quare<b>S</b>ize=<i>number</i><br />
		<b>i</b>nitial<b>V</b>ariation=<i>number</i>
	</td>
	<td><b>l</b>ayout=[<b>h</b>]orizontal | (<b>v</b>)ertical<br />
		<b>i</b>nitial<b>G</b>ame=[<b>f</b>]irst | (<b>l</b>)ast | (<b>r</b>)andom | <i>number</i><br />
		<b>p</b>iece<b>S</b>ize=[<b>d</b>]efault | <i>number</i><br />
		<b>a</b>utoplay<b>M</b>ode=[<b>n</b>]one | (<b>g</b>)ame | (<b>l</b>)oop
	</td>
</tr>
<tr><td colspan="2"><b>m</b>oves<b>D</b>isplay=[<b>f</b>]igurine | (<b>t</b>)ext | (<b>p</b>)uzzle | (<b>h</b>)idden | (<b>l</b>)ive<br />
					<b>i</b>nitial<b>H</b>alfmove=[<b>s</b>]tart | (<b>e</b>)nd | (<b>r</b>)andom | (<b>c</b>)omment | <i>number</i><br />
					<b>p</b>iece<b>F</b>ont=[<b>d</b>]efault | (<b>a</b>)lpha | (<b>m</b>)erida | (<b>u</b>)scf | (<b>s</b>)vgchess
	</td>
</tr>
</table>
Example in the body of a SPIP article about the <a href="http://en.wikipedia.org/wiki/Alekhine%27s_Defense" target="_blank">Alekhine's defence</a>:<br />
<textarea rows=6 cols=54>
Before
[pgn frameHeight=500 squareSize=46 pieceSize=40 pf=m
 movesDisplay=figurine layout=vertical am=loop]
1. e4 Nf6 2.e5 Nd5 3.c4 Nb6
[/pgn]
After
</textarea>
EOT
);
?>