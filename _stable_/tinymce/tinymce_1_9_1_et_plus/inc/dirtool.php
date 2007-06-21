<?php 

/* Do not remove or alter this section***************************

************************Class Description************************
dirtool  (c) Nov 2005  Uwe Stein

dirtool allows to copy, delete and move complete directory-trees 
*****************************************************************

************************ sorry and thx ***************************
Please excuse errors in this text. English isnt my native language,
and so  suggestions about the code and the spelling are welcome

*********************Contact and Bug report***********************
contact me using the "contact-button" at one of my packages at phpclasses.org

********************Licence****************************************
This software is covered by The GNU General Public License (GPL)
***************************************************************

**************End of do not remove or alter section*************************/

/*  CDE : ACTi : modified preg_replace by str_replace */

// get the value of slash according to the OS
function slash(){
    if(isset($_SERVER['OS'])) 
           return "\\";
    else 
          return "/";
}
class dirtool {
     var $path;
     var $from;
     var $to;
     var $aContent= array();
     var $debug = FALSE;
     
     function dirtool($path) {
       if (!is_dir($path))
           die("<br><strong>$path is NOT a directory</strong>");
        $this->path = realpath($path);
        $from = "";
        $to="";
        
        // read the directory
        $slash = slash();
        $verz=opendir ($path);
        while ($file = readdir ($verz)) {
            if ($file != "." && $file != "..") {
               $tmp = $this->path.$slash.$file;
                if (is_dir($tmp)) 
                   $this->aContent[] = new dirtool($tmp);
                else 
                    $this->aContent[] = $tmp;
             }  //if 
        } // while
         closedir($verz);
     }  //End of func directory

     function move($newLocation) {
         $perm = fileperms($this->path);
         $this->copy($newLocation,$perm); // :-)
         $this->delete();
         chmod($newLocation,$perm);
     }

     function copy($path, $mode, $from = "") {
            $this->copy_tree($path, $mode, $from = "");
            $this->copy_files($path, $mode, $from = "");
     }  // End of func copy

     function delete(){
       $this->delete_files();
       // because $this contains still the files in the array aContent
       // a new dir-object is created. The new one, read the tree again and contains only the 
       //subdirectories  (the files are now deleted )
       $dummy = new dirtool($this->path);
       $dummy->debug($this->debug); // copy the debug-state
       $dummy->delete_tree();
     }
     function copy_tree($path, $mode, $from = "") {
        if (!mkdir($path, $mode))
           die ("Error: directory $path could not be created"); 
           if ($this->debug == TRUE)
              echo "<br>Directory <b>$path</b> created";
         chmod($path,$mode);
        // at the first loop of recursiv callings keep the "$from-path        
        //if ($from == "")
       $this->from = $this->path;
       $this->to = $path;

       // walk through the array aContent and create all  directories
       for ($i=0; $i < count($this->aContent); $i++) {
            if (is_object($this->aContent[$i])) {
               //$pattern = "^".$this->from."^"; //mise en commentaire ACTi 14/06/2007
			   $pattern = $this->from;
               $replace = $this->to;
			   //$dirToCreate = preg_replace($pattern,$replace, $this->aContent[$i]->path ); //mise en commentaire ACTi 14/06/2007
			   $dirToCreate = str_replace($pattern,$replace, $this->aContent[$i]->path );
            // call copy recursively to create the new directory and process the next level
              $this->aContent[$i]->copy_tree($dirToCreate, $mode,$this->from);
            } // End if ... 

       }  // End for .... 
       clearstatcache();

    }  // End of func copy_tree

    function copy_files ($path, $mode, $from = "") {
       for ($i=0; $i < count($this->aContent); $i++) {
            // if it is a dir-object, call copy recursively
            if (is_object($this->aContent[$i])) {
               ////$pattern = "^".$this->from."^"; //mise en commentaire ACTi 14/06/2007
			   $pattern = $this->from;
               $replace = $this->to;
               //$newpath = preg_replace($pattern,$replace, $this->aContent[$i]->path ); //mise en commentaire ACTi 14/06/2007
			   $newpath = str_replace($pattern,$replace, $this->aContent[$i]->path ); 
             // call copy recursively to enter the sub-dir and process the next level
              $this->aContent[$i]->copy_files($newpath, $mode,$this->from);
            } // End if ... 
            // if it is a file, copy 
            else {
                 // save the fileperms 
                 $perms = fileperms($this->aContent[$i]);
                 $src = $this->aContent[$i];
                 //$pattern = "^".$this->from."^";  //mise en commentaire ACTi 14/06/2007
				 $pattern = $this->from;
                 $replace = $this->to;
                 //$dest = preg_replace($pattern,$replace, $this->aContent[$i] ); //mise en commentaire ACTi 14/06/2007
				 $dest = str_replace($pattern,$replace, $this->aContent[$i] );
                 copy($src,$dest);
                 if ($this->debug == TRUE) 
                    echo "<br><b>$src</b> copied to <b>$dest</b>";
                 chmod($dest,$perms);
            }
        } // End for.....
        clearstatcache();
    
    }  // End of func copy files 

    //  deletes all dirs and subdirs  --> assumes that there are no files in the tree 
    function delete_tree() {
        //remove all entries by calling delete_tree for each member in the dir
        if (count($this->aContent)) {
           while (count($this->aContent)) {
                    $this->aContent[0]->delete_tree();
                    array_shift($this->aContent);
           }
        }
        if (!rmdir($this->path)) {
           $mess = "<br>could not remove dir ".$this->path;
           die($mess);
        }
        if ($this->debug == TRUE) {
           echo "<br>Directory <b> ".$this->path."</b> removed";
        }
        return;
    }  // End of func delete_tree
    
    // deletes all files in the complete tree  
    function delete_files() {
      for ($i=0; $i < count($this->aContent); $i++) {
            // if it is a dir-objekt, call delete_files recursively
            if (is_object($this->aContent[$i])) {
               $pattern = "^".$this->from."^";
               $replace = $this->to;
               $newpath = preg_replace($pattern,$replace, $this->aContent[$i]->path );
             // call copy recursively to enter the sub-dir and process the next level
              $this->aContent[$i]->delete_files($newpath, $mode,$this->from);
            } // End if ... 
            // if it is a file, delete 
            else {
               if (!@unlink( $this->aContent[$i] ))  {
                   $mess = "<b>removing file ".$this->aContent[$i]." failed  Please check the fileperms";
                   $mess .=" and try again</b>";
                   echo $mess;
                   exit;
               }
               if ($this->debug == TRUE) {
                  echo "<br>File<b> ".$this->aContent[$i]."</b> removed";
               }
            }
         } // End for.....
         clearstatcache();
    }  // End func delete files 
    
    function debug($bool=TRUE){
        $this->debug = $bool;
    }
    
    
} // End of class dirtool
?>