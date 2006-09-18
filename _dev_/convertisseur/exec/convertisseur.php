<?php

// -------------------------------
// Main 
// ------------------------------
function exec_convertisseur(){
  include_spip("inc/presentation");
  global $spip_lang_right;
  
  $conv_in  = "";  
  $conv_out = "";
  $log      = "";
  $format   = "";  
  
  // check rights (utile ?)
  global $connect_statut;
	global $connect_toutes_rubriques;
  if ($connect_statut != '0minirezo') {    
		debut_page(_T("convertisseur:convertir_titre"), "naviguer", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
   
  // --------------------------------------------------------------------------- 
	// Definition des regex pour les convertions 
	// ---------------------------------------------------------------------------
	$conv_formats = array(); 
  
  // Convertion MoinWiki -> SPIP
  // ref. syntaxe: http://zone.spip.org/trac/spip-zone/wiki/WikiFormatting
  // ref. syntaxe: http://moinmoin.wikiwikiweb.de/HelpOnFormatting?highlight=%28formatting%29  
  $conv_formats['MoinWiki_SPIP'] = array(
      "pattern" => array(
        'code'   => "{{{([^}}}]*)}}}", // FIXME si } dans {{{ }}}                
        'bold3'  => "'''''([^''''']*)'''''",
        'bold2'  => "''''([^'''']*)''''",
        'bold'   => "'''([^''']*)'''", 
        'i'      => "''([^'']*)''",      
        'under'  => "__([^\_]*)__",  
        'del'    => "~~([^\~]*)~~",
        'h4'     => "==== ([^ ====]*) ====",
        'h3'     => "=== ([^ ===]*) ===",
        'h2'     => "== ([^ ==]*) ==",
        'h'      => "= ([^ =]*) =", 
        'link2'  => "\\[([^\\[]*) ([^(\\[| )]*)\\]", // FIXME si plusieurs espaces blanc
        'cell'   => "\|\|([^\|]*)\|\|",
        'ul'     => "([^ ]*)\*([^ \*]*)",  
        'ul_pas2'=> " -\*", 
        'ul2'    => "  -\*", 
        'ul3'    => "  -\**",        
        'ol2'    => "   ([^ ]*)1.([^ 1.]*)",
        'ol'     => " 1\.([^ 1\.]*)",
        
        ),
      "replacement" => array(
        'code'   => "<code>\\1</code>",              
        'bold3'   => "{{\\1}}",
        'bold2'   => "{{\\1}}", 
        'bold'   => "{{\\1}}", 
        'i'      => "{\\1}",       
        'under'  => "<span class='underline'>\\1</span",
        'del'    => '<del>\\1</del>',
        'h4'     => "{{{\\1}}}", 
        'h3'     => "{{{\\1}}}", 
        'h2'     => "{{{\\1}}}",  
        'h'      => "{{{\\1}}}",                
        'link2'  => "[\\2->\\1]",
        'cell'   => "|\\1|",        
        'ul'     => "-*\\2", 
        'ul_pas2'=> "-*", 
        'ul2'    => "-**",
        'ul3'    => "-***",             
        'ol2'    => "1.#\\2",
        'ol'     => "-#\\1", 
        )
  );
  
  // Convertion BBcode -> SPIP
  // ref. syntaxe: http://en.wikipedia.org/wiki/BBCode
  // voir aussi la version filtre: http://www.spip-contrib.net/Du-BBcode-dans-SPIP  
  // question: detecter si barre enrichie pour adopter la syntaxte etendue ?
  $conv_formats['BBcode_SPIP'] = array(
      "pattern" => array(
        'url'   => "\\[url]([^\\[]*)\\[/url\\]",
        'url2'  => "\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]",
        'email' => "\\[email\\]([^\\[]*)\\[/email\\]",
        'email2'=> "\\[email=([^\\[]*)\\]([^\\[]*)\\[/email\\]",
        'color' => "\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]",
        'size'  => "\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]",
        //'list'  => "!\[list\](.+)\[/list\]!Umi",
        //'list2' => "!\[\*\](.+)(?=(\[\*\]|</ul>))!Umi",
        'code'  => "\\[code]([^\\[]*)\\[/code\\]",
        'quote' => "\\[quote]([^\\[]*)\\[/quote\\]",
        'b'     => "\\[b]([^\\[]*)\\[/b\\]",
        'i'     => "\\[i]([^\\[]*)\\[/i\\]",
        'center'=> "\\[center]([^\\[]*)\\[/center\\]",
        'img'   => "\\[img]([^\\[]*)\\[/img\\]",
      ),
      "replacement" => array(
        'url'   => "[\\1->\\1]",
        'url2'  => "[\\2->\\1]",
        'email' => "[\\1->mailto:\\1]",
        'email2'=> "[\\2->mailto:\\1]",
        'color' => "<span style=\"color:\\1\">\\2</span>",
        'size'  => "<span style=\"font-size:\\1px\">\\2</span>",
        //'list'  => "<ul> $1 </ul>",
        //'list2' => "<li>$1</li>",
        'code'   => "<code>\\1</code>",
        'quote'  => "<quote>\\1</quote>",
        'b'      => "{{\\1}}",
        'i'      => "{\\1}",
        'center' => "<div style=\"text-align:center:\\1\">\\2</div>",
        'img'    => "<img src=\"\\1\" alt='' />",
      )      
  );
  
  // Convertion SPIP -> txt
  $conv_formats['SPIP_txt'] = array(
      "pattern" => array(
        'h'     => "{{{([^}}}]*)}}}",
        'b'     => "{{([^}}]*)}}",
        'i'     => "{([^}]*)}",
        'url'   => "\\[([^\\[]*)->([^(\\[| )]*)\\]",         
      ),
      "replacement" => array(
        'h'   => "\\1\n",
        'b'   => "* \\1 *",
        'i'   => "\\1",
        'url' => "\\1 (\\2)",       
      )      
  );
  
  // --------------------------------------------------------------------------- 
	// Action ? 
	// ---------------------------------------------------------------------------
	if (isset($_POST['conv_in'])) {
	   $conv_in = $_POST['conv_in'];
     if (isset($_POST['format'])) {
        $conv_out = $_POST['conv_in'];
        $format = trim(strip_tags($_POST['format']));        
        if (isset($conv_formats[$format])) { 
          // on convertit (en avant les regex!)                   
          foreach($conv_formats[$format]['pattern'] as $key=>$pattern)  {
              $replacement = $conv_formats[$format]['replacement'][$key];              
              $conv_out = eregi_replace($pattern, $replacement, $conv_out);
          }                    
        } else {
            $log = "<span style='color:red'>"._T("convertisseur:unknown_format")."</span>";
        }
     }	   
  }
  
  // ---------------------------------------------------------------------------
  // HTML output 
  // ---------------------------------------------------------------------------
	debut_page(_T("convertisseur:convertir_titre"), "naviguer", "plugin");	
  debut_gauche();
	debut_boite_info();
	echo _T("convertisseur:convertir_desc");	
	fin_boite_info();
	
	debut_droite();
	echo $log;	
	echo "<form method='post'>\n";
	if ($conv_out!="") {
	   $conv_out = str_replace("</textarea>",'&lt;/textarea&gt;',$conv_out);
	   echo "<div style='background-color:#E6ECF9;padding:8px 3px;margin-bottom:5px'>"._T("convertisseur:convertir_en");
	   if (isset($conv_formats[$format])) echo "<strong>"._T("convertisseur:$format")."</strong>\n";
	   echo "<textarea name='conv_out' cols='65' rows='12'>$conv_out</textarea><br />\n";
	   echo "</div>\n";
  }
	$conv_in = str_replace("</textarea>",'&lt;/textarea&gt;',$conv_in);
	echo "<textarea name='conv_in' cols='65' rows='12'>$conv_in</textarea><br />\n";
	echo _T("convertisseur:from");
  echo "<select name='format'>\n"; 
  foreach ($conv_formats as $k=>$val) {  
      if ($format==$k) $selected = " selected='selected'";
                  else $selected = "";
      echo "<option value='$k'$selected>"._T("convertisseur:$k")."</option>\n";
  }
  echo "</select>\n";	
  echo "<input type='submit' value='". _T("convertisseur:convertir")."'>\n";  
  echo "</form>\n"; 

  
  fin_page();
}
?>
