<?php
/**
 * 
 * class Party
 * 
 * Grupo con información generales sobre el obligado a Facturar.
 * 
 * @property string $industryClassificationCode Código de clasificación de la industria, Corresponde al código de actividad económica CIIU.
 * @property PartyName $partyName, Grupo con información sobre el nombre comercial del emisor.
 * @property PhysicalLocation $physicalLocation, Grupo con información con respeto a la localización física del emisor.
 * @property PartyTaxScheme $partyTaxScheme, Grupo de información tributarias del emisor.
 * @property PartyLegalEntity $partyLegalEntity, Grupo de información legales del emisor .
 * @property Contact $contact, Grupo de detalles con información de contacto del emisor.
 * 
 */
class Party {
    public $industryClassificationCode;
    public $partyName;
    public $physicalLocation;
    public $partyTaxScheme;
    public $partyLegalEntity;
    public $contact;

    public function __construct(
        $industryClassificationCode, 
        PartyName $partyName= null, 
        PhysicalLocation $physicalLocation = null, 
        PartyTaxScheme $partyTaxScheme,
        PartyLegalEntity $partyLegalEntity, 
        Contact $contact) {
        $this->industryClassificationCode = $industryClassificationCode;
        $this->partyName = $partyName;
        $this->physicalLocation = $physicalLocation;
        $this->partyTaxScheme = $partyTaxScheme;
        $this->partyLegalEntity = $partyLegalEntity;
        $this->contact = $contact;
    }

    public function toXML(DOMDocument $doc,$includeIndustryClassificationCode = true) {

        $partyNode = $doc->createElement('cac:Party');

        if($includeIndustryClassificationCode){
            $partyNode->appendChild(XMLGenerator::createNode($doc, 'cbc:IndustryClassificationCode', $this->industryClassificationCode));
        }
        
        if($this->partyName){
            $partyNode->appendChild($this->partyName->toXML($doc));
        }

        if($this->physicalLocation){
            $partyNode->appendChild($this->physicalLocation->toXML($doc));
        }

        $partyNode->appendChild($this->partyTaxScheme->toXML($doc));
        $partyNode->appendChild($this->partyLegalEntity->toXML($doc));
        $partyNode->appendChild($this->contact->toXML($doc));

        return $partyNode;
    }
}


?>