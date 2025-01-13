<?php
/**
 * Clase que representa un esquema de impuestos
 * 
 * @property string $ID Identificador del esquema de impuestos
 * @property string $name Nombre del esquema de impuestos
 * 
 */
class TaxScheme {
    public $ID;
    public $name;

    public function __construct(
        $ID,
        $name
    ) {
        $this->ID = $ID;
        $this->name = $name;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:TaxScheme');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Name', $this->name));
    
        return $node;
    }
    
}

?>
