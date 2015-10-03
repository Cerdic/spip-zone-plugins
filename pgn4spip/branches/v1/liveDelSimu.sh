#!/bin/bash
# pgn4spip bash live.pgn simulator for Test09 and Test10
# bash script to create a pgn file over time simulating a live game broadcast
# $1 the current folder of the .sh script for bash shell
# @license GNU General Public License version 3
#
# Example in Windows Command Prompt:
# Test\MacroRunSimu.bat
# To run the (s)imulation, enter the new doskey macro: s
# Then validate it by enter. The following will be displayed:
#
# Generating PGN file live.pgn simulating live game broadcast
#  step 0 of 34
#  step 1 of 34
#  ...
# This script could be interrupted at any time by Ctrl+C in the Command Prompt window.
# CAUTION: this file has been edited in Notepad++ for the menu "Edit" > "EOL Conversion" > "Unix format"
# Do not use the standard Notepad in Window accessories because you could lose the Unix EOL.

cd $1
cd pgn4web/live/
rm --force live.pgn	# Remove live.pgn because it will be created again
./live-simulation.sh

