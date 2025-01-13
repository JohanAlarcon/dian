<?php

class AdditionalItemProperty {
    public $name;
    public $value;
    public $valueQuantity;
    public $valueQuantityAttributes = [];
    public $visibleValueQuantity = false;

    public function __construct(
        $name,
        $value,
        $valueQuantity,
        $valueQuantityAttributes,
        $visibleValueQuantity
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->valueQuantity = $valueQuantity;
        $this->valueQuantityAttributes = $valueQuantityAttributes;
        $this->visibleValueQuantity = $visibleValueQuantity;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:AdditionalItemProperty');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Name', $this->name));

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Value', $this->value));

        if ($this->visibleValueQuantity) {
            
            $valueQuantityNode = $dom->createElement('cbc:ValueQuantity', $this->valueQuantity);
    
            foreach ($this->valueQuantityAttributes as $key => $value) {
                $valueQuantityNode->setAttribute($key, $value);
            }
    
            $node->appendChild($valueQuantityNode);
        }

    
        return $node;
    }
    
}

?>
