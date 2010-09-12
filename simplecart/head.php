<?php
include_spip('inc/utils');


    function simplecart_insert_head($stream){
            $js = '<script src=\''.url_absolue(find_in_path('simpleCart.js')).'\' type=\'text/javascript\'></script>';
            $js .= '<script type="text/javascript">
                    simpleCart.email = "'. lire_config('simplecart/paypal_account', 'gaitan@gmail.com') .'";';
            if (lire_config('simplecart/google_merchant_id')) {
                        $js .= 'simpleCart.merchantId = "'. lire_config('simplecart/google_merchant_id') .'";';
            }
            if (lire_config('simplecart/cart_headers')) {
                        $js .= 'simpleCart.cartHeaders = '. lire_config('simplecart/cart_headers') .' ;';
            }
            
            if (lire_config('simplecart/tax_rate')) {
                        $js .= 'simpleCart.taxRate = "'. lire_config('simplecart/tax_rate') .'";';
            }
            if (lire_config('simplecart/shipping_flat_rate')) {
                        $js .= 'simpleCart.shippingFlatRate = "'. lire_config('simplecart/shipping_flat_rate') .'";';
            }
            if (lire_config('simplecart/shipping_quantity_rate')) {
                        $js .= 'simpleCart.shippingQuantityRate = "'. lire_config('simplecart/shipping_quantity_rate') .'";';
            }
            if (lire_config('simplecart/shipping_total_rate')) {
                        $js .= 'simpleCart.shippingTotalRate = "'. lire_config('simplecart/shipping_total_rate') .'";';
            }
            $js .=  '</script>';

            $css = '<link rel="stylesheet" href="'. url_absolue(find_in_path('css/simplecart.css')).'" type="text/css" media="all" />';


        if (strpos($stream,'<head')!==FALSE)
            return preg_replace('/(<head[^>]*>)/i', "\n\$1".$js.$css, $stream, 1);
        else 
            return $stream.$js;
    }

?>
