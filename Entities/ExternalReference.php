<?php

class ExternalReference{

    public $mimeCode;
    public $encodingCode;
    public $description;

    public function __construct(
        $mimeCode,
        $encodingCode,
        $description
    ){
        $this->mimeCode = $mimeCode;
        $this->encodingCode = $encodingCode;
        $this->description = $description;
    }

    public function toXML($dom) {
        $node = $dom->createElement('cac:Attachment');
        $ExternalReference = $dom->createElement('cac:ExternalReference');
        $node->appendChild($ExternalReference);
    
        $ExternalReference->appendChild(XMLGenerator::createNode($dom, 'cbc:MimeCode', $this->mimeCode));
        $ExternalReference->appendChild(XMLGenerator::createNode($dom, 'cbc:EncodingCode', $this->encodingCode));
        
        // Crear un nodo CDATA para la descripción
        $descriptionNode = $dom->createElement('cbc:Description');
        $cdataSection = $dom->createCDATASection($this->description);
        $descriptionNode->appendChild($cdataSection);
        $ExternalReference->appendChild($descriptionNode);
    
        return $node;
    }

}

?>