<?php

class FirmadorDIAN {
    const POLITICA_FIRMA = [
        "name"   => "Politica de firma para facturas electronicas de la Republica de Colombia",
        "url"    => "https://facturaelectronica.dian.gov.co/politicadefirma/v2/politicadefirmav2.pdf",
        "digest" => "dMoMvtcG5aIzgYo0tIsSQeVJBDnUnfSOfBpxXrmor0Y="
    ];

    private $publicKey = null;
    private $privateKey = null;

    public function signXML($xmlString, $pfxFile, $pfxPassword, $xmlTagName)
{
    if (empty($xmlString)) {
        throw new Exception("El XML proporcionado esta vacio.");
    }

    if (!file_exists($pfxFile)) {
        throw new Exception("El archivo PFX no existe: $pfxFile");
    }
    
    // Crear el objeto DOMDocument y cargar el XML desde el string
    $xml = new DOMDocument();
    if (!$xml->loadXML($xmlString)) {
        throw new Exception("Error al cargar el XML desde el string proporcionado.");
    }

    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;

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
    file_put_contents('canonicalized2.xml', $xmlString);
    // Generar la firma digital y obtener el nodo XML correspondiente
    $signatureXMLString = $this->firmarDocumentoDesdeString($xmlString, $pfxFile, $pfxPassword);

    // Convertir el string de la firma en un nodo DOM
    $signatureDoc = new DOMDocument();
    $signatureDoc->loadXML($signatureXMLString);

    // Importar el nodo de la firma al documento principal
    $signatureNode = $xml->importNode($signatureDoc->documentElement, true);

    // Añadir la firma al ExtensionContent
    $extensionContent->appendChild($signatureNode);

    $secondUBLExtension->appendChild($extensionContent);

    // Convertir el documento firmado a string y devolverlo
    return $xml->saveXML();
}

public function firmarDocumentoDesdeString($xmlString, $certificadoPfx, $claveCertificado) {
    if (empty($xmlString)) {
        throw new Exception("El XML proporcionado esta vacio.");
    }

    // Cargar el certificado y extraer claves
    $pfx = file_get_contents($certificadoPfx);
    if ($pfx === false) {
        throw new Exception("No se pudo cargar el certificado PFX.");
    }

    if (!openssl_pkcs12_read($pfx, $key, $claveCertificado)) {
        throw new Exception("No se pudo leer el certificado PFX. Verifica la contraseña.");
    }

    $this->publicKey = $key["cert"];
    $this->privateKey = $key["pkey"];
    $certData = openssl_x509_parse($this->publicKey);
    if (!$certData) {
        throw new Exception("Error al analizar el certificado.");
    }

    // Generar firma
    $signature = $this->generarFirma($xmlString, $certData);

    return $signature;
}


    private function generarFirma($xml, $certData) {
        // Canonicalizar XML
        $dom = new DOMDocument('1.0','UTF-8');
        $dom->loadXML($xml);

        $canonicalXML = $dom->C14N();

        // Calcular digest del documento
        $documentDigest = base64_encode(hash('SHA256', $canonicalXML, true));
        
        // Obtener detalles del certificado
        $certDigest = base64_encode(openssl_x509_fingerprint($this->publicKey, "sha256", true));
        $certIssuer = $this->formatearIssuer($certData['issuer']);
        $certSerialNumber = $certData['serialNumber'];
        
        // Generar propiedades firmadas
        $signTime = gmdate('Y-m-d\TH:i:s').'-05:00';
        $signedProperties = $this->generarSignedProperties($signTime, $certDigest, $certIssuer, $certSerialNumber);
        
        // Crear la firma digital para las tres referencias
        $signatureValue = $this->crearFirma($documentDigest, $signedProperties);

        // Generar keyInfo

        $keyInfo = $this -> getKeyInfo($this->obtenerCertificadoBase64());
        
        // Crear la estructura de la firma con tres referencias
        $signature = '<ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Id="xmldsig-760ebff4b86db4c059532d6e4f01a8a3261f348ab39e10ad448dbb49bf5e1d8be25ffbd5495d2d295866693f20b3d724"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"></ds:CanonicalizationMethod> <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></ds:SignatureMethod> <ds:Reference Id="xmldsig-760ebff4b86db4c059532d6e4f01a8a3261f348ab39e10ad448dbb49bf5e1d8be25ffbd5495d2d295866693f20b3d724-ref0" URI=""> <ds:Transforms> <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></ds:Transform> </ds:Transforms> <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod> <ds:DigestValue>' . $documentDigest . '</ds:DigestValue> </ds:Reference> <ds:Reference URI="#xmldsig-760ebff4b86db4c059532d6e4f01a8a3261f348ab39e10ad448dbb49bf5e1d8be25ffbd5495d2d295866693f20b3d724-KeyInfo"> <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod> <ds:DigestValue>' . base64_encode(hash('SHA256', $keyInfo, true)) . '</ds:DigestValue> </ds:Reference> <ds:Reference Type="http://uri.etsi.org/01903#SignedProperties" URI="#xmldsig-760ebff4b86db4c059532d6e4f01a8a3261f348ab39e10ad448dbb49bf5e1d8be25ffbd5495d2d295866693f20b3d724-signedprops"> <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod> <ds:DigestValue>' . base64_encode(hash('SHA256', $this->obtenerCertificadoBase64(), true)) . '</ds:DigestValue> </ds:Reference> </ds:SignedInfo> <ds:SignatureValue>' . $signatureValue . '</ds:SignatureValue> '.$keyInfo.' <ds:Object> <xades:QualifyingProperties Target="#xmldsig-760ebff4b86db4c059532d6e4f01a8a3261f348ab39e10ad448dbb49bf5e1d8be25ffbd5495d2d295866693f20b3d724"> '.$signedProperties.' </xades:QualifyingProperties> </ds:Object> </ds:Signature>';
    
        // Retornar solo la firma como XML
        return $signature;
    }
    
    private function getKeyInfo($certificadoBase64) {
        return '<ds:KeyInfo Id="xmldsig-760ebff4b86db4c059532d6e4f01a8a3261f348ab39e10ad448dbb49bf5e1d8be25ffbd5495d2d295866693f20b3d724-KeyInfo"> <ds:X509Data> <ds:X509Certificate>' . $certificadoBase64 . '</ds:X509Certificate> </ds:X509Data> </ds:KeyInfo>';
    }

    private function obtenerCertificadoBase64() {
        // Eliminar cabeceras y pie de las claves
        return str_replace(
            ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\n", "\r"],
            '',
            $this->publicKey
        );
    }
    

    private function generarSignedProperties($signTime, $certDigest, $certIssuer, $certSerialNumber) {
        return '<xades:SignedProperties Id="xmldsig-760ebff4b86db4c059532d6e4f01a8a3261f348ab39e10ad448dbb49bf5e1d8be25ffbd5495d2d295866693f20b3d724-signedprops"><xades:SignedSignatureProperties><xades:SigningTime>' . $signTime . '</xades:SigningTime> <xades:SigningCertificate> <xades:Cert> <xades:CertDigest> <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod> <ds:DigestValue>' . $certDigest . '</ds:DigestValue> </xades:CertDigest> <xades:IssuerSerial><ds:X509IssuerName>' . $certIssuer . '</ds:X509IssuerName> <ds:X509SerialNumber>' . $certSerialNumber . '</ds:X509SerialNumber> </xades:IssuerSerial></xades:Cert></xades:SigningCertificate><xades:SignaturePolicyIdentifier><xades:SignaturePolicyId><xades:SigPolicyId><xades:Identifier>https://facturaelectronica.dian.gov.co/politicadefirma/v2/politicadefirmav2.pdf</xades:Identifier><xades:Description>Politica de firma para facturas electronicas de la Republica de Colombia</xades:Description> </xades:SigPolicyId> <xades:SigPolicyHash> <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod> <ds:DigestValue> dMoMvtcG5aIzgYo0tIsSQeVJBDnUnfSOfBpxXrmor0Y=</ds:DigestValue> </xades:SigPolicyHash> </xades:SignaturePolicyId> </xades:SignaturePolicyIdentifier> <xades:SignerRole> <xades:ClaimedRoles> <xades:ClaimedRole>supplier</xades:ClaimedRole> </xades:ClaimedRoles> </xades:SignerRole></xades:SignedSignatureProperties> </xades:SignedProperties>';
    }

    private function crearFirma($documentDigest, $signedProperties) {
        $signedInfo = '<ds:SignedInfo> <ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"></ds:CanonicalizationMethod> <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></ds:SignatureMethod> <ds:Reference URI=""> <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod> <ds:DigestValue>' . $documentDigest . '</ds:DigestValue> </ds:Reference> </ds:SignedInfo>';

        $signatureValue = '';
        openssl_sign($signedInfo, $signatureValue, $this->privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signatureValue);
    }

    private function formatearIssuer($issuer) {
        // Formatear los campos del emisor segun las normas de la DIAN
        return implode('/', array_map(function ($item) {
            return trim($item);
        }, explode('/', $issuer)));
    }
}
?>