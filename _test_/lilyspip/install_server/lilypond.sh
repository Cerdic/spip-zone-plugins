#!/bin/sh

ulimit -t 60
cmd="/usr/local/bin/lilypond --png --output=$1 $2 2> $1"
eval $cmd


