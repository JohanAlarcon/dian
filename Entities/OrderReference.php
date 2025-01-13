<?php

/**
 * Clase que representa el nodo OrderReference.
 * Grupo de campos para información que describen una orden de pedido para esta factura.
 * @property string ID Prefijo y Número del documento orden referenciado.
 * @property string issueDate Fecha de emisión: Fecha de emisión de la orden .
 * 
 */

class OrderReference {

    public $ID;
    public $issueDate;

    public function __construct($ID, $issueDate) {
        $this->ID = $ID;
        $this->issueDate = $issueDate;
    }

    public function toXML($dom) {

        $node = $dom->createElement('cac:OrderReference');

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueDate', $this->issueDate));

        return $node;
    }
}

?>