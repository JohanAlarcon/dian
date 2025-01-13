<?php

date_default_timezone_set('America/Bogota');

class MethodSignature
{
    private $publicKey;
    private $privateKey;
    private $xml;

    public function cargarCertificado($xml)
    {

        $certificadoPfx = __DIR__ . '/../../certificados/certificado.pfx';
        $claveCertificado = 'auxyhVfbkX';

        $pfx = file_get_contents($certificadoPfx);
        if (!openssl_pkcs12_read($pfx, $key, $claveCertificado)) {
            die("Error al leer el archivo PFX. Revisa la clave o el archivo.");
        }

        $this->publicKey = $key['cert'];
        $this->privateKey = $key['pkey'];
        $this->xml = $xml;
    }

    public function getToTagCan($id, $to)
    {
        $to_tag = '<wsa:To xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:wcf="http://wcf.dian.colombia" xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" wsu:Id="id-' . $id . '">' . $to . '</wsa:To>';
        $digest = base64_encode(hash('sha256', $to_tag, true));
        return $digest;
    }

    public function getCertificate()
    {
        openssl_x509_export($this->publicKey, $publicPEM);
        $publicPEM = str_replace("-----BEGIN CERTIFICATE-----", "", $publicPEM);
        $publicPEM = str_replace("-----END CERTIFICATE-----", "", $publicPEM);
        $publicPEM = str_replace("\r", "", str_replace("\n", "", $publicPEM));
        return $publicPEM;
    }

    public function getSignedInfoCan($id, $digestValue)
    {

        return '<ds:SignedInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:wcf="http://wcf.dian.colombia" xmlns:wsa="http://www.w3.org/2005/08/addressing"><ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"><ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="wsa soap wcf"></ec:InclusiveNamespaces></ds:CanonicalizationMethod><ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></ds:SignatureMethod><ds:Reference URI="#id-' . $id . '"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"><ec:InclusiveNamespaces xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#" PrefixList="soap wcf"></ec:InclusiveNamespaces></ds:Transform></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></ds:DigestMethod><ds:DigestValue>' . $digestValue . '</ds:DigestValue></ds:Reference></ds:SignedInfo>';
    }

    public function formSoapRequest($certificate, $created, $expires, $disgestValue, $signatureValue, $action, $to, $toId, $xml)
    {
        return  '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:wcf="http://wcf.dian.colombia"><soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"><wsse:BinarySecurityToken EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary" ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3" wsu:Id="X509-' . $toId . '">' . $certificate . '</wsse:BinarySecurityToken><wsu:Timestamp><wsu:Created>' . $created . '</wsu:Created><wsu:Expires>' . $expires . '</wsu:Expires></wsu:Timestamp><ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#"><ds:SignedInfo><ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"><ec:InclusiveNamespaces PrefixList="wsa soap wcf" xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#"/></ds:CanonicalizationMethod><ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"/><ds:Reference URI="#id-' . $toId . '"><ds:Transforms><ds:Transform Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"><ec:InclusiveNamespaces PrefixList="soap wcf" xmlns:ec="http://www.w3.org/2001/10/xml-exc-c14n#"/></ds:Transform></ds:Transforms><ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/><ds:DigestValue>' . $disgestValue . '</ds:DigestValue></ds:Reference></ds:SignedInfo><ds:SignatureValue>' . $signatureValue . '</ds:SignatureValue><ds:KeyInfo><wsse:SecurityTokenReference><wsse:Reference URI="#X509-' . $toId . '" ValueType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0#X509v3"/></wsse:SecurityTokenReference></ds:KeyInfo></ds:Signature></wsse:Security><wsa:Action>' . $action . '</wsa:Action><wsa:To wsu:Id="id-' . $toId . '" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $to . '</wsa:To></soap:Header>' . $xml . '</soap:Envelope>';
    }

    public function firmarXML()
    {

        $toId = 'A761BBC7BD3CF85423157738020456023';
        $to = 'https://vpfe.dian.gov.co/WcfDianCustomerServices.svc';

        $digestValue =  $this->getToTagCan($toId, $to);
        $signedInfoTag = $this->getSignedInfoCan($toId, $digestValue);
        $algo = "SHA256";

        $certificate = $this->getCertificate();
        $created = gmdate("Y-m-d\TH:i:s\Z");
        $expires = gmdate("Y-m-d\TH:i:s\Z", time() + 3600);

        $signatureResult = '';
        openssl_sign($signedInfoTag, $signatureResult, $this->privateKey, $algo);
        $signatureValue = base64_encode($signatureResult);

        $action = 'http://wcf.dian.colombia/IWcfDianCustomerServices/GetNumberingRange';

        $xml = $this->formSoapRequest($certificate, $created, $expires, $digestValue, $signatureValue, $action, $to, $toId, $this->xml);

        return $xml;
    }

    public function enviarXML($xml,$action)
    {
        try {

            $this->cargarCertificado($xml);

            $xmlFirmado = $this->firmarXML();

            $printXML = true;

            if ($printXML) {
                $filePath = __DIR__ . '/xmlFirmado.xml';
                file_put_contents($filePath, $xmlFirmado);
                header('Content-Type: text/xml');
                echo $xmlFirmado;
                exit;
            }

            $endpoint = 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc?wsdl';

            $curl = curl_init();

            $headers = [
                'Accept: application/xml',
                'Content-type: application/soap+xml',
                'Content-Length: ' . strlen($xmlFirmado),
                'SOAPAction: ' . $action,
            ];

            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 180);
            curl_setopt($curl, CURLOPT_TIMEOUT, 180);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlFirmado);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $res  = curl_exec($curl);

            if (!$res) {
                die("Error al obtener respuesta del servicio: " . curl_error($curl));
            }

            curl_close($curl);

            $response = new DOMDocument;

            $response->loadXML($res);
            $errors = $response->getElementsByTagName('ErrorMessage')[0];

            if (!is_null($errors)) {
                echo $errors->nodeValue;
                echo "<br>";
            }
            
            return $res;

        } catch (SoapFault $e) {
            echo "Error al consumir el servicio: " . $e->getMessage();
        }
    }
}
