<?php

/**
 * Clase que representa el nodo InvoicePeriod.
 * Grupo de campos para información que describen una orden de pedido para esta factura.
 * @property string ID Prefijo y Número del documento orden referenciado.
 * @property string issueDate Fecha de emisión: Fecha de emisión de la orden .
 * 
 */

class InvoicePeriod {

    public $StartDate;
    public $StartTime;
    public $EndDate;
    public $EndTime;

    public function __construct($StartDate, $StartTime, $EndDate, $EndTime) {
        $this->StartDate = $StartDate;
        $this->StartTime = $StartTime;
        $this->EndDate = $EndDate;
        $this->EndTime = $EndTime;
    }

    public function toXML($dom) {

        $node = $dom->createElement('cac:InvoicePeriod');

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:StartDate', $this->StartDate));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:StartTime', $this->StartTime));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:EndDate', $this->EndDate));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:EndTime', $this->EndTime));

        return $node;
    }
}

?>