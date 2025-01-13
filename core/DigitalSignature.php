<?php

require __DIR__ . '/../../vendor/autoload.php';

use DOMDocument;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class DigitalSignatureDIAN
{
    public function signXML($xmlFilePath, $pfxFile, $pfxPassword, $xmlTagName)
    {
        if (!file_exists($xmlFilePath)) {
            throw new Exception("El archivo XML no existe: $xmlFilePath");
        }

        if (!file_exists($pfxFile)) {
            throw new Exception("El archivo PFX no existe: $pfxFile");
        }

        // Leer el XML
        $xml = new DOMDocument();
        $xml->load($xmlFilePath);

        // Dividir la etiqueta en prefijo y nombre
        $tagSearch = explode(':', $xmlTagName);
        $prefix = isset($tagSearch[0]) ? $tagSearch[0] : '';
        $mainTag = isset($tagSearch[1]) ? $tagSearch[1] : $tagSearch[0];

        // Crear o buscar el nodo UBLExtensions
        $namespace = 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2';
        $extensionsElement = $xml->getElementsByTagNameNS($namespace, 'UBLExtensions')->item(0);

        if (!$extensionsElement) {
            $extensionsElement = $xml->createElement("{$prefix}:UBLExtensions");
            $xml->documentElement->appendChild($extensionsElement);
        }

        // Crear el segundo UBLExtension si no existe
        $existingUBLExtensions = $xml->getElementsByTagNameNS($namespace, 'UBLExtension');
        if ($existingUBLExtensions->length < 2) {
            $secondUBLExtension = $xml->createElement("{$prefix}:UBLExtension");
            $extensionsElement->appendChild($secondUBLExtension);
        } else {
            $secondUBLExtension = $existingUBLExtensions->item(1);
        }

        // Crear ExtensionContent dentro de UBLExtension
        $extensionContent = $xml->createElement("{$prefix}:ExtensionContent");

        // Generar la firma digital y añadirla a ExtensionContent
        $signatureNode = $this->createSignatureNode($xml, $pfxFile, $pfxPassword);
        $extensionContent->appendChild($signatureNode);

        $secondUBLExtension->appendChild($extensionContent);

        // Guardar el archivo firmado
        $signedXmlPath = str_replace('.xml', '_signed.xml', $xmlFilePath);
        $xml->save($signedXmlPath);

        return $signedXmlPath;
    }

    private function createSignatureNode($xml, $pfxFile, $pfxPassword)
{
    if (!($xml instanceof DOMDocument)) {
        throw new Exception("El parámetro \$xml no es un objeto válido de DOMDocument.");
    }

    $certs = [];
    if (!openssl_pkcs12_read(file_get_contents($pfxFile), $certs, $pfxPassword)) {
        throw new Exception("No se pudo leer el archivo PFX. Verifica la contraseña.");
    }

    $privateKey = $certs['pkey'];
    $publicCert = $certs['cert'];

    if (empty($privateKey) || empty($publicCert)) {
        throw new Exception("La clave privada o el certificado público no son válidos.");
    }

    $objDSig = new XMLSecurityDSig();
    $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);

    // Añadir referencia al documento
    $objDSig->addReference(
        $xml,
        XMLSecurityDSig::SHA256,
        ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
        ['uri' => '']
    );

    // Crear y configurar KeyInfo
    $keyInfoNode = $xml->createElement('ds:KeyInfo');
    $keyInfoNode->setAttribute('Id', 'xmldsig-keyinfo');
    $x509Data = $xml->createElement('ds:X509Data');
    $x509Certificate = $xml->createElement('ds:X509Certificate', base64_encode($publicCert));
    $x509Data->appendChild($x509Certificate);
    $keyInfoNode->appendChild($x509Data);

    $objDSig->addReference(
        $keyInfoNode,
        XMLSecurityDSig::SHA256,
        null,
        ['uri' => '#xmldsig-keyinfo']
    );

    // Crear Object y QualifyingProperties
    $objectNode = $xml->createElement('ds:Object');
    $qualifyingProperties = $xml->createElementNS(
        'http://uri.etsi.org/01903/v1.3.2#',
        'xades:QualifyingProperties'
    );
    $qualifyingProperties->setAttribute('Target', '#xmldsig-signature-id');

    $signedProperties = $this->createSignedProperties($xml, $publicCert);
    $qualifyingProperties->appendChild($signedProperties);
    $objectNode->appendChild($qualifyingProperties);

    $objDSig->addReference(
        $signedProperties,
        XMLSecurityDSig::SHA256,
        null,
        ['type' => 'http://uri.etsi.org/01903#SignedProperties']
    );

    // Generar la firma
    $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
    $objKey->loadKey($privateKey);

    $signatureNode = $objDSig->sign($objKey);
    if ($signatureNode === null) {
        throw new Exception("No se pudo generar el nodo de firma.");
    }

    // Añadir firma al documento
    $objDSig->appendSignature($signatureNode, $xml->documentElement);

    // Añadir KeyInfo y Object al nodo de firma
    $signatureNode->appendChild($keyInfoNode);
    $signatureNode->appendChild($objectNode);

    return $signatureNode;
}




    private function createSignedProperties($xml, $publicCert)
    {
        $signedProperties = $xml->createElementNS(
            'http://uri.etsi.org/01903/v1.3.2#',
            'xades:SignedProperties'
        );
        $signedProperties->setAttribute('Id', 'xmldsig-signedprops');

        // Nodo SignedSignatureProperties
        $signedSignatureProperties = $xml->createElement('xades:SignedSignatureProperties');

        // Nodo SigningTime
        $signingTime = $xml->createElement('xades:SigningTime', gmdate('Y-m-d\TH:i:s\Z'));
        $signedSignatureProperties->appendChild($signingTime);

        // Nodo SigningCertificate
        $signingCertificate = $xml->createElement('xades:SigningCertificate');
        $cert = $xml->createElement('xades:Cert');
        $certDigest = $xml->createElement('xades:CertDigest');

        $digestMethod = $xml->createElement('ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $certDigest->appendChild($digestMethod);

        $digestValue = base64_encode(hash('sha256', $publicCert, true));
        $certDigest->appendChild($xml->createElement('ds:DigestValue', $digestValue));

        $cert->appendChild($certDigest);

        $issuerSerial = $xml->createElement('xades:IssuerSerial');
        $issuerSerial->appendChild($xml->createElement('ds:X509IssuerName', 'Nombre del Emisor del Certificado')); // Reemplazar por datos reales
        $issuerSerial->appendChild($xml->createElement('ds:X509SerialNumber', 'Número de Serie del Certificado')); // Reemplazar por datos reales

        $cert->appendChild($issuerSerial);
        $signingCertificate->appendChild($cert);
        $signedSignatureProperties->appendChild($signingCertificate);

        // Nodo SignaturePolicyIdentifier
        $signaturePolicyIdentifier = $xml->createElement('xades:SignaturePolicyIdentifier');
        $signaturePolicyId = $xml->createElement('xades:SignaturePolicyId');
        $policyId = $xml->createElement('xades:SigPolicyId');
        $policyId->appendChild($xml->createElement('xades:Identifier', 'https://facturaelectronica.dian.gov.co/politicadefirma/v2/politicadefirmav2.pdf'));
        $policyId->appendChild($xml->createElement('xades:Description', 'Política de firma para facturas electrónicas de la República de Colombia'));

        $signaturePolicyId->appendChild($policyId);

        $sigPolicyHash = $xml->createElement('xades:SigPolicyHash');
        $digestMethod = $xml->createElement('ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');
        $sigPolicyHash->appendChild($digestMethod);
        $sigPolicyHash->appendChild($xml->createElement('ds:DigestValue', 'dMoMvtcG5aIzgYo0tIsSQeVJBDnUnfSOfBpxXrmor0Y='));

        $signaturePolicyId->appendChild($sigPolicyHash);
        $signaturePolicyIdentifier->appendChild($signaturePolicyId);
        $signedSignatureProperties->appendChild($signaturePolicyIdentifier);

        // Nodo SignerRole
        $signerRole = $xml->createElement('xades:SignerRole');
        $claimedRoles = $xml->createElement('xades:ClaimedRoles');
        $claimedRoles->appendChild($xml->createElement('xades:ClaimedRole', 'supplier'));
        $signerRole->appendChild($claimedRoles);
        $signedSignatureProperties->appendChild($signerRole);

        $signedProperties->appendChild($signedSignatureProperties);

        return $signedProperties;
    }
}
