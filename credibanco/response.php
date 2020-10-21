<?php
///////Configuraciones/////////
$url_comercio = "https://zirus.pizza";
$pre          = "wpei_";
$comercio     = "ZIRUS PIZZA";
///////Configuraciones/////////
?>
<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
        <title>Recibo de Pago - Credibanco</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="vpos/bootstrap/css/bootstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="vpos/assets/signup-form.css" type="text/css" />

<link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="vpos/images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="vpos/images/favicon-16x16.png">
        <link rel="shortcut icon" href="vpos/images/favicon.ico">
    </head>

<style type="text/css" media="print">
@media print {
#parte2 {display:none;}
#impri {display:none;}
}
</style>

    <body>
        <div class="container">
            <div class="signup-form-container" id="printarea">
                <!-- form start -->
                <div class="form-header">
                    <img id="logo" src="vpos/images/logo-Co-01.png">

                    <img id="logoa" src="vpos/images/logo_comercio.png">
                </div>

                <div class="form-header">
                    <h3 class="form-title titulo"><span class="glyphicon glyphicon-home titulo"> &nbsp </span><i class="fa fa-user"></i>RECIBO ELECTRONICO DE TRANSACCION</h3><br>
                    <p class="col-center">VENTA NO PRESENCIAL</p>
                    <br><p class="text-justify">PAGARE INCONDICIONALMENTE Y A LA ORDEN DEL ACREEDOR, EL VALOR TOTAL DE ESTE PAGARE JUNTO CON LOS INTERESES A LAS TASAS MAXIMA PERMITIDAS POR LA LEY</p>



                </div>

                <div class="form-body">
                    <div class="form-group">
                        <div class="input-group">
                            <h3 class="titulo"><?php echo $comercio; ?></h3>
                        </div>
                    </div>
                    <table class="table table-responsive">
                       <?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
date_default_timezone_set('America/Bogota');

if ($_POST) {

    include "vpos/beans/vpos_plugin.php";
    include "vpos/conexion_cer.php";
    include "vpos/conexion_i.php";

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

    $arrayIn['IDACQUIRER']  = mb_convert_encoding($_POST['IDACQUIRER'], "ISO-8859-1", "UTF-8");
    $arrayIn['IDCOMMERCE']  = mb_convert_encoding($_POST['IDCOMMERCE'], "ISO-8859-1", "UTF-8");
    $arrayIn['XMLRES']      = mb_convert_encoding($_POST['XMLRES'], "ISO-8859-1", "UTF-8");
    $arrayIn['DIGITALSIGN'] = mb_convert_encoding($_POST['DIGITALSIGN'], "ISO-8859-1", "UTF-8");
    $arrayIn['SESSIONKEY']  = mb_convert_encoding($_POST['SESSIONKEY'], "ISO-8859-1", "UTF-8");

    if (VPOSResponse($arrayIn, $arrayOut, $llavePublicaFirma, $llavePrivadaCifrado, $VI)) {

        if (isset($arrayOut['authorizationCode'])) {
            $arrayOut['authorizationCode'] = $arrayOut['authorizationCode'];
        } else {
            $arrayOut['authorizationCode'] = " ";
        }

        if (isset($arrayOut['purchaseIva'])) {
            $arrayOut['purchaseIva'] = $arrayOut['purchaseIva'];
        } else {
            $arrayOut['purchaseIva'] = 0;
        }

        if (isset($arrayOut['reserved6'])) {
            $arrayOut['reserved6'] = $arrayOut['reserved6'];
        } else {
            $arrayOut['reserved6'] = 0;
        }

        $monto_a_mostrar = utf8_decode($arrayOut['purchaseAmount']) / 100;
        $iva             = $arrayOut['purchaseIva'] / 100;
        $subtotal        = $monto_a_mostrar - $iva - $arrayOut['reserved6'];
     
        $id_ref = $arrayOut['reserved2'];

               //$arrayOut['authorizationResult'] = 00;

 if ($arrayOut['authorizationResult'] == 00) {

  $update = "UPDATE  `" . $pre . "posts` SET  `post_status` =  'wc-processing' WHERE  `" . $pre . "posts`.`ID` =" . $id_ref;

                $result = mysqli_query($conexion, $update);

            

        }

        //-------------inicio de repuesta aprobada 19---------------

       //$arrayOut['authorizationResult'] = 19;

        if ($arrayOut['authorizationResult'] == 19) {


                 $update19 = "UPDATE  `" . $pre . "posts` SET  `post_status` =  'wc-verify' WHERE  `" . $pre . "posts`.`ID` =" . $id_ref;

                $result = mysqli_query($conexion, $update19);


           

        }
        //fin de recibio respuesta aprobada 19

    


 $consulta = "Select * from envio where purchaseOperationNumber = '" . $arrayOut['purchaseOperationNumber'] . "'";


$resultado = mysqli_query($conexion, $consulta);
$fila      = mysqli_fetch_row($resultado);

if($fila[8] == "Apellido"){
    $fila[8] = "";
}


?>

                       <tbody>
                            <tr>
                                <th>Codigo Unico:</th>
                                <td><?php echo $codigo_unico ?></td>
                            </tr>
                            <tr>
                                <th>Terminal:</th>
                                <td><?php echo Utf8_encode($arrayOut['purchaseTerminalCode']); ?></td>
                            </tr>


                            <tr>
                                <th>Numero de Transaccion:</th>
                                <td><?php echo Utf8_encode($arrayOut['purchaseOperationNumber']); ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de Transaccion:</th>
                                <td><?php echo $arrayOut['fecha'] = date('d-m-Y'); ?></td>
                            </tr>
                            <tr>
                                <th>Hora de Transaccion:</th>
                                <td><?php echo $arrayOut['hora'] = date('H:i:s'); ?></td>
                            </tr>
                             <tr>
                                <th>Nombre:</th>
                                <td><?php echo $fila[7] . " " . $fila[8];
?></td>
                            </tr>

                            <tr>
                                <th>Moneda:</th>
                                <td>COP</td>
                            </tr>
                           <tr>
                                <th>Valor Compra:</th>
                                <td><?php echo "$ " . Utf8_encode(number_format($subtotal, 2, ",", ".")); ?></td>
                            </tr>
                            <tr>
                                <th>IVA:</th>
                                <td><?php echo "$ " . Utf8_encode(number_format($iva, 2, ",", ".")); ?></td>
                            </tr>
                              <tr>
                                <th>Envio:</th>
                                <td><?php echo "$ " . Utf8_encode(number_format($arrayOut['reserved6'], 2, ",", ".")); ?></td>
                            </tr>



                                <tr>
                                <th>Valor Total:</th>

                                <td><?php echo "$ " . Utf8_encode(number_format($monto_a_mostrar, 2, ",", ".")); ?></td>
                            </tr>

                            <tr>
                                <th>Concepto de pago:</th>
                                <td><?php echo utf8_encode("Compra en tienda online Pedido #") . $arrayOut['reserved7']; ?></td>
                            </tr>
                            <tr>
                                <th>Referencia:</th>
                                <td><?php echo Utf8_encode($arrayOut['reserved7']); ?></td>
                            </tr>

                            <tr>
                                <th>Respuesta:</th>
                                <td><?php echo Utf8_encode($arrayOut['errorMessage']); ?></td>
                            </tr>
                            <tr>
                                <th>Numero de Autorizacion:</th>
                                <td><?php echo Utf8_encode($arrayOut['authorizationCode']); ?></td>
                            </tr>



                            <?php
} else {
    echo "<h1>Error contacte al adminstrador</h1>";
}
$arrayOut = Utf8_encode(urlencode(serialize($arrayOut)));

?>

                        </tbody>
                    </table>
                </div>



 <center>

                        <div id="parte2" class="col-md-12 col-sm-12 col-xs-12 pad-adjust">

                    <a class="btn btn-primary btn-lg btn-block" href="<?php echo $url_comercio; ?>" class="col-center-naranja">Volver a <?php echo $comercio; ?>
</a></div>
<br>&nbsp
                </center>

                <div class="form-footer">
                    <div class="row ">
                        <div class="col-md-12 col-sm-12 col-xs-12 pad-adjust">
                            <form method="POST" action="imprimir.php" target="_blank">
                                <input type="hidden" name="recibo" value="<?php echo $arrayOut ?>">



                                <button type="button"  class="btn btn-success btn-lg btn-block" id ="impri">IMPRIMIR COMPROBANTE</button>

                            </form>
                            <?php }
?>
                        </div>
                    </div>
                </div>

            </div>

        </div>


        <script src="vpos/bootstrap/js/bootstrap.min.js"></script>
        <script src="vpos/assets/jquery-1.11.2.min.js"></script>
        <script src="vpos/assets/jquery.validate.min.js"></script>
        <script src="vpos/assets/register.js"></script>
<script>

// JavaScript Validation

$('document').ready(function()
                    {

$("#impri").click(function () {

$("#printarea").show();
    window.print();
});
});
</script>




    </body>
</html>
