<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/simplecart/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'add_to_cart' => 'Agregar al carrito',

	// B
	'buy' => 'Comprar',

	// C
	'c_dineromail' => 'DineroMail',
	'c_email' => 'Email',
	'c_google_checkout' => 'Google Checkout',
	'c_paypal' => 'PayPal',
	'cart_configuration' => 'Configuración del carrito',
	'cart_headers' => 'Cabeceras del carrito',
	'cart_headers_explication' => 'Puedes editar los campos que el carrito muestra. Lee <a href="http://simplecartjs.com/documentation.html">Cart formatting configuration page</a> para detalles',
	'checkout' => 'Finalizar la compra',
	'checkout2email' => 'Enviar pedido por email',
	'checkout2email_explication' => 'El pedido se envia a la siguiente dirección de correo. No se utiliza un medio de pago.',
	'checkout_methods' => 'Medios de pago',
	'checkout_to' => 'Pagar a través de',

	// D
	'description' => 'Un carrito de compras simple en javascript',
	'devise' => 'Moneda', # NEW
	'devise_choix' => 'Elección de la moneda', # NEW
	'devise_explication' => 'La moneda que se utilizará para el pago (Compruebe la compatibilidad antes de m&eacute;todo de pago)',
	'dineromail_country' => 'País',
	'dineromail_country_argentine' => 'Argentina',
	'dineromail_country_brazil' => 'Brasil',
	'dineromail_country_chile' => 'Chile',
	'dineromail_country_explication' => 'Seleccione el país donde registró su cuenta DineroMail.',
	'dineromail_country_mexico' => 'México',
	'dineromail_currency' => 'Moneda de la transacción',
	'dineromail_currency_explication' => 'Tipo de moneda de la transacción. No hay conversión de tipo. Los precios deben indicarse en la moneda seleccionada',
	'dineromail_currency_local' => 'Moneda local',
	'dineromail_currency_usd' => 'Dólares estadounidenses',
	'dineromail_header_image' => 'Imagen de cabecera',
	'dineromail_header_image_explication' => 'URL absoluta del logo a mostrar en la cabecera de DineroMail (jpg o gif, 150px x 50px)',
	'dineromail_merchant_id' => 'Número de cuenta',
	'dineromail_merchant_id_explication' => 'Número de identificación de tu cuenta DineroMail sin el dígito verificador',
	'dineromail_payments_methods' => 'Medios de pago en DineroMail',
	'dineromail_payments_methods_explication' => 'Cadena de texto que define los métodos de pago permitidos. Dejar en blanco para habilitar todos los disponibles en el país.',
	'dineromail_see' => 'Ver',
	'dineromail_seller_name' => 'Nombre del vendedor',
	'dineromail_seller_name_explication' => 'Leyenda que el vendedor quiere mostrar en el encabezado. Dejar en blanco para mostrar el email asociado a la cuenta.',

	// E
	'empty' => 'Vaciar',
	'error_url' => 'URL compra erronea',
	'error_url_explication' => 'URL donde se re direcciona al comprador en caso de transacción errónea',

	// F
	'final_total' => 'Total',

	// G
	'google_merchant_id' => 'Identificador Merchant ID',
	'google_merchant_id_explication' => 'Número de identificación de tu cuenta Google Merchant',

	// H
	'header_name' => 'Nombre',
	'header_price' => 'Precio',
	'header_quantity' => 'Cantidad',
	'header_total' => 'Total',

	// O
	'ok_url' => 'URL compra exitosa',
	'ok_url_explication' => 'URL donde se redirecciona al comprador en caso de transacción exitosa',
	'other_parameters' => 'Otros parámetros',

	// P
	'paypal_account' => 'Cuenta PayPal',
	'paypal_account_explication' => 'Si tienes una cuenta PayPal, ingresa el email de tu cuenta',
	'pending_url' => 'URL compra pendiente',
	'pending_url_explication' => 'URL donde se re direcciona al comprador en caso de transacción pendiente.',

	// S
	'shipping_cost' => 'Costo de envío',
	'shipping_flat_rate' => 'Tasa de envío fija',
	'shipping_flat_rate_explication' => 'Agrega una tasa fija a la orden completa',
	'shipping_quantity_rate' => 'Costo de envío por cantidad',
	'shipping_quantity_rate_explication' => 'Agrega un monto fijo por cada item en el carrito',
	'shipping_total_rate' => 'Costo de envío como porcentaje del total',
	'shipping_total_rate_explication' => 'Agrega un costo de envío proporcional al costo de la orden',
	'subtotal' => 'Subtotal',

	// T
	'tax_and_shipping' => 'Impuestos y costo de envío',
	'tax_cost' => 'Impuestos',
	'tax_rate' => 'Tasa impositiva',
	'tax_rate_explication' => 'Tasa de impuestos. Ejemplo: 0.19 de Impuesto al Valor Agregado',
	'title' => 'SimpleCart',

	// Y
	'your_cart' => 'Tu carrito'
);

?>
