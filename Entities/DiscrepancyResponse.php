<?php

/**
 * Clase que representa el nodo DiscrepancyResponse.
 * Grupo de campos para información que describen una orden de pedido para esta factura.
 * @property string ID Prefijo y Número del documento orden referenciado.
 * @property string issueDate Fecha de emisión: Fecha de emisión de la orden .
 * 
 */

class DiscrepancyResponse
{

    public $ReferenceID;
    public $ResponseCode;

    public function __construct($ReferenceID, $ResponseCode)
    {
        $this->ReferenceID = $ReferenceID;
        $this->ResponseCode = $ResponseCode;
    }

    public function toXML($dom)
    {

        $node = $dom->createElement('cac:DiscrepancyResponse');

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ReferenceID', $this->ReferenceID));

        $responseCode = $dom->createElement('cbc:ResponseCode', $this->ResponseCode);
        $responseCode->setAttribute('listName', '');
        $responseCode->setAttribute('listURI', '');
        $responseCode->setAttribute('name', '');
        $node->appendChild($responseCode);

        return $node;
    }
}
