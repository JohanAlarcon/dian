<?php

class DianExtensions {
    public $softwareSecurityCode;
    public $QRCode;

    public $invoiceControl;
    public $invoiceSource;
    public $softwareProvider;
    public $authorizationProvider;

    public function __construct(
        $softwareSecurityCode,
        $QRCode=null,
        InvoiceControl $invoiceControl=null,
        InvoiceSource $invoiceSource,
        SoftwareProvider $softwareProvider,
        AuthorizationProvider $authorizationProvider
    ) {
        $this->softwareSecurityCode = $softwareSecurityCode;
        $this->QRCode = $QRCode;

        $this->invoiceControl = $invoiceControl;
        $this->invoiceSource = $invoiceSource;
        $this->softwareProvider = $softwareProvider;
        $this->authorizationProvider = $authorizationProvider;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('sts:DianExtensions');
    
         // Crear el nodo SoftwareSecurityCode
        $softwareSecurityCodeNode = $dom->createElement('sts:SoftwareSecurityCode', $this->softwareSecurityCode);
    
        // Agregar los atributos al nodo SoftwareSecurityCode
        $AttributesSoftwareSecurityCode = array(
            "schemeAgencyID" => "195",
            "schemeAgencyName" => "CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)"
        );
    
        foreach ($AttributesSoftwareSecurityCode as $key => $value) {
            $softwareSecurityCodeNode->setAttribute($key, $value);
        }
    
        // Añadir el nodo SoftwareSecurityCode al nodo principal
        
        
       
        
        // Agregar las demás entidades

        if ($this->invoiceControl !== null) {
            $node->appendChild($this->invoiceControl->toXML($dom));
        }
        $node->appendChild($this->invoiceSource->toXML($dom));
        $node->appendChild($this->softwareProvider->toXML($dom));
        $node->appendChild($softwareSecurityCodeNode); // Añadir el nodo SoftwareSecurityCode al nodo principal
        $node->appendChild($this->authorizationProvider->toXML($dom));

        if ($this->QRCode !== null) {
            $node->appendChild(XMLGenerator::createNode($dom, 'sts:QRCode', $this->QRCode)); // Agregar el nodo QRCode
        }
    
        return $node;
    }
    
}

?>
