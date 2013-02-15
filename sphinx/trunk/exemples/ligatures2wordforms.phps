<?php

function usage() {
  echo <<< EOF
# usage:
# php ligatures2wordforms.php index > sites/wordforms.txt
# s'il y a eu la moindre modif, il faut alors tout reindexer et relancer (stop&kill) le serveur sphinx

EOF;
}


$index = $argv[1];
if (!preg_match('/^\w+$/', $index))
  die(usage());

$dict = file('data/'.$index.'.dict.txt');

foreach($dict as $l) {
  list($w,$freq) = explode(' ', trim($l));

  if (preg_match('/\x{0152}/u', $w)) {
    $v = preg_replace('/\x{0152}/u', 'OE', $w);
    echo mb_strtolower("$w > $v\n", 'UTF8');
  }  
}