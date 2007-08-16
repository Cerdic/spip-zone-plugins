#!/bin/sh

ulimit -t 60
cmd="/usr/local/bin/lilypond --safe --png --output=$1 $2 2> $1"
eval $cmd


