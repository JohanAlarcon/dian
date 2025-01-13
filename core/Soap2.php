<?php

require __DIR__ . '/../../vendor/autoload.php';

// Incluye las librerías necesarias (phpseclib para manejar la firma)
use phpseclib3\Crypt\RSA;
use phpseclib3\File\X509;

// Ruta al archivo PFX y la contraseña del PFX
$pfxFile = 'D:/certificadoDigital/roa/certificate.pfx';
$password = 'auxyhVfbkX';

// Leer el archivo PFX
$pfx = file_get_contents($pfxFile);
if (!openssl_pkcs12_read($pfx, $certs, $password)) {
    die('No se pudo leer el archivo PFX');
}

// Extraer el certificado y la clave privada
$privateKey = $certs['pkey'];
$certificate = $certs['cert'];

// Crear el cliente SOAP y forzar el tipo de contenido correcto
$options = [
    'trace' => 1,
    'exceptions' => true,
    'soap_version' => SOAP_1_2, // SOAP 1.2 es requerido para application/soap+xml
    'location' => 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc', // URL del servicio
    'uri' => 'http://wcf.dian.colombia', // El URI del servicio
    'user_agent' => 'PHP SOAP Client', // Opcional: especificar un agente de usuario
];

// Crear el cliente SOAP con las opciones
$client = new SoapClient('https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc?wsdl', $options);

// Crear el encabezado WS-Security
$wsseHeader = new SoapHeader(
    'http://schemas.xmlsoap.org/ws/2002/12/secext',
    'Security',
    [
        'BinarySecurityToken' => base64_encode($certificate),
    ]
);

// Agregar el encabezado de seguridad
$client->__setSoapHeaders([$wsseHeader]);

// Parámetros para la operación GetNumberingRange
$params = [
    'accountCode' => '900997411',
    'accountCodeT' => '900997411',
    'softwareCode' => 'a83780ce-effd-4552-8fc3-3aa9cba710ee',
];

// Realizar la solicitud
try {
    $response = $client->GetNumberingRange($params);
    var_dump($response); // Ver la respuesta del servicio
} catch (SoapFault $e) {
    echo 'Error: ' . $e->getMessage();
}