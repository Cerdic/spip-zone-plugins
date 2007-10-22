<?php



function recuperer_id3($fichier){

// Copy remote file locally to scan with getID3()
require_once(_DIR_PLUGIN_GETID3.'/getid3/getid3.php');
$getID3 = new getID3;	
$remotefilename = $fichier ;
if ($fp_remote = @fopen($remotefilename, 'rb')) {
    $localtempfilename = tempnam('tmp', 'getID3');
    if ($fp_local = @fopen($localtempfilename, 'wb')) {
        // Do this to copy the entire file:
        //while ($buffer = fread($fp_remote, 16384)) {
        //    fwrite($fp_local, $buffer);
        //}
        
        // Do this to only work on the first 10kB of the file (good enough for most formats)
        $buffer = fread($fp_remote, 10240);
        fwrite($fp_local, $buffer);
        
        fclose($fp_local);
        
        // Scan file - should parse correctly if file is not corrupted
        $ThisFileInfo = $getID3->analyze($localtempfilename);
        // re-scan file more aggressively if file is corrupted somehow and first scan did not correctly identify
        /*if (empty($ThisFileInfo['fileformat']) || ($ThisFileInfo['fileformat'] == 'id3')) {
            $ThisFileInfo = GetAllFileInfo($localtempfilename, strtolower(fileextension($localtempfilename)));
        }*/
        
        // Delete temporary file
        unlink($localtempfilename);
    }
    fclose($fp_remote);
}
	
	if(sizeof($ThisFileInfo)>0){
	
			$id3['titre'] = ($ThisFileInfo['tags']['id3v2']['title']['0']) ? $ThisFileInfo['tags']['id3v2']['title']['0'] : $ThisFileInfo['id3v2']['comments']['title']['0'] ;
			$id3['artiste'] = ($ThisFileInfo['tags']['id3v2']['artist']['0']) ? $ThisFileInfo['tags']['id3v2']['artist']['0'] : $ThisFileInfo['id3v2']['comments']['artist']['0'] ;
			$id3['album']  = ($ThisFileInfo['tags']['id3v2']['album']['0']) ? $ThisFileInfo['tags']['id3v2']['album']['0'] : $ThisFileInfo['id3v2']['comments']['album']['0'] ;
			$id3['genre'] = ($ThisFileInfo['tags']['id3v2']['genre']['0']) ? $ThisFileInfo['tags']['id3v2']['genre']['0'] : $ThisFileInfo['id3v2']['comments']['genre']['0'] ;
			$id3['comment'] = ($ThisFileInfo['tags']['id3v2']['comment']['0']) ? $ThisFileInfo['tags']['id3v2']['comment']['0'] : $ThisFileInfo['id3v2']['comments']['comment']['0'] ;
			$id3['sample_rate'] = $ThisFileInfo['audio']['sample_rate'] ;
			$id3['track'] = $ThisFileInfo['tags']['id3v2']['track']['0'] ;
			$id3['encoded_by'] = $ThisFileInfo['tags']['id3v2']['encoded_by']['0'] ;
			$id3['totaltracks'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
			$id3['tracknum'] = $ThisFileInfo['tags']['id3v2']['totaltracks']['0'] ;
			
		
			return $id3 ;
			
	}	
			
	
	
}

?>