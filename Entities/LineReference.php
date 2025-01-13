<?php

class LineReference {
    public $lineID;

    public function __construct(
        $lineID
    ) {
        $this->lineID = $lineID;
    }

    public function toXML($dom) {
        
        $node = $dom->createElement('cac:LineReference');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:LineID', $this->lineID));
    
        return $node;
    }
    
}

?>
