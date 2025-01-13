<?php

class DocumentResponse {
    public $response;
    public $documentReference;
    public $lineResponse;

    public function __construct(
        Response $response,
        DocumentReference $documentReference,
        LineResponse $lineResponse
    ) {
        $this->response = $response;
        $this->documentReference = $documentReference;
        $this->lineResponse = $lineResponse;
    }

    public function toXML(DOMDocument $doc) {
        $documentResponseNode = $doc->createElement('cac:DocumentResponse');
        
        $documentResponseNode->appendChild($this->response->toXML($doc));
        $documentResponseNode->appendChild($this->documentReference->toXML($doc));
        $documentResponseNode->appendChild($this->lineResponse->toXML($doc));

        return $documentResponseNode;
    }
}


?>