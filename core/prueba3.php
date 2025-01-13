<?php

require_once('xmlseclibs.php');

// Cargar el documento XML
$doc = new DOMDocument();
$doc->load('../../../facturacion/factura/clases/canon_firmado.xml');

// Crear el objeto XMLSec
$xmlSec = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'private'));
$signature = $doc->getElementsByTagName('ds:Signature')->item(0);

// Verificar la firma
$valid = $xmlSec->validateSignature($signature);
if ($valid) {
    echo "Firma válida";
} else {
    echo "Firma no válida";
}
