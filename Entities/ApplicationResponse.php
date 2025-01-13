<?php

class ApplicationResponse {
    
    public $UBLExtensions;
    public $ublVersion;
    public $customizationID;
    public $profileID;
    public $profileExecutionID;
    public $ID;
    public $UUID;
    public $UUIDAtributes;
    public $issueDate;
    public $issueTime;
    public $note;
    public $documentType;
    public $parentDocumentID;
    public $senderParty;
    public $receiverParty;
    public $documentResponse;

    public function __construct(
        UBLExtensions $ublExtensions,
        $ublVersion,
        $customizationID,
        $profileID,
        $profileExecutionID,
        $ID,
        $UUID,
        $UUIDAtributes = [],
        $issueDate,
        $issueTime,
        SenderParty $senderParty,
        ReceiverParty $receiverParty,
        DocumentResponse $documentResponse
    ) {
        $this->UBLExtensions = $ublExtensions;
        $this->ublVersion = $ublVersion;
        $this->customizationID = $customizationID;
        $this->profileID = $profileID;
        $this->profileExecutionID = $profileExecutionID;
        $this->ID = $ID;
        $this->UUID = $UUID;
        $this->UUIDAtributes = $UUIDAtributes;
        $this->issueDate = $issueDate;
        $this->issueTime = $issueTime;
        $this->senderParty = $senderParty;
        $this->receiverParty = $receiverParty;
        $this->documentResponse = $documentResponse;
    }

    public function toXML() {
        
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;    // Para mejorar la legibilidad del XML
        $dom->xmlStandalone = false;  // standalone="no"

        // Crear nodo raíz Invoice
        $applicationResponseNode = $dom->createElement('ApplicationResponse');

        $applicationResponseNode->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $applicationResponseNode->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $applicationResponseNode->setAttribute('xmlns:ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
        $applicationResponseNode->setAttribute('xmlns:sts', 'dian:gov:co:facturaelectronica:Structures-2-1');
        $applicationResponseNode->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $applicationResponseNode->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:ApplicationResponse-2');
        
        $applicationResponseNode->appendChild($this->UBLExtensions->toXML($dom));

        $applicationResponseNode->appendChild(XMLGenerator::createNode($dom, 'cbc:UBLVersionID', $this->ublVersion));
        $applicationResponseNode->appendChild(XMLGenerator::createNode($dom, 'cbc:CustomizationID', $this->customizationID));
        $applicationResponseNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ProfileID', $this->profileID));
        $applicationResponseNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ProfileExecutionID', $this->profileExecutionID));
        $applicationResponseNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));

        $UUIDNode = $dom->createElement('cbc:UUID', $this->UUID);
        foreach ($this->UUIDAtributes as $key => $value) {
            $UUIDNode->setAttribute($key, $value);
        }
        $applicationResponseNode->appendChild($UUIDNode);
        
        $applicationResponseNode->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueDate', $this->issueDate));
        $applicationResponseNode->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueTime', $this->issueTime));
        
        $applicationResponseNode->appendChild($this->senderParty->toXML($dom));
        $applicationResponseNode->appendChild($this->receiverParty->toXML($dom));

        $applicationResponseNode->appendChild($this->documentResponse->toXML($dom));

        $dom->appendChild($applicationResponseNode);

        return $dom->saveXML();
    }
}

?>
