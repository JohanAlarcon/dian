<?php

/**
 * Class PartyName
 * 
 * Grupo con información sobre el nombre comercial del emisor.
 * 
 * @property string $name Nombre comercial del emisor.
 */
class PartyName {
    public $name;

    public function __construct(
        $name
    ) {
        $this->name = $name;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:PartyName');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Name', $this->name));
    
        return $node;
    }
    
}

?>
