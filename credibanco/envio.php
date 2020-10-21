<html>
    <head>
        <title>CredibanCo</title>
    </head>
    <body>
        <div>
<h2 style="text-align: center;color: #777777;">Pronto será redireccionado al Vpos Credibanco</h2>
<h3 style="text-align: center;color: #777777;">Lo invitamos a permanecer un instante más</h3>
        <center><img src="https://www.recaudocredibanco.com/credibanco.jpeg" /></center>
    </body>
</html>
</body>
</html>

<?php
//error_reporting(0);
//error_reporting(E_ERROR);

include "vpos/beans/vpos_plugin.php";
include "vpos/conexion_cer.php";
include "vpos/conexion_i.php";


$_POST['billingNationality'] = "CO";
$_POST['purchaseAmount'] = utf8_encode($_POST['purchaseAmount']);
$_POST['reserved6'] = $_POST['shipping_total'];
$_POST['purchaseIva']    = utf8_encode($_POST['purchaseIva']); 


$decimales = explode(".", $_POST['purchaseAmount']); 
if (isset($decimales[1])) {
    $caracteres = array(".", ",");
    $resultado  = str_replace($caracteres, "", $_POST['purchaseAmount']); 
    $conversion = intval($resultado); 
    $monto      = $conversion;
} else {
    $input      = intval($_POST['purchaseAmount']); 
    $input      = number_format($_POST['purchaseAmount'], 2, ',', ''); 
    $caracteres = array(".", ",");
    $resultado  = str_replace($caracteres, "", $input); 
    $conversion = intval($resultado); 
    $monto      = $conversion;
}

$_POST['purchaseAmount']          = utf8_encode($monto);

$decimales            = explode(".", $_POST['purchaseIva']); 
if (isset($decimales[1])) {
    $caracteres = array(".", ",");
    $resultado  = str_replace($caracteres, "", $_POST['purchaseIva']); 
    $conversion = intval($resultado); 
    $iva        = $conversion;
} else {
    $input      = intval($_POST['purchaseIva']); 
    $input      = number_format($_POST['purchaseIva'], 2, ',', ''); 
    $caracteres = array(".", ",");
    $resultado  = str_replace($caracteres, "", $input); 
    $conversion = intval($resultado); 
    $iva        = $conversion;
}

$_POST['purchaseIva'] = utf8_encode($iva); // es el monto del impuesto


$_POST['purchaseCurrencyCode'] = utf8_encode("170"); //pesos
$_POST['purchasePlanId']       = utf8_encode("01"); //identificacion de plan de cobranza  dado por el adquiriente al comercio dato unico 01
$_POST['purchaseQuotaId']      = utf8_encode("012"); //cuotas de tdc si es otra trajeta usar 001

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


//TRABAJO ESPECIAL PARA ZIRUS (PORQUE TIENE NOMBRE Y APELLIDO JUNTOS)
$array = explode(" ", $_POST['billingFirstName']);
$_POST['billingFirstName']  = substr($array[0], 0, 30);
$_POST['billingLastName']   = substr($array[1], 0, 30);

if (empty($_POST['billingLastName'])) {

$_POST['billingLastName']   = "Apellido";

}



$_POST['billingCountry']    = utf8_decode(substr($_POST['billingCountry'], 0, 2));
$_POST['billingState']      = utf8_decode(substr($_POST['billingState'], 0, 30));
$_POST['billingCity']       = utf8_decode(substr($_POST['billingCity'], 0, 30));
$_POST['billingPostalCode'] = "12345";
$_POST['billingAddress'] = utf8_encode(substr($_POST['billingAddress'], 0, 50));


$_POST['billingEmail']           = utf8_encode(substr($_POST['billingEmail'], 0, 50));
$_POST['billingNationality']     = utf8_encode($_POST['billingNationality']);
$_POST['additionalObservations'] = utf8_encode(substr($_POST['additionalObservations'], 0, 50)); 


$_POST['reserved2'] = $_POST['reserved2']; 
$_POST['reserved3'] = $_POST['billingCelPhoneNumber'];
$_POST['reserved4'] = $_POST['billingEmail'];
$_POST['reserved5'] = $_POST['reserved5'];
$_POST['reserved6'] = $_POST['shipping_total']; //envio


$_POST['shippingReceiverName']       = $_POST['billingFirstName'];
$_POST['shippingReceiverLastName']   = $_POST['billingLastName'];
$_POST['shippingReceiverIdentifier'] = utf8_encode($_POST['reserved2']);



$_POST['shippingCountry'] = $_POST['billingCountry'];
$_POST['shippingCity']       = $_POST['billingState'];
$_POST['shippingAddress']    = $_POST['billingAddress'];
$_POST['shippingState']      = $_POST['billingState'];
$_POST['shippingPostalCode'] = $_POST['billingPostalCode'];

$_POST['billingPhoneNumber']    = utf8_encode($_POST['billingPhoneNumber']);
$_POST['billingCelPhoneNumber'] = utf8_encode($_POST['billingCelPhoneNumber']);


$_POST['shippingReceptionMethod'] = utf8_encode("ba");




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

$_POST['fingerPrint'] = $_POST['fingerPrint'];

if ($_POST) {
     $insertar = "INSERT INTO envio (purchaseOperationNumber, purchaseAmount, purchaseCurrencyCode,purchasePlanId,purchaseQuotaId,purchaseIpAddress,billingFirstName,billingLastName,billingCountry,billingCity,billingState,billingAddress,billingPhoneNumber,billingCelPhoneNumber,billingGender,billingEmail,billingNationality,additionalObservations,fingerPrint,repuesta, id_ref) VALUES (" . $correlativo . "," . $monto . "," . $_POST['purchaseCurrencyCode'] . "," . $_POST['purchasePlanId'] . "," . $_POST['purchaseQuotaId'] . ",'" . $_POST['purchaseIpAddress'] . "','" . $_POST['billingFirstName'] . "','" . $_POST['billingLastName'] . "','" . $_POST['billingCountry'] . "','" . $_POST['billingCity'] . "','" . $_POST['billingState'] . "','" . $_POST['billingAddress'] . "','" . $_POST['billingPhoneNumber'] . "','" . $_POST['billingCelPhoneNumber'] . "','" . $_POST['billingGender'] . "','" . $_POST['billingEmail'] . "','" . $_POST['billingNationality'] . "','" . $_POST['reserved4'] . "','" . $_POST['fingerPrint'] . "','0','" . $correlativo . "');";

    $resultado = mysqli_query($conexion, $insertar);

}

$_POST['purchaseOperationNumber'] = (string) $correlativo;


if ($_POST['additionalObservations'] == "") {
    $_POST['additionalObservations'] = $correlativo;
}

//requeridos
$array_send['acquirerId']           = utf8_encode($id_acquirer);
$array_send['commerceId']           = utf8_encode($id_commerce);
$array_send['purchaseTerminalCode'] = utf8_encode($terminal);
$array_send['purchaseLanguage']     = utf8_encode("SP");


$array_send['purchaseOperationNumber'] = utf8_encode($_POST['purchaseOperationNumber']);
$array_send['purchaseAmount']          = utf8_encode($_POST['purchaseAmount'] );

$array_send['purchaseIva'] = $_POST['purchaseIva'];

$array_send['purchaseCurrencyCode'] = utf8_encode($_POST['purchaseCurrencyCode']);
$array_send['purchasePlanId']       = utf8_encode($_POST['purchasePlanId']);
$array_send['purchaseQuotaId']      = utf8_encode($_POST['purchaseQuotaId']);
$array_send['purchaseIpAddress']    = utf8_encode($_POST['purchaseIpAddress']);

$array_send['billingFirstName']       = $_POST['billingFirstName'];
$array_send['billingLastName']        = $_POST['billingLastName'];
$array_send['billingCountry']         = utf8_encode($_POST['billingCountry']);
$array_send['billingCity']            = utf8_encode($_POST['billingCity']);
$array_send['billingState']           = utf8_encode($_POST['billingState']);
$array_send['billingAddress']         = utf8_encode($_POST['billingAddress']);
$array_send['billingPhoneNumber']     = utf8_encode($_POST['billingPhoneNumber']);
$array_send['billingCelPhoneNumber']  = utf8_encode($_POST['billingCelPhoneNumber']);
$array_send['billingGender']          = utf8_encode($_POST['billingGender']);
$array_send['billingEmail']           = utf8_encode($_POST['billingEmail']);
$array_send['billingNationality']     = utf8_encode($_POST['billingNationality']);
$array_send['additionalObservations'] = utf8_encode($_POST['additionalObservations']);
$array_send['fingerPrint']            = utf8_encode($_POST['fingerPrint']);
$array_send['billingPostalCode']      = utf8_encode($_POST['billingPostalCode']);

$array_send['shippingCountry'] = utf8_encode($_POST['shippingCountry']);

$array_send['reserved4'] = utf8_encode(substr($_POST['reserved4'], 0, 30));
$array_send['reserved2'] = utf8_encode(substr($_POST['reserved2'], 0, 30));
$array_send['reserved3'] = utf8_encode(substr($_POST['reserved3'], 0, 30));
$array_send['reserved5'] = utf8_encode(substr($_POST['reserved5'], 0, 30));
$array_send['reserved6'] = utf8_encode(substr($_POST['reserved6'], 0, 30));
$array_send['reserved7'] = utf8_encode(substr($_POST['reserved7'], 0, 30));

$array_send['shippingReceiverName']       = $_POST['shippingReceiverName'];
$array_send['shippingReceiverLastName']   = $_POST['billingLastName'];
$array_send['shippingReceiverIdentifier'] = $_POST['shippingReceiverIdentifier'];
$array_send['shippingReceptionMethod']    = utf8_encode($_POST['shippingReceptionMethod']);

$array_send['shippingCountry'] = utf8_encode($_POST['shippingCountry']);
$array_send['shippingCity']    = utf8_encode($_POST['shippingCity']);
$array_send['shippingAddress'] = utf8_encode($_POST['shippingAddress']);
$array_send['shippingState']   = utf8_encode($_POST['shippingState']);

$array_send['shippingPostalCode'] = utf8_encode($_POST['shippingPostalCode']);


$array_get['XMLREQ']      = utf8_encode("");
$array_get['DIGITALSIGN'] = utf8_encode("");
$array_get['SESSIONKEY']  = utf8_encode("");

//print_r($array_send);
VPOSSend($array_send, $array_get, $llaveVPOSCryptoPub, $llaveComercioFirmaPriv, $VI);
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<form name="frmSolicitudPago" method="post" action="https://ecommerce.credibanco.com/vpos2/MM/transactionStart20.do">
    <input TYPE="hidden" NAME="IDACQUIRER" value="<?php echo $id_acquirer; ?>">
    <input TYPE="hidden" NAME="IDCOMMERCE" value="<?php echo $id_commerce; ?>">
    <input TYPE="hidden" NAME="TERMINALCODE" value="<?php echo $terminal; ?>">
    <input TYPE="hidden" NAME="XMLREQ" value="<?php echo $array_get['XMLREQ']; ?>">
    <input TYPE="hidden" NAME="DIGITALSIGN" value="<?php echo $array_get['DIGITALSIGN']; ?>">
    <input TYPE="hidden" NAME="SESSIONKEY" value="<?php echo $array_get['SESSIONKEY']; ?>">
    <input type="hidden"  name="enviar">
</form>
<script type="text/javascript">
   document.frmSolicitudPago.submit();
</script>


