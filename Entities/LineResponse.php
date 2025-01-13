<?php

class LineResponse {
    public $lineReference;
    public $response;

    public function __construct(
        LineReference $lineReference,
        Response $response
    ) {
        $this->lineReference = $lineReference;
        $this->response = $response;
    }

    public function toXML(DOMDocument $doc) {
        $lineResponseNode = $doc->createElement('cac:LineResponse');
        
        $lineResponseNode->appendChild($this->lineReference->toXML($doc));
        $lineResponseNode->appendChild($this->response->toXML($doc));

        return $lineResponseNode;
    }
}


?>