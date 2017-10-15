<?php

/**
 * Simple C pour SPIP
 * (c) 2017 MIT License
 * patch à supprimer ultérieurement
 * http://romy.tetue.net/plugins/simplec/todo.txt
 *
 */

$GLOBALS['spip_pipeline']['affichage_final'] .= '|simplec';

// Corriger code généré pour le code dans SPIP :
function simplec($texte) {

// .spip_code
$texte = str_replace('<code class="spip_code"', '<code', $texte);
$texte = str_replace('<code class=\'spip_code\'', '<code', $texte);

// div.spip_code
$texte = str_replace('<div class="spip_code"', '<pre', $texte);
$texte = str_replace('<div class=\'spip_code\'', '<pre', $texte);
$texte = str_replace('<div style="text-align: left;" class="spip_code" dir="ltr"', '<pre', $texte);
$texte = str_replace('<div style=\'text-align: left;\' class=\'spip_code\' dir=\'ltr\'', '<pre', $texte);
$texte = str_replace('</code></div>', '</code></pre>', $texte);

//
$texte = str_replace('class="spip_code"', '', $texte);
$texte = str_replace('class=\'spip_code\'', '', $texte);
// $texte = str_replace('spip_code', '', $texte);

// .spip_cadre
// = ça marche, mais on appliquera plus tard : quand on sera capable de copier auto via JS
// $texte = str_replace('<form action=\'\'  method=\'get\'><div>
// <input type=\'hidden\' name=\'exec\' value=\'\' />
// <textarea readonly=\'readonly\'', '<pre', $texte);
// $texte = str_replace('class=\'spip_cadre\' dir=\'ltr\'>', 'class=\'spip_cadre\' dir=\'ltr\'><code>', $texte);
// $texte = str_replace('</textarea></div></form>', '</code></pre>', $texte);
$texte = str_replace('<form action=\'\'  method=\'get\'><div>
<input type=\'hidden\' name=\'exec\' value=\'\' />', '', $texte); // supprimé depuis SPIP 3.0
$texte = str_replace('</textarea></div></form>', '</textarea>', $texte);

//
// $texte = str_replace('class=\'spip_cadre\'', '', $texte);
// $texte = str_replace('spip_cadre', '', $texte);

// Variantes par langage
$texte = str_replace('<pre><code class="spip">', '<pre class="spip"><code>', $texte);
$texte = str_replace('<pre><code class="html">', '<pre class="html"><code>', $texte);
$texte = str_replace('<pre><code class="css">', '<pre class="css"><code>', $texte);
$texte = str_replace('<pre><code class="less">', '<pre class="less"><code>', $texte);
$texte = str_replace('<pre><code class="scss">', '<pre class="scss"><code>', $texte);
$texte = str_replace('<pre><code class="js">', '<pre class="js"><code>', $texte);
$texte = str_replace('<pre><code class="php">', '<pre class="php"><code>', $texte);
$texte = str_replace('<pre><code class="xml">', '<pre class="xml"><code>', $texte);
$texte = str_replace('<pre><code class="md">', '<pre class="md"><code>', $texte);
$texte = str_replace('<pre><code', '<pre class="code"><code', $texte);

// Align
$texte = str_replace('<pre', '<pre dir="ltr" style="text-align: left;"', $texte);

return $texte;
}

