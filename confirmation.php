<?php
/*
Template Name: bcommerce
*/

bCommercePayment::initContent();

class bCommercePayment{
    
    public static function initContent(){
        $order          = new WC_Order( $_GET["order"] );
        //print_r($order);
        $shipping_total = $order->shipping_total; // es el monto del envio
        $cart_tax = $order->cart_tax;//es el impuesto
        $amount     = $order->get_total();//es el monto total
        $subtotal   = $order->total;//es el monto total
        $apellidos  = $order->billing_last_name;
        $celular    = $order->billing_phone;
        $ciudad     = $order->billing_city;
        $direccion  = $order->billing_address_1;
        $email      = $order->billing_email;
        $nombre     = $order->billing_first_name;
        $genero     = 'M';
        $nacionalidad=$order->billing_country;
        $estado     = $order->billing_state;
        $pais       = "CO";
        $telefono   = $order->billing_phone;
        $codigopostal=$order->billing_postcode;
        $iva = $order->cart_tax;

        $nro_referencia=$order->id; //numero de pedido

        $purchaseCurrencyCode= "170";           //pesos
        $purchasePlanId     = "01";             //identificacion de plan de cobranza  dado por el adquiriente al comercio dato unico 01
        $purchaseQuotaId    = "012";            //cuotas de tdc si es otra trajeta usar 001
        
        if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) {
            $purchaseIpAddress = $_SERVER ['HTTP_CLIENT_IP'];
        } elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
            $purchaseIpAddress = $_SERVER ['HTTP_X_FORWARDED_FOR'];
        } else {
            $purchaseIpAddress = $_SERVER ['REMOTE_ADDR'];
        }
        // $purchaseIpAddress   = "172.26.4.91";    //ip del comprador//172.19.206.1 //edpru //ej pru 172.26.4.91

        $billingFirstName   = $nombre;
        $billingLastName    = $apellidos;
        $billingCountry     = $pais;
        $billingCity        = $ciudad;
        $purchaseIva = $iva;
        
        $billingState       = $estado;

        $billingAddress     = $direccion;
        $billingPhoneNumber = $celular;
        $billingCelPhoneNumber=$celular;
        $billingGender      = $genero;  
        $billingEmail       = $email;  
        $billingNationality = $nacionalidad;
        
        $additionalObservations="Compra de Articulos";  //observacion adicional de la compra
        $fingerPrint        = "01";
        $reserved2          = $nro_referencia;          //docuemnto cambiar esto
        $reserved3          = $celular;
        $reserved4          = $email;
        $reserved5          = substr("compra articulos", 0, 20);
        $reserved6          = $subtotal;
        $reserved7          = $order->order_number;//id para actualizar la tabla


        echo "<form name='form' method='post' action='https://zirus.pizza/credibanco/envio.php'>
                <input type='hidden' name='acquirerId' value='1'/>
                <input type='hidden' name='commerceId' value='".$_GET["commerce_id"]."'/>
                <input type='hidden' name='purchaseOperationNumber' value='".$_GET["order"]."'/>
                <input type='hidden' name='purchaseTerminalCode' value='".$_GET["terminal_id"]."'/>
                
                <br /><br />

                <input type='hidden' name='purchaseAmount' value='".$amount."'/>
                <input type='hidden' name='purchaseIva' value='".$purchaseIva."'/>
                <input type='hidden' name='shipping_total' value='".$shipping_total."'/>

                <input type='hidden' name='purchaseCurrencyCode' value='".$purchaseCurrencyCode."'/>
                <input type='hidden' name='purchasePlanId' value='".$purchasePlanId."'/>
                <input type='hidden' name='purchaseQuotaId' value='".$purchaseQuotaId."'/>
                <input type='hidden' name='purchaseIpAddress' 10value='".$purchaseIpAddress."'/>
                <input type='hidden' name='purchaseLanguage' value='SP'/>

                <br /><br />
                
                <input type='hidden' name='billingFirstName' value='".$billingFirstName."'/>
                <input type='hidden' name='billingLastName' value='".$billingLastName."'/>
                <input type='hidden' name='billingCountry' value='".$billingCountry."'/>
                <input type='hidden' name='billingCity' value='".$billingCity."'/>
                <input type='hidden' name='billingState' value='".$billingState."'/>
                <input type='hidden' name='billingPostalCode' value='".$codigopostal."'/>

                <br /><br />
                
                <input type='hidden' name='billingAddress' value='".$billingAddress."'/>
                <input type='hidden' name='billingPhoneNumber' value='".$billingPhoneNumber."'/>
                <input type='hidden' name='billingCelPhoneNumber' value='".$billingCelPhoneNumber."'/>
                <input type='hidden' name='billingGender' value='".$billingGender."'/>
                <input type='hidden' name='billingEmail' value='".$billingEmail."'/>
                <input type='hidden' name='billingNationality' value='".$billingNationality."'/>
                <input type='hidden' name='additionalObservations' value='".$additionalObservations."'/>
                <input type='hidden' name='fingerPrint' value='".$fingerPrint."'/>
                
                <br /><br />
                
                <input type='hidden' name='reserved2' value='".$reserved2."'/>
                <input type='hidden' name='reserved3' value='".$reserved3."'/>
                <input type='hidden' name='reserved4' value='".$reserved4."'/>
                <input type='hidden' name='reserved5' value='".$reserved5."'/>
                <input type='hidden' name='reserved6' value='".$reserved6."'/>
                <input type='hidden' name='reserved7' value='".$reserved7."'/>

        </form>
        ";

        echo "<script language='javascript'> document.form.submit(); </script>";
    }
}
?>