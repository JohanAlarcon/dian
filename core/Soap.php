<?php

require __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use RobRichards\WsePhp\WSSESoap;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

$certificadoPem = 'D:/certificadoDigital/roa/certificado_pem.cer'; // Ruta a tu certificado PEM
$privateKeyPem = 'D:/certificadoDigital/roa/private_pem.pem'; // Ruta a tu clave privada

try {
    // Crear cliente Guzzle con configuración SSL
    $client = new Client([
        'base_uri' => 'https://vpfe-hab.dian.gov.co/', // URL base sin el ?wsdl
        'verify' => false, // Solo para pruebas; en producción, usar CA confiable
        'cert' => $certificadoPem, // Archivo combinado PEM
    ]);

    // Configurar cuerpo de la solicitud SOAP con el formato correcto
    $body = <<<XML
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:wcf="http://wcf.dian.colombia">
   <soap:Header/>
   <soap:Body>
      <wcf:GetNumberingRange>
         <wcf:accountCode>900997411</wcf:accountCode>
         <wcf:accountCodeT>900997411</wcf:accountCodeT>
         <wcf:softwareCode>a83780ce-effd-4552-8fc3-3aa9cba710ee</wcf:softwareCode>
      </wcf:GetNumberingRange>
   </soap:Body>
</soap:Envelope>
XML;

    // Cargar el cuerpo de la solicitud en un DOMDocument
    $doc = new DOMDocument();
    $doc->loadXML($body);

    // Crear un nuevo WSSESoap objeto
    $wsse = new WSSESoap($doc);

    // Firmar el mensaje
    $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
    $key->loadKey($privateKeyPem, true);
    $wsse->signSoapDoc($key);

    // Agregar el certificado al encabezado WS-Security
    $token = $wsse->addBinaryToken(file_get_contents($certificadoPem));
    $wsse->attachTokentoSig($token);

    // Obtener el cuerpo firmado
    $body = $wsse->saveXML();

    // Verifica el cuerpo de la solicitud firmada
    // echo "Cuerpo firmado: " . $body;

    // Enviar la solicitud
    $response = $client->post('WcfDianCustomerServices.svc', [
        'headers' => [
            'Content-Type' => 'application/soap+xml; charset=utf-8',
        ],
        'body' => $body,
    ]);

    // Mostrar la respuesta
    echo "Respuesta: " . $response->getBody();

} catch (RequestException $e) {
    echo "Error en la solicitud: " . $e->getMessage();
    if ($e->hasResponse()) {
        // Imprimir el cuerpo de la respuesta de error
        echo "Respuesta de error: " . $e->getResponse()->getBody();
        
        // También puedes obtener más detalles sobre el error
        echo "Detalles de error: " . $e->getResponse()->getStatusCode();
    }
}
