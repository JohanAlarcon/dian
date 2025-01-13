<?php

/**
 * Class Delivery
 * 
 * Grupo de información para entrega de bienes 
 * 
 * @property string $actualDeliveryDate, Fecha efectiva de entrega de los bienes 
 * @property Address $deliveryAddress, Grupo con información con respeto a la Direccion de entrega 
 * 
 */
class Delivery {
    public $actualDeliveryDate;
    public $deliveryAddress;

    public function __construct(
        $actualDeliveryDate,
        Address $deliveryAddress
    ) {
        $this->actualDeliveryDate = $actualDeliveryDate;
        $this->deliveryAddress = $deliveryAddress;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:Delivery');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ActualDeliveryDate', $this->actualDeliveryDate));
    
        $node->appendChild($this->deliveryAddress->toXML($dom, 'cac:DeliveryAddress'));
        
    
        return $node;
    }
    
}

?>
