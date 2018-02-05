<?php
                
function filtre_calculeSpan($inSpanAmount, $inMaxItemSize,$inMaxAbsoluteSize){

   
    $v= floor($inMaxAbsoluteSize/$inSpanAmount);
    
    if(empty($inMaxItemSize) || ($inMaxItemSize>$inMaxAbsoluteSize)  ) 
    {
    $inMaxItemSize = $inMaxAbsoluteSize;
    }
    if($v>$inMaxItemSize) {$v=$inMaxItemSize;}
    return $v;
}

function filtre_calculeOffset($inSpanSize,$inBeforeAfter,$inSpanNum,$inSpanAmount,$inMaxAbsoluteSize)
{
    $totalSpaceLeft = $inMaxAbsoluteSize - $inSpanAmount * $inSpanSize;
    

    $offset = 0;     
    if($inSpanNum == 1 && strcmp($inBeforeAfter,"avant")==0 )
    {
        $offset = floor($totalSpaceLeft/2);
    }
    if($inSpanNum == $inSpanAmount && strcmp($inBeforeAfter,"apres")==0 )
    { $offset = ceil($totalSpaceLeft/2);
    }
      return $offset;
}


?>