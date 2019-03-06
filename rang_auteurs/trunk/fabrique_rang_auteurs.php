<?php

/**
 *  Fichier généré par la Fabrique de plugin v6
 *   le 2019-03-06 16:04:29
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$data = array (
  'fabrique' => 
  array (
    'version' => 6,
  ),
  'paquet' => 
  array (
    'prefixe' => 'rang_auteurs',
    'nom' => 'Rang sur les auteurs',
    'slogan' => 'De la préséance que diable !',
    'description' => '',
    'version' => '1.0.0',
    'auteur' => 'Cedric',
    'auteur_lien' => '',
    'licence' => 'GNU/GPL',
    'categorie' => 'edition',
    'etat' => 'test',
    'compatibilite' => '[3.3.0-dev;3.3.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => '',
    'formulaire_config_titre' => '',
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => '',
  ),
  'objets' => 
  array (
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => 'iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFZklEQVR42q2XW0xUVxSGiyGADiIgt0Fu1hGVi4yKIEhnAAUUtVqs1oEDDrHABKug3Ow43jKiYiheGKzWSGpsY5pIm8YH00TTNDZp0qZPTR/70PSxD33qQx+a1fUdnShK4YyR5I8ra/3/v9feZ886xzdExEQEf9Hd9tRvDtpT7xMDYnLEkRixrqUGxmNjnCMLbU0azlPEty3LlmCDW/ZnZ/wGiMlRgwMXzetqILo3NWnc68iR5txMMXLsf37a0Sy/TN2VCc9OYMbkqMGBiwbt62hgQWfyokvXjXfkh2tj8pnPkB8nr0nX8lz5djQIiMlRMzlw0aB95QYux8as7U9JutSbvGi41Z72852uFrnheVtCu7fKxLuN0uHImQZy1ODARYMWD7wibSB6aKHN8GL83g65fWCffD9+UU5WlUqXI1d8iu4VS6eBHDU4cNGgxQMvPCNqQJHF83x4LiCTbU1yumq99BTly6FVy+RwgUMOF04HOWpw4KJBiwdekTbAbc9ozrb/ddNokis76+Sg7rKncLkcKc6fFXDgokGLB154Wm3A/J23Ls2ScX2uD4N+CTU1mMZ9q1dIf8nKWQEHLhq0eOAVnhNWGog/YE955H+rVB4NB2RwTYH06PFiPOBcZQlw0aDFAy888bbUgKKoOSv9jwtb3DJ1qF36i1fIkJoNrS20BLho0OKBF55WG+CYMo2sjN+Dm6vkXnebDOmuPlxXJP7SYkuAiwYtHnjhibelBnz21AeD5U653+eTk+Ul4tddBdavlkCZNcBFgxYPvPC0fAfet6d+1/Zmtozo8d3rMuTq9lo5WVYipzY4LQEuGrR44IVnJHdgpaLWoz+hkS0uudpYI6d0F2cq1siZytkBBy4atHjghWckdwCigyEy6dkhoW01crG2Qob1Np+tmh1w4KJBiwdeeOJteRAdibd5ObrTrjI5t6lSbu3dLqObNsqwLnJOFznvWj8N5KjBgYsGLR544RnRKG6Pjdncujjxzp7kxC/26S7OVm+QUGO1XNFjBRfcZdMQzsOBiwYtHni9yrsgmedmJCVMcIx+vVwcK3P+xq46+Uh3eG3HJhNjGpOjBgcuGrR44BVpA2YTJVFRWRhd0BvNjoiBX39eH2tuoLTIxHWNB/X3H67DRUO8Vj3Ci0fcgC8j5atgvUvu+gzp1I+OXp3xvPVa8pZId6EjvCAxOWpw4JoatOrx9Ss14I6KSsH8wYk+Ga6pkL7ip+8CfeF49WIF3OVCHRCTowYHLhq01GuiotLwjOwSZqX/ekKNpz7wysDqleZ8P6bTbVAXuK5fPjdbdoknffFjQEyOGhy4aNDicSAz7XFEd6AvLsblyc1Ug3YZ0Qt2XJ/vCX3ujNnjOuendMYP6LDZaZvvB8TkqMGBiwYtHngNxsW6rDYQw+6P6UT7fP9uxmp4AvKvOWi+POjFFFEBICZH7XkuWjzwwhPvORs4Gvtk97f1y3asvurJ4GHQAF0gpLd7dGu1GPbUn56+4TKM9MWPyFGDE+ajxQMvPPGeq4GYltzMf/r0KG/taZQRfX4Xa55hpLpcJvdukx5ngTQvtAWUn6hI2Gub302OGpznNXjghSferPF/DczrT4j3cWsndLhcbXAxbKbhcl2VfLKrPnz8+Yq4p4YOctTgvKjDC0+8WYO1Zmogzshb8u/RNXrLlRza4n4JE3rMpzeuM7/5X/jQTGvVx0ANzkxaPPFmDdaaqYHk1pzMv9sdOdKpX7Ud+XkvgbxX/w9oJMQHzeN/9pfgsS3opTabFm/WYK2ZGsDQqaidA5WKPHbx/C9HkaPYYEHvZK2ZGoimwHHOAbpf8MLrlTiOmgV9ImuFG/gPXI43h6Q9Vd4AAAAASUVORK5CYII=',
        ),
      ),
    ),
    'objets' => 
    array (
    ),
  ),
);
