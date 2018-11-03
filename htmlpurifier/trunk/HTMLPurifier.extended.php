<?php

class HTMLPurifier_URIScheme_generic extends HTMLPurifier_URIScheme {
    public function doValidate(&$uri, $config, $context){ 
      return  true; 
    }
}

class HTMLPurifier_URIScheme_tcp extends HTMLPurifier_URIScheme_generic {}
class HTMLPurifier_URIScheme_udp extends HTMLPurifier_URIScheme_generic {}
class HTMLPurifier_URIScheme_ssh extends HTMLPurifier_URIScheme_generic {}

