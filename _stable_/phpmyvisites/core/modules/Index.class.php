<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: Index.class.php,v 1.4 2005/10/09 19:10:50 matthieu_ Exp $

class Index extends Module 
{
    var $defaultAction = 'display';
    
    var $viewTemplate  = 'common/index.tpl';
    
    
    function indexModule()
    {
        parent::Module();
    }
    
}

?>