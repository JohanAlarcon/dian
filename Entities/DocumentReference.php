<?php

class DocumentReference {
    public $ID;
    public $UUID;
    public $UUIDAtributes;

    public function __construct(
        $ID,
        $UUID,
        $UUIDAtributes
    ) {
        $this->ID = $ID;
        $this->UUID = $UUID;
        $this->UUIDAtributes = $UUIDAtributes;
    }

    public function toXML($dom) {
        
        $node = $dom->createElement('cac:DocumentReference');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));

        $UUIDNode = $dom->createElement('cbc:UUID', $this->UUID);
        foreach ($this->UUIDAtributes as $key => $value) {
            $UUIDNode->setAttribute($key, $value);
        }
        $node->appendChild($UUIDNode);
    
        return $node;
    }
    
}

?>
