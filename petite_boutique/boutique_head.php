<?php
	
	function boutique_insert_head($flux){
	$flux .= "<script type='text/javascript' src='".find_in_path('js/jquery-1.3.2.min.js"')."'></script>\n";
	
	$flux .= "<script type='text/javascript' src='".find_in_path('js/iZoom.js')."'></script>";
	$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('css/loupe.css')).'" />';
	$flux .="<script type='text/javascript'>
 $('document').ready(function(){
                $('#myPic1').iZoom({diameter:200, borderColor:'grey', borderWidth:0});
            
            });

</script>";
 $js = '<script src=\''.url_absolue(find_in_path('simpleCart.js')).'\' type=\'text/javascript\'></script>';
            $js .= '<script type="text/javascript">';
            
            $js .= lire_config('boutique/cart_headers') ? 'simpleCart.cartHeaders ='.lire_config('boutique/cart_headers').';': '';
            $js .= lire_config('boutique/tax_rate') ? 'simpleCart.taxRate ="'.lire_config('boutique/tax_rate').'";': '';
            $js .= lire_config('boutique/shipping_flat_rate') ? 'simpleCart.shippingFlatRate ="'.lire_config('boutique/shipping_flat_rate').'";': '';
            $js .= lire_config('boutique/shipping_quantity_rate') ? 'simpleCart.shippingQuantityRate ="'.lire_config('boutique/shipping_quantity_rate').'";': '';
            $js .= lire_config('boutique/shipping_total_rate') ? 'simpleCart.shippingTotalRate ="'.lire_config('boutique/shipping_total_rate').'";': '';
            $js .= lire_config('boutique/ok_url') ? 'simpleCart.okUrl="'.lire_config('boutique/ok_url').'";': '';
            $js .= lire_config('boutique/error_url') ? 'simpleCart.errorUrl="'.lire_config('boutique/error_url').'";': '';
            $js .= lire_config('boutique/pending_url') ? 'simpleCart.pendingUrl="'.lire_config('boutique/pending_url').'";': '';


            if( lire_config('boutique/method_paypal')) {
                $js .= lire_config('boutique/paypal_account') ? 'simpleCart.email="'.lire_config('boutique/paypal_account').'";': '';
            }

            if( lire_config('boutique/method_googlecheckout')) {
                $js .= lire_config('boutique/google_mechant_id') ? 'simpleCart.merchantId="'.lire_config('boutique/google_merchant_id').'";': '';
            }
            
                       
            $js .=  '</script>';

            $css = '<link rel="stylesheet" href="'. url_absolue(find_in_path('css/simplecart.css')).'" type="text/css" media="all" />';


        if (strpos($stream,'<head')!==FALSE)
            return preg_replace('/(<head[^>]*>)/i', "\n\$1".$js.$css, $stream, 1);
        else 
return $flux.$js.$css;
}

?>