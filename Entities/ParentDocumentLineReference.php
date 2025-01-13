<?php

class ParentDocumentLineReference
{

    public $lineID;
    public $ID;
    public $UUID;
    public $IssueDate;
    public $ValidationResultCode;
    public $ValidationDate;
    public $ValidationTime;
    public $externalReferenceApplicationResponse;

    public function __construct(
        $lineID,
        $ID,
        $UUID,
        $IssueDate,

        $ValidationResultCode,
        $ValidationDate,
        $ValidationTime,
        ExternalReference $externalReferenceApplicationResponse
    ) {
        $this->lineID = $lineID;
        $this->ID = $ID;
        $this->UUID = $UUID;
        $this->IssueDate = $IssueDate;

        $this->ValidationResultCode = $ValidationResultCode;
        $this->ValidationDate = $ValidationDate;
        $this->ValidationTime = $ValidationTime;
        $this->externalReferenceApplicationResponse = $externalReferenceApplicationResponse;
    }

    public function toXML($dom)
    {

        $node = $dom->createElement('cac:ParentDocumentLineReference');

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:LineID', $this->lineID));
        $documentReferenceNode = $dom->createElement('cac:DocumentReference');
        $documentReferenceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));

        $nodeUUID = XMLGenerator::createNode($dom, 'cbc:UUID', $this->UUID);

        $nodeUUID->setAttribute('schemeName', 'CUDE-'.$this->UUID);

        $documentReferenceNode->appendChild($nodeUUID);

        $documentReferenceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueDate', $this->IssueDate));
        $documentReferenceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:DocumentType', 'ApplicationResponse'));

        $externalReferenceNode = $this->externalReferenceApplicationResponse->toXML($dom);
        $documentReferenceNode->appendChild($externalReferenceNode);

        $resultOfVerificationNode = $dom->createElement('cac:ResultOfVerification');
        $resultOfVerificationNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ValidatorID', "Unidad Especial Direccion de Impuestos y Aduanas Nacionales"));
        $resultOfVerificationNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ValidationResultCode', $this->ValidationResultCode));
        $resultOfVerificationNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ValidationDate', $this->ValidationDate));
        $resultOfVerificationNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ValidationTime', $this->ValidationTime));

        $documentReferenceNode->appendChild($resultOfVerificationNode);

        $node->appendChild($documentReferenceNode);

        return $node;
        
    }
}
