<?php

/* La constant _NO_MACRO_CACHE permet de désactiver complétement le cache
   des macros, indépendament du cache des squelettes.
   La constante de spip _NO_CACHE désactivant le cache des squelettes agit
   aussi sur celui des macros. */

if (!defined('_NO_MACRO_CACHE'))
  define('_NO_MACRO_CACHE', FALSE);
