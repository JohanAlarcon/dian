<?php

function validateXMLSignature($xmlString, $digestValueFromXML) {

    $dom = new DOMDocument('1.0','UTF-8');
    $dom->loadXML($xmlString);

    $canonicalXML = $dom->C14N();
    //$canonicalXML=$xmlString;

    // Calcular digest del documento
    $documentDigest = base64_encode(hash('sha256', $canonicalXML, true));

    // Compare the calculated digest with the digest value from the XML
    if ($documentDigest === $digestValueFromXML) {
        return "Valid signature. Digest values match: ".$documentDigest;
    } else {
        return "Invalid signature. Digest values do not match: ".$documentDigest;
    }
}

// Example usage:
$xmlUrl = '../../../facturacion/factura/clases/canon_firmado.xml';
$xmlContent = file_get_contents($xmlUrl);

$result = validateXMLSignature($xmlContent, $digestValueFromXML);
echo $result;

?>
