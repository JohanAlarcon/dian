<?php
/**
 * 
 * Clase AddressLine
 * 
 * Grupo de elemento que identifica libremente la Direccion
 * 
 * @property string $line Línea de Direccion.
 * 
 */
class AddressLine {
    public $line;

    public function __construct(
        $line
    ) {
        $this->line = $line;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:AddressLine');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Line', $this->line));
    
        return $node;
    }
    
}

?>
