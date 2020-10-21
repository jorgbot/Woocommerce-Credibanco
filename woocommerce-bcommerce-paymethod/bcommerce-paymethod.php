<?php
/*
Plugin Name: Botón de pago WooCommerce CREDIBANCO
Description: Metodo de pago de CREDIBANCO para CMS WooCommerce
Version: 1.0
Author: Credibanco
 */
add_action('plugins_loaded', 'woocommerce_bcommerce_paymethod_gateway', 0);
function woocommerce_bcommerce_paymethod_gateway()
{

    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    class WC_Bcommerce_Paymethod extends WC_Payment_Gateway
    {

        /**
         * Constructor de la pasarela de pago
         *
         * @access public
         * @return void
         */
        public function __construct()
        {
            $this->id                 = 'bcommerce';
            $this->icon               = apply_filters('woocomerce_bcommerce_icon', plugins_url('/img/logo.png', __FILE__));
            $this->has_fields         = false;
            $this->method_title       = 'Bcommerce';
            $this->method_description = 'Metodo de pago de CREDIBANCO';

            $this->init_form_fields();
            $this->init_settings();

            $this->title                  = $this->settings['title'];
            $this->nombre_establecimiento = $this->settings['nombre_establecimiento'];
            $this->commerce_id            = $this->settings['commerce_id'];
            //$this->api_key = $this->settings['api_key'];
            $this->terminal_id        = $this->settings['terminal_id'];
            $this->url                = $this->settings['url'];
            $this->redirect_page_id   = $this->settings['redirect_page_id'];
            $this->cod_unico_comercio = $this->settings['cod_unico_comercio'];
            $this->nit_comercio       = $this->settings['nit_comercio'];

            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
            } else {
                add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
            }
            add_action('woocommerce_receipt_payu', array(&$this, 'receipt_page'));
        }

        /**
         * Receipt Page
         **/
        function receipt_page($order)
        {
            echo '<p>' . __('Thank you for your order, please click the button below to pay.', 'bcommerce') . '</p>';
            echo $this->generate_bcommerce_form($order);
        }

        /**
         * Funcion que define los campos que iran en el formulario en la configuracion
         *
         * @access public
         * @return void
         */
        function init_form_fields()
        {
            $this->form_fields = array(
                'enabled'                => array(
                    'title'   => __('Habilitar/Deshabilitar', 'bcommerce_paymethod'),
                    'type'    => 'checkbox',
                    'label'   => __('Habilita la pasarela de pago', 'bcommerce_paymethod'),
                    'default' => 'no'),
                'title'                  => array(
                    'title'       => __('Título', 'bcommerce_paymethod'),
                    'type'        => 'text',
                    'description' => __('Título que el usuario verá durante checkout.', 'bcommerce_paymethod'),
                    'default'     => 'Bcommerce'),
                'nombre_establecimiento' => array(
                    'title'       => __('Nombre del Establecimiento', 'bcommerce_paymethod'),
                    'type'        => 'text',
                    'description' => __('Nombre del comercio', 'bcommerce_paymethod')),
                'commerce_id'            => array(
                    'title'       => __('Commerce ID', 'bcommerce_paymethod'),
                    'type'        => 'text',
                    'description' => __('ID proporcionado por CREDIBANCO', 'bcommerce_paymethod')),
                /*'api_key' => array(
                'title' => __('API Key', 'bcommerce_paymethod'),
                'type' => 'text',
                'description' => __('Llave proporcionada por WooCommerce', 'bcommerce_paymethod')),*/
                'terminal_id'            => array(
                    'title'       => __('Terminal', 'bcommerce_paymethod'),
                    'type'        => 'text',
                    'description' => __('Terminal proporcionada por CREDIBANCO', 'bcommerce_paymethod')),
                'url'                    => array(
                    'title' => __('URL', 'bcommerce_paymethod'),
                    'type'  => 'text',
                    'label' => __('Servicio Web Bcommerce', 'bcommerce_paymethod')),
                'cod_unico_comercio'     => array(
                    'title'       => __('Código Único', 'bcommerce_paymethod'),
                    'type'        => 'text',
                    'description' => __('Código único proporcionado por CREDIBANCO', 'bcommerce_paymethod')),
                'nit_comercio'           => array(
                    'title'       => __('NIT', 'bcommerce_paymethod'),
                    'type'        => 'text',
                    'description' => __('NIT del comercio', 'bcommerce_paymethod')),
                'redirect_page_id'       => array(
                    'title'       => __('Return Page'),
                    'type'        => 'select',
                    'options'     => $this->get_pages('Select Page'),
                    'description' => "URL of success page",
                ),
            );
        }

        /**
         * Muestra el fomrulario en el admin con los campos de configuracion
         *
         * @access public
         * @return void
         */
        public function admin_options()
        {
            echo '<h3>' . __('Configuración Botón de Pago CREDIBANCO', 'bcommerce_paymethod') . '</h3>';
            echo '<table class="form-table">';
            $this->generate_settings_html();
            echo '</table>';
        }

        /**
         * Genera botón de pagos Credibanco
         *
         * @access public
         * @return void
         */
        public function generate_bcommerce_form($order_id)
        {
            // WEB Service
            $response = "";
            try {
                $client   = new SoapClient($this->url);
                $response = $client->realizarpago($this->get_params_vpos($order_id));
            } catch (Exception $e) {
                $response = $e->getMessage();
                print_r($response);
                return "'<h3>'.__('Error', 'bcommerce_paymethod').'</h3>';";
            }
            return $response;
        }

        /**
         * Metodo que genera los botones, del botón de pago Credibanco
         *
         * @access public
         * @return void
         */
        public function get_params_vpos($order_id)
        {
            global $woocommerce;
            $customer = $woocommerce->customer;

            $adquiriente = "1";
            // numero del comercio
            $codcomerc = $this->commerce_id;
            // numero de la terminal de pago
            $numterminal = $this->terminal_id;
            $tmp         = strlen($numterminal);
            while ($tmp < 8) {
                $numterminal = '0' . $numterminal;
                $tmp         = $tmp + 1;
            }

            // Genero - WooCommerce no maneja Genero
            $genero = "F";

            $order  = new WC_Order($order_id);
            $amount = $order->get_total();
            $iva    = $order->get_total_tax();

            $ivareturn = $iva;

            $amount = number_format($amount, 2);
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '', $amount);
            $iva    = number_format($iva, 2);
            $iva    = str_replace('.', '', $iva);
            $iva    = str_replace(',', '', $iva);

            // direccion ip
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }
            // Reserva un número de Orden
            $numtx = $order->id;

            // direccion de facturacion del cliente
            $direccion = $order->billing_address_1;
            $ciudad    = $order->billing_city;
            $telefono  = $order->billing_phone;
            $pais      = $order->billing_country;
            $state     = $order->billing_state;
            $postcode  = $order->billing_postcode;
            $tmp       = strlen($telefono);
            while ($tmp < 10) {
                $telefono = '0' . $telefono;
                $tmp      = $tmp + 1;
            }
            $celular = ""; // WooCommerce no registra el celular
            if ($celular == "") {
                $celular = "000000000";
            }

            // direccion envio
            $nombre          = $order->shipping_first_name;
            $nombreEnvio     = $order->shipping_first_name;
            $apellidos       = $order->shipping_last_name;
            $apellidoEnvio   = $order->shipping_last_name;
            $direccion_envio = $order->shipping_address_1;
            $ciudad_envio    = $order->shipping_city;
            $pais_envio      = $order->shipping_country;
            $state_envio     = $order->shipping_state;
            $postcode_envio  = $order->shipping_postcode;
            $telefono_envio  = $order->shipping_phone;
            $celular_envio   = "";
            $nacionalidad    = "CO";

            $email = $order->billing_email;

            $data = array(
                'adquiriente'                 => '1',
                'transactionTrace'            => 'BC',
                'nacionalidad'                => $nacionalidad,
                'codigopais'                  => $pais,
                'numtx'                       => $numtx,
                'direccion'                   => $direccion,
                'ipaddress'                   => $ipaddress,
                'amount'                      => $amount,
                'tasaaero'                    => '',
                'iva'                         => $iva,
                'ivareturn'                   => $ivareturn,
                'nombres'                     => $nombre,
                'apellidos'                   => $apellidos,
                'numterminal'                 => $numterminal,
                'codcomerc'                   => $codcomerc,
                'email'                       => $email,
                'genero'                      => $genero,
                'ciudad'                      => $ciudad,
                'celular'                     => $celular,
                'telefono'                    => $telefono,
                'paisenvio'                   => $pais_envio,
                'ciudadenvio'                 => $ciudad_envio,
                'direccionenvio'              => $direccion_envio,
                'fingerprint'                 => $numtx,
                'postcode'                    => $postcode,
                'state'                       => $state,
                'observaciones'               => 'Comprobante de pago. Pedido # ' . $numtx,
                'monto'                       => $amount,
                'cuotas'                      => '001',
                'opcionales'                  => '',
                'passengerFirstName'          => '',
                'passengerLastName'           => '',
                'passengerDocumentType'       => '',
                'passengerDocumentNumber'     => '',
                'passengerAgencyCode'         => '',
                'airportCode'                 => '',
                'airportCity'                 => '',
                'airportCountry'              => '',
                'airportCodeLlegada'          => '',
                'airportCityLlegada'          => '',
                'airportCountryLlegada'       => '',
                'administrativeRateAmount'    => '',
                'administrativeRateIva'       => '',
                'administrativeRateIvaReturn' => '',
                'administrativeRateCode'      => '',
                'flightAirlineCode'           => '',
                'flightDepartureAirport'      => '',
                'flightArriveAirport'         => '',
                'flightDepartureDate'         => '',
                'flightDepartureTime'         => '',
                'flightArriveDate'            => '',
                'flightArriveTime'            => '',
                'flightReservation'           => '',
                'flightDepartureIata'         => '',
                'flightArriveIata'            => '',
                'propina'                     => '',
                'iac'                         => '',
                'domicilio'                   => '',
                'nombreEnvio'                 => $nombreEnvio,
                'apellidoEnvio'               => $apellidoEnvio,
                'postcodee'                   => $postcode_envio,
                'statee'                      => $state_envio,
            );

            return $data;
        }

        /**
         * Procesa el pago
         *
         * @access public
         * @return void
         */
        function process_payment($order_id)
        {
            global $woocommerce;
            $order = new WC_Order($order_id);

            // Mark as on-hold (we're awaiting the cheque)
            $order->update_status('on-hold', __('Awaiting cheque payment', 'woocommerce'));

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            $woocommerce->cart->empty_cart();
            // Genera boton de pagos CREDIBANCO
            $this->generate_bcommerce_form($order_id);

            return array(
                'result'   => 'success',
                'redirect' =>
                add_query_arg(
                    array(
                        'order'       => $order->id,
                        'url'         => $this->url,
                        'commerce_id' => $this->commerce_id,
                        'terminal_id' => $this->terminal_id,
                        'key'         => $order->order_key,
                    ),
                    get_permalink($this->redirect_page_id)
                ),
            );

            /*
        return array('result' => 'success', 'redirect' => add_query_arg('order',
        $order->id, add_query_arg('key', $order->order_key, get_permalink($this->redirect_page_id)))
        );
        /*
        return array(
        'result' => 'success',
        'redirect' => $this->get_return_url( $order )
        );
        /*
        return array(
        'result' => 'success',
        'redirect' =>  $order->get_checkout_payment_url( true )
        );
        /*

        // Return thankyou redirect
        return array(
        'result' => 'success',
        'redirect' => $this->url
        );
         */
        }

        function showMessage($content)
        {
            return '<div class="box ' . $this->msg['class'] . '-box">' . $this->msg['message'] . '</div>' . $content;
        }

        // get all pages
        function get_pages($title = false, $indent = true)
        {
            $wp_pages  = get_pages('sort_column=menu_order');
            $page_list = array();
            if ($title) {
                $page_list[] = $title;
            }

            foreach ($wp_pages as $page) {
                $prefix = '';
                // show indented child pages?
                if ($indent) {
                    $has_parent = $page->post_parent;
                    while ($has_parent) {
                        $prefix .= ' - ';
                        $next_page  = get_page($has_parent);
                        $has_parent = $next_page->post_parent;
                    }
                }
                // add to page list array array
                $page_list[$page->ID] = $prefix . $page->post_title;
            }
            return $page_list;
        }

    }

    /**
     * Ambas funciones son utilizadas para notifcar a WC la existencia del botón de pagos
     */
    function add_bcommerce_paymethod($methods)
    {
        $methods[] = 'WC_Bcommerce_Paymethod';
        return $methods;
    }
    add_filter('woocommerce_payment_gateways', 'add_bcommerce_paymethod');
}
