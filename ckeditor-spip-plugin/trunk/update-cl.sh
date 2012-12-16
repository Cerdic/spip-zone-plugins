#!/bin/sh
rev=$(svnversion)
rev=${rev##*:}

echo $rev

svn2cl --group-by-day --reparagraph  --include-rev --include-actions --output=ChangeLog.txt
