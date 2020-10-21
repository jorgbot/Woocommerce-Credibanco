<?php

include "vpos/beans/vpos_plugin.php";
include "vpos/conexion_cer.php";
include "vpos/conexion_i.php";

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/Bogota');
//post
$_POST['purchaseAmount'] = 1000; //10000.00 sin puntos es decir 1000000
$decimales               = explode(".", $_POST['purchaseAmount']); //esto es ver si tiene decimales
if (isset($decimales[1])) {
    $caracteres = array(".", ",");
    $resultado  = str_replace($caracteres, "", $_POST['purchaseAmount']); //esto es quitarle las decimales
    $conversion = intval($resultado); // esto es conversion de string a numero
    $monto      = $conversion;
} else {
    $input      = intval($_POST['purchaseAmount']); // esto es conversion de string a numero
    $input      = number_format($_POST['purchaseAmount'], 2, ',', ''); //esto es agregarle las decimales;
    $caracteres = array(".", ",");
    $resultado  = str_replace($caracteres, "", $input); //esto es quitarle las decimales;
    $conversion = intval($resultado); // esto es conversion de string a numero
    $monto      = $conversion;
}

$_POST['purchaseCurrencyCode'] = "170"; //pesos
$_POST['purchasePlanId']       = "01"; //identificacion de plan de cobranza  dado por el adquiriente al comercio dato unico 01
$_POST['purchaseQuotaId']      = "012"; //cuotas de tdc si es otra trajeta usar 001

function get_real_ip()
{

    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    } else {
        return $_SERVER["REMOTE_ADDR"];
    }

}
$ip = get_real_ip();

$_POST['purchaseIpAddress'] = $ip;
$_POST['purchaseIpAddress'] = "172.26.4.91"; //ip del comprador//172.19.206.1 //edpru //ej pru 172.26.4.91

$_POST['billingFirstName']  = "angel";
$_POST['billingLastName']   = "moran";
$_POST['billingCountry']    = "CO";
$_POST['billingState']      = "CO";
$_POST['billingCity']       = "CO";
$_POST['billingPostalCode'] = "080001";

$_POST['billingGender']          = "M";
$_POST['billingEmail']           = "angel.javier01@gmail.com";
$_POST['billingNationality']     = "CO";
$_POST['additionalObservations'] = substr("esto es un observacion", 0, 50); //observacion adicional de la compra
$_POST['fingerPrint']            = "01";
$_POST['reserved2']              = "21164317"; //docuemnto
$_POST['reserved3']              = "reserved3";
$_POST['reserved4']              = "reserved4";
$costo_neto                      = $_POST['purchaseAmount'] - 200;

$_POST['reserved5']      = $costo_neto;
$_POST['billingAddress'] = "CO";

$_POST['shippingReceiverName']       = $_POST['billingFirstName'];
$_POST['shippingReceiverLastName']   = $_POST['billingLastName'];
$_POST['shippingReceiverIdentifier'] = "21164317";

$_POST['shippingCountry'] = $_POST['billingCountry'];

$_POST['shippingCity']       = $_POST['billingCity'];
$_POST['shippingAddress']    = $_POST['billingAddress'];
$_POST['shippingState']      = $_POST['billingState'];
$_POST['shippingPostalCode'] = $_POST['billingPostalCode'];

//lo que no estan llegando, creo que es opcional

if (empty($_POST['billingPhoneNumber'])) {

    $_POST['billingPhoneNumber']    = "3194836874";
    $_POST['billingCelPhoneNumber'] = "3194836874";
} else {

    $_POST['billingPhoneNumber']    = $_POST['billingPhoneNumber'];
    $_POST['billingCelPhoneNumber'] = $_POST['billingCelPhoneNumber'];
}

if (empty($_POST['billingPostalCode'])) {

    $_POST['billingPostalCode'] = "100001";
} else {

    $_POST['billingPostalCode'] = $_POST['billingPostalCode'];

}

if (empty($_POST['reserved3'])) {

    $_POST['reserved3'] = "100001";
} else {

    $_POST['reserved3'] = $_POST['reserved3'];

}

if (empty($_POST['shippingReceptionMethod'])) {

    $_POST['shippingReceptionMethod'] = "ba";
} else {

    $_POST['shippingReceptionMethod'] = $_POST['shippingReceptionMethod'];

}

if (empty($_POST['shippingPostalCode'])) {

    $_POST['shippingPostalCode'] = "10001";
} else {

    $_POST['shippingPostalCode'] = $_POST['shippingPostalCode'];

}

//post

//server

function Conectarse()
{
    global $host, $puerto, $usuario, $contrasena, $baseDeDatos;
    $link = mysqli_connect($host, $usuario, $contrasena);

    if (!($link)) {
        exit();
    } else {
    }
    if (!mysqli_select_db($link, $baseDeDatos)) {
        exit();
    } else {
    }
    return $link;
}


$conexion = Conectarse();
$consulta = "Select max(purchaseOperationNumber) as correlativo  from envio";
$resultado = mysqli_query($conexion, $consulta);
$fila      = mysqli_fetch_array($resultado);
$inicial   = 1000;
$total     = mysqli_num_rows($resultado);

if ($total == 0) {
    $correlativo = $inicial;
} else {
    $correlativo = $fila['correlativo'];
    $correlativo++;
}
$_POST['fingerPrint'] = "01";

//insertamos en la base de datos el registro
//if($_POST){
  
     $insertar = "INSERT INTO envio (purchaseOperationNumber, purchaseAmount, purchaseCurrencyCode,purchasePlanId,purchaseQuotaId,purchaseIpAddress,billingFirstName,billingLastName,billingCountry,billingCity,billingState,billingAddress,billingPhoneNumber,billingCelPhoneNumber,billingGender,billingEmail,billingNationality,additionalObservations,fingerPrint,id_ref,repuesta) VALUES (" . $correlativo . "," . $monto . "," . $_POST['purchaseCurrencyCode'] . "," . $_POST['purchasePlanId'] . "," . $_POST['purchaseQuotaId'] . ",'" . $_POST['purchaseIpAddress'] . "','" . $_POST['billingFirstName'] . "','" . $_POST['billingLastName'] . "','" . $_POST['billingCountry'] . "','" . $_POST['billingCity'] . "','" . $_POST['billingState'] . "','" . $_POST['billingAddress'] . "','" . $_POST['billingPhoneNumber'] . "','" . $_POST['billingCelPhoneNumber'] . "','" . $_POST['billingGender'] . "','" . $_POST['billingEmail'] . "','" . $_POST['billingNationality'] . "','" . $_POST['reserved4'] . "','" . $_POST['fingerPrint'] . "','" . $correlativo . "','0');";

    $resultado = mysqli_query($conexion, $insertar);



//}

//convertimo el correlativo a alfanumerico
$_POST['purchaseOperationNumber'] = (string) $correlativo;
//server

//requeridos
$array_send['acquirerId']           = $id_acquirer;
$array_send['acquirerId']           = $id_acquirer;
$array_send['commerceId']           = $id_commerce;
$array_send['purchaseTerminalCode'] = $terminal;
$array_send['purchaseLanguage']     = "SP";

//COMENTAR ESTE BLOQUE CUANDO SE ENVIE DESDE EL FORM PRINCIPAL
$array_send['purchaseOperationNumber'] = $_POST['purchaseOperationNumber'];
$array_send['purchaseAmount']          = $monto;
$array_send['purchaseCurrencyCode']    = $_POST['purchaseCurrencyCode'];
$array_send['purchasePlanId']          = $_POST['purchasePlanId'];
$array_send['purchaseQuotaId']         = $_POST['purchaseQuotaId'];
$array_send['purchaseIpAddress']       = $_POST['purchaseIpAddress'];

$array_send['billingFirstName']       = $_POST['billingFirstName'];
$array_send['billingLastName']        = $_POST['billingLastName'];
$array_send['billingCountry']         = $_POST['billingCountry'];
$array_send['billingCity']            = $_POST['billingCity'];
$array_send['billingState']           = $_POST['billingState'];
$array_send['billingAddress']         = $_POST['billingAddress'];
$array_send['billingPhoneNumber']     = $_POST['billingPhoneNumber'];
$array_send['billingCelPhoneNumber']  = $_POST['billingCelPhoneNumber'];
$array_send['billingGender']          = $_POST['billingGender'];
$array_send['billingEmail']           = $_POST['billingEmail'];
$array_send['billingNationality']     = $_POST['billingNationality'];
$array_send['additionalObservations'] = $_POST['additionalObservations'];
$array_send['fingerPrint']            = $_POST['fingerPrint'];
$array_send['billingPostalCode']      = $_POST['billingPostalCode'];

$array_send['shippingCountry'] = $_POST['shippingCountry'];

$array_send['reserved4'] = $_POST['reserved4'];
$array_send['reserved2'] = $_POST['reserved2'];
$array_send['reserved3'] = $_POST['reserved3'];
$array_send['reserved5'] = $_POST['reserved5'];

$array_send['shippingReceiverName']       = $_POST['shippingReceiverName'];
$array_send['shippingReceiverLastName']   = $_POST['shippingReceiverLastName'];
$array_send['shippingReceiverIdentifier'] = $_POST['shippingReceiverIdentifier'];
$array_send['shippingReceptionMethod']    = $_POST['shippingReceptionMethod'];

$array_send['shippingCountry'] = $_POST['shippingCountry'];
$array_send['shippingCity']    = $_POST['shippingCity'];
$array_send['shippingAddress'] = $_POST['shippingAddress'];
$array_send['shippingState']   = $_POST['shippingState'];

$array_send['shippingPostalCode'] = $_POST['shippingPostalCode'];

//COMENTAR ESTE BLOQUE CUANDO SE ENVIE DESDE EL FORM PRINCIPAL

//auto seteo de arreglo de cadenas con los parámetros devueltos por el componente mediante puntero
$array_get['XMLREQ']      = "";
$array_get['DIGITALSIGN'] = "";
$array_get['SESSIONKEY']  = "";

print_r($array_send);
 
VPOSSend($array_send, $array_get, $llaveVPOSCryptoPub, $llaveComercioFirmaPriv, $VI);
?>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
    </head>
    <body>
        <form name="frmSolicitudPago" method="post" action="https://ecommerce.credibanco.com/vpos2/MM/transactionStart20.do">
            <input TYPE="hidden" NAME="IDACQUIRER" value="<?php echo $id_acquirer; ?>">
            <input TYPE="hidden" NAME="IDCOMMERCE" value="<?php echo $id_commerce; ?>">
            <input TYPE="hidden" NAME="TERMINALCODE" value="<?php echo $terminal; ?>">
            <input TYPE="hidden" NAME="XMLREQ" value="<?php echo $array_get['XMLREQ']; ?>">
            <input TYPE="hidden" NAME="DIGITALSIGN" value="<?php echo $array_get['DIGITALSIGN']; ?>">
            <input TYPE="hidden" NAME="SESSIONKEY" value="<?php echo $array_get['SESSIONKEY']; ?>">
            <input type="submit"  name="enviar">
        </form>
        <script type="text/javascript">
          //document.frmSolicitudPago.submit();
        </script>
    </body>
</html>