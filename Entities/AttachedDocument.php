<?php

class AttachedDocument {
    
    public $ublVersion;
    public $customizationID;
    public $profileID;
    public $profileExecutionID;
    public $ID;
    public $issueDate;
    public $issueTime;
    public $note;
    public $documentType;
    public $parentDocumentID;
    public $senderParty;
    public $receiverParty;
    public $externalReferenceInvoice;
    public $ParentDocumentLineReference;

    public function __construct(
        $ublVersion,
        $customizationID,
        $profileID,
        $profileExecutionID,
        $ID,
        $issueDate,
        $issueTime,
        $note,
        $documentType,
        $parentDocumentID,
        SenderParty $senderParty,
        ReceiverParty $receiverParty,
        ExternalReference $externalReferenceInvoice,
        ParentDocumentLineReference $ParentDocumentLineReference
    ) {
        $this->ublVersion = $ublVersion;
        $this->customizationID = $customizationID;
        $this->profileID = $profileID;
        $this->profileExecutionID = $profileExecutionID;
        $this->ID = $ID;
        $this->issueDate = $issueDate;
        $this->issueTime = $issueTime;
        $this->note = $note;
        $this->documentType = $documentType;
        $this->parentDocumentID = $parentDocumentID;
        $this->senderParty = $senderParty;
        $this->receiverParty = $receiverParty;
        $this->externalReferenceInvoice = $externalReferenceInvoice;
        $this->ParentDocumentLineReference = $ParentDocumentLineReference;
    }

    public function toXML() {
        
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;    // Para mejorar la legibilidad del XML
        $dom->xmlStandalone = false;  // standalone="no"

        // Crear nodo raíz Invoice
        $attachedDocumentNode = $dom->createElement('AttachedDocument');

        $attachedDocumentNode->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:AttachedDocument-2');
        $attachedDocumentNode->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $attachedDocumentNode->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $attachedDocumentNode->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $attachedDocumentNode->setAttribute('xmlns:ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
        $attachedDocumentNode->setAttribute('xmlns:xades', 'http://uri.etsi.org/01903/v1.3.2#');
        $attachedDocumentNode->setAttribute('xmlns:xades141', 'http://uri.etsi.org/01903/v1.4.1#');

        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:UBLVersionID', $this->ublVersion));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:CustomizationID', $this->customizationID));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ProfileID', $this->profileID));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ProfileExecutionID', $this->profileExecutionID));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));

        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueDate', $this->issueDate));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueTime', $this->issueTime));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:Note', $this->note));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:DocumentType', $this->documentType));
        $attachedDocumentNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ParentDocumentID', $this->parentDocumentID));

        $attachedDocumentNode->appendChild($this->senderParty->toXML($dom));
        $attachedDocumentNode->appendChild($this->receiverParty->toXML($dom));

        $attachedDocumentNode->appendChild($this->externalReferenceInvoice->toXML($dom));
        $attachedDocumentNode->appendChild($this->ParentDocumentLineReference->toXML($dom));

        $dom->appendChild($attachedDocumentNode);

        return $dom->saveXML();
    }
}

?>
