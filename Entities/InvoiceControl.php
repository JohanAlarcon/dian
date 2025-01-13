<?php

class InvoiceControl {
    public $invoiceAuthorization;
    public $authorizationPeriod;
    public $authorizedInvoices;

    public function __construct($invoiceAuthorization, AuthorizationPeriod $authorizationPeriod, AuthorizedInvoices $authorizedInvoices) {
        $this->invoiceAuthorization = $invoiceAuthorization;
        $this->authorizationPeriod = $authorizationPeriod;
        $this->authorizedInvoices = $authorizedInvoices;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('sts:InvoiceControl');
        
        // Crear y agregar el nodo InvoiceAuthorization
        $node->appendChild(XMLGenerator::createNode($dom, 'sts:InvoiceAuthorization', $this->invoiceAuthorization));
        
        // Agregar el nodo AuthorizationPeriod
        $node->appendChild($this->authorizationPeriod->toXML($dom));
        
        // Agregar el nodo AuthorizedInvoices
        $node->appendChild($this->authorizedInvoices->toXML($dom));
        
        return $node;
    }
}

?>
