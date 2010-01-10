<?php

// Gravatar
function gravatar_url($email = '')
{
   if ($email != '') {
       return 'http://www.gravatar.com/avatar.php?gravatar_id='.md5($email).'&amp;size=42&amp;rating=PG';
   } else {
       return '';
   }
}

?>