<?php

/**
 * Clase que representa el nodo BillingReference.
 * Grupo de campos para información que describen una orden de pedido para esta factura.
 * @property string ID Prefijo y Número del documento orden referenciado.
 * @property string issueDate Fecha de emisión: Fecha de emisión de la orden .
 * 
 */

class BillingReference
{

    public $ID;
    public $UUID;
    public $IssueDate;

    public function __construct($ID, $UUID, $IssueDate)
    {
        $this->ID = $ID;
        $this->UUID = $UUID;
        $this->IssueDate = $IssueDate;
    }

    public function toXML($dom)
    {

        $node = $dom->createElement('cac:BillingReference');

        $InvoiceDocumentReference = $dom->createElement('cac:InvoiceDocumentReference');
        $InvoiceDocumentReference->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));
        $UUID = $dom->createElement('cbc:UUID', $this->UUID);
        $UUID->setAttribute('schemeName', 'CUFE-SHA384');
        $InvoiceDocumentReference->appendChild($UUID);
        $InvoiceDocumentReference->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueDate', $this->IssueDate));
        $node->appendChild($InvoiceDocumentReference);

        return $node;
    }
}
