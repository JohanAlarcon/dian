<?php

class Response {
    public $responseCode;
    public $description;

    public function __construct(
        $responseCode,
        $description
    ) {
        $this->responseCode = $responseCode;
        $this->description = $description;
    }

    public function toXML($dom) {
        
        $node = $dom->createElement('cac:Response');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ResponseCode', $this->responseCode));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Description', $this->description));
    
        return $node;
    }
    
}

?>
