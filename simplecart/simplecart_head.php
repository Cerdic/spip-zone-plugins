<?php
include_spip('inc/utils');


    function simplecart_insert_head($stream){
            $js = '<script src=\''.url_absolue(find_in_path('simpleCart.js')).'\' type=\'text/javascript\'></script>';
            $js .= '<script type="text/javascript">';
            
            $js .= lire_config('simplecart/cart_headers') ? 'simpleCart.cartHeaders ='.lire_config('simplecart/cart_headers').';': '';
            $js .= lire_config('simplecart/tax_rate') ? 'simpleCart.taxRate ="'.lire_config('simplecart/tax_rate').'";': '';
            $js .= lire_config('simplecart/shipping_flat_rate') ? 'simpleCart.shippingFlatRate ="'.lire_config('simplecart/shipping_flat_rate').'";': '';
            $js .= lire_config('simplecart/shipping_quantity_rate') ? 'simpleCart.shippingQuantityRate ="'.lire_config('simplecart/shipping_quantity_rate').'";': '';
            $js .= lire_config('simplecart/shipping_total_rate') ? 'simpleCart.shippingTotalRate ="'.lire_config('simplecart/shipping_total_rate').'";': '';
            $js .= lire_config('simplecart/ok_url') ? 'simpleCart.okUrl="'.lire_config('simplecart/ok_url').'";': '';
            $js .= lire_config('simplecart/error_url') ? 'simpleCart.errorUrl="'.lire_config('simplecart/error_url').'";': '';
            $js .= lire_config('simplecart/pending_url') ? 'simpleCart.pendingUrl="'.lire_config('simplecart/pending_url').'";': '';


            if( lire_config('simplecart/method_paypal')) {
                $js .= lire_config('simplecart/paypal_account') ? 'simpleCart.email="'.lire_config('simplecart/paypal_account').'";': '';
            }

            if( lire_config('simplecart/method_googlecheckout')) {
                $js .= lire_config('simplecart/google_mechant_id') ? 'simpleCart.merchantId="'.lire_config('simplecart/google_merchant_id').'";': '';
            }
            
            if( lire_config('simplecart/method_dineromail')) {
                $js .= lire_config('simplecart/dineromail_merchant_id') ? 'simpleCart.dmMerchantId="'.lire_config('simplecart/dineromail_merchant_id').'";': '';
                $js .= lire_config('simplecart/dineromail_country_id') ? 'simpleCart.dmCountryId='.lire_config('simplecart/dineromail_country_id').';': '';
                $js .= lire_config('simplecart/dineromail_currency') == 2 ? 'simpleCart.dmCurrency=USD;': '';
                $js .= lire_config('simplecart/dineromail_seller_name') ? 'simpleCart.dmSellerName="'.lire_config('simplecart/dineromail_seller_name').'";': '';
                $js .= lire_config('simplecart/dineromail_header_image') ? 'simpleCart.dmHeaderImage="'.lire_config('simplecart/dineromail_header_image').'";': '';             
            }else{
            $js .= lire_config('simplecart/devise_id') ? 'simpleCart.currency="'.lire_config('simplecart/devise_id').'";': '';             
            }
            
            $js .=  '</script>';

            $css = '<link rel="stylesheet" href="'. url_absolue(find_in_path('css/simplecart.css')).'" type="text/css" media="all" />';


        if (strpos($stream,'<head')!==FALSE)
            return preg_replace('/(<head[^>]*>)/i', "\n\$1".$js.$css, $stream, 1);
        else 
            return $stream.$js.$css;
    }

?>
