<?php
/**
 * 
 * class PhysicalLocation
 * 
 * Grupo con información con respeto a la localización física del emisor.
 * 
 * @property Address $address Grupo con datos de una persona o entidad sobre la Direccion del lugar físico de expedición del documento.
 * 
 */
class PhysicalLocation {
    public $address;

    public function __construct(
        Address $address
    ) {
        $this->address = $address;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:PhysicalLocation');
    
        $node->appendChild($this->address->toXML($dom,'cac:Address'));
        
    
        return $node;
    }
    
}

?>
