<?php

function validateXMLSignature($xmlContent) {
    $dom = new DOMDocument('1.0','UTF-8');
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;
    $dom->loadXML($xmlContent, LIBXML_NOBLANKS);

    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

    $references = $xpath->query('//ds:SignedInfo/ds:Reference');

    $results = [];

    foreach ($references as $reference) {
        $uri = $reference->getAttribute('URI');
        $digestValue = $xpath->query('ds:DigestValue', $reference)->item(0)->nodeValue;

        if ($uri === '') {
            $elementToSign = $dom->documentElement;
        } else {
            $elementToSign = $xpath->query("//*[@Id='" . substr($uri, 1) . "']")->item(0);
        }

        if ($elementToSign) {
            // Clonar el nodo a firmar
            $newDom = new DOMDocument();
            $importedElement = $newDom->importNode($elementToSign, true);
            $newDom->appendChild($importedElement);

            // Aplicar transformaciones
            $transforms = $xpath->query('ds:Transforms/ds:Transform', $reference);
            foreach ($transforms as $transform) {
                $algorithm = $transform->getAttribute('Algorithm');
                if ($algorithm === 'http://www.w3.org/2000/09/xmldsig#enveloped-signature') {
                    $signatureNodes = $newDom->getElementsByTagName('Signature');
                    foreach ($signatureNodes as $signatureNode) {
                        $signatureNode->parentNode->removeChild($signatureNode);
                    }
                }
            }
            //exit($newDom->saveXML());
            // Canonicalizar y calcular el DigestValue
            $canonicalXml = $newDom->C14N(true, false); // Sin comentarios
            $calculatedDigestValue = base64_encode(hash('sha256', $canonicalXml, true));

            $results[] = [
                'URI' => $uri,
                'DigestValue' => $digestValue,
                'CalculatedDigestValue' => $calculatedDigestValue,
                'Match' => ($digestValue === $calculatedDigestValue) ? 'Yes' : 'No'
            ];
        } else {
            echo "Elemento no encontrado para URI: $uri\n";
        }
    }

    return $results;
}


$xmlUrl = '../../../facturacion/factura/clases/canon_firmado.xml';
$xmlContent = file_get_contents($xmlUrl);
$digestResults = validateXMLSignature($xmlContent);
print_r($digestResults);

?>
