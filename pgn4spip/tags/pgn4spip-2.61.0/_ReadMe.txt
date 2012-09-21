The pgn4spip 2.61.0 plug-in for SPIP 2 and 3 displays the chessboard and the chess game 
in PGN format inside an article between [pgn] and [/pgn] tags or [PGN] and [/PGN] tags.

Usage in the body of an article:

Before
[pgn] 1. e4 Nf6 [/pgn]
After

pgn4spip is the SPIP interface of pgn4web by Paolo Casaschi
http://pgn4web.casaschi.net/home.html

Demo on-line
http://pgn4web.casaschi.net/demo.html

Demo off-line
http://localhost/spip/plugins/pgn4spip/pgn4web/demo.html

pgn4spip has been developed by Matt Chesstale in PHP, Javascript, HTML, CSS, CFG and SPIP.
matteo.chesstale@gmail.com
license: GNU GPL 3.0 (c) 2012

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

It does not require the CFG 3.x plug-in for its configuration in SPIP 3.x
It requires the CFG 1.x plug-in for its configuration in SPIP 2.x
http://plugins.spip.net/cfg.html

pgn4spip is compatible with the plug-in Swiss Knife 1.8.x, 
tool "Curly inverted commas" << French double quotes >>
pgn4spip must be enabled AFTER Swiss Knife.
pgn4spip does not require Swiss Knife.
________________

1. Setup of the plug-in

Unzip pgn4spip-2.61.0.zip in the folder of SPIP plug-ins.
For example: D:\Xampp\Xampp177\htdocs\spip\plugins\

Download the latest version of pgn4web: pgn4web-2.61.zip
http://code.google.com/p/pgn4web/downloads/list

Unzip pgn4web-2.61.zip in the pgn4spip folder
For example: D:\Xampp\Xampp177\htdocs\spip\plugins\pgn4spip

The name of plug-in folder is always in lowercase.

Remove the version number:
rename pgn4spip\pgn4web-2.61 into pgn4spip\pgn4web
For example: D:\Xampp\Xampp177\htdocs\spip\plugins\pgn4spip\pgn4web

The pgn4web folder has a size of 13 MB.
In the case of site having a limited hard disk,
one can eventually delete certain unused fonts 
among alpha, merida, svgchess or demo files.
Keep at least the fonts uscf/20 and 26. 
________________

2. Configuration of the plugin

http://localhost/spip/ecrire/?exec=admin_plugin
SPIP Configuration > Manage plugins

Click [x] to activate the plug-in.
In SPIP 3, confirm clicking to "SUBMIT" even grayed.

Click to the name "pgn4spip" to get help.

Click to cfg-16.png icon on the right at the level of the name pgn4spip

The folder doc\ is not required at run-time for the plugin.
You do not need to upload it in your on-line SPIP web site.

The configuration can be optionally tested on-line in
http://pgn4web.casaschi.net/board-generator.html

or off-line
http://localhost/spip/plugins/pgn4spip/pgn4web/board-generator.html
________________

3. Advanced usage

The plugin can be optionally configured in the parameters of the [pgn] tag
inside an article. See the help during the configuration of the plugin.

doc\pgn4web parameters.txt
http://localhost/spip/plugins/pgn4spip/pgn4web/board.html?help=true

Example in the body of an article:

Before
[pgn height=260 autoplayMode=loop] 1. e4 Nf6 [/pgn]
After

Examples of articles SPIP with the [pgn] tag

Text file example of test to be copied-pasted in a SPIP 2 or SPIP 3 article:
Test01 Symbol eval.txt : variations, evaluations symbols "with the idea of"
Test02 MultiPgn.txt    : several chess games
Test03 Puzzle Fen.txt  : two chess problems
Test04 Puzzle URL      : chess problems through the URL of .pgn file
Test05 Puzzle Doc.txt  : chess problems in .pgn file attached to the article
Test06 Comment.txt     : comments in a chess game
Test07 Puzzle Docs     : the .pgn file is attached to the article as a document
Test08 Table 2 board   : two pgn tags horizontally in a table
Test09 Live horizontal : broadcast in real time live.pgn in horizontal mode
Test10 Live vertical   : broadcast in real time live.pgn in vertical mode

The folder test\ is not required at run-time for the plugin.
________________

3.1 Troubleshooting

Symptom 1: with Swiss Knife enabled
Instead of the chessboard, I see the header of the PGN with eventually
some strange quotes or even chess figurines inside PGN comments.

Solution 1: disable pgn4spip then reenable pgn4spip.
This allows pgn4spip to be enabled after Swiss Knife.

Solution 2: if the problem persists, try again emptying the SPIP cache
between disabling and reenabling the pgn4spip plugin.
SPIP 3 : Maintenance > Empty the cache > Click "Empty the cache" in Current cache size
SPIP 2 : Configuration > Empty the cache > Click "Empty the cache" in Current size of the cache

Solution 3: if the problem persists, try again emptying equally the browser cache otherwise
disable Swiss Knife or its tool "Curly inverted commas".
__________

Symptom 2: parameters inside tag <pgn> of attached document are not taken in consideration
Wrong syntax: <pgn1 | movesDisplay = puzzle bd = s>

Solution: add option="..." and remove each blank separator 
except after a value and the next parameter.
<pgn1|option="movesDisplay=puzzle bd=s sS=35">

See test\Test05
________________

3.2 Live broadcast of the chess game

test\Test09 and test10 show the usage of parameters movesDisplay=live and refreshMinutes=0.25
that manage the periodic refresh of the chessboard from file
pgnData=http://localhost/spip/plugins/pgn4spip/pgn4web/live/live.pgn
updated at a different frequency using the simulator
spip\plugins\pgn4spip\pgn4web\live\live-simulation.sh

This is an Unix Bash script that can be run in Windows with the freeware c:\cygwin
Read instructions in test\Test09

spip\plugins\pgn4spip\test\MacroRunSimu.bat is a batch tool to be customized.
Indicate the full path of your web server and the folder of the plugin.
It defines doskey macros for the command line "Command Prompt" in Windows
to launch your web server and the (s)imulateur.