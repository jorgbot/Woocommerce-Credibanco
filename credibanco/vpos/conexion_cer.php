<?php

$ruta_absoluta = 'file://' . getcwd() . '/vpos/certificados';

$VI          = "3a56d4ec2b218c39";
$id_commerce = '6988';
$terminal    = '00017854';
$id_acquirer = '1';

$codigo_unico = "015818750"; 

$llaveVPOSCryptoPub     = $ruta_absoluta . "/LLAVE.VPOS.CRB.CRYPTO.1024.X509.txt";
$llaveComercioFirmaPriv = $ruta_absoluta . "/empresa.firma.privada.txt";


$llavePublicaFirma   = $ruta_absoluta . "/LLAVE.VPOS.CRB.SIGN.1024.X509.txt";
$llavePrivadaCifrado = $ruta_absoluta . "/empresa.cifrado.privada.txt";
