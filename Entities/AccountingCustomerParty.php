<?php

/**
 * Class AccountingCustomerParty
 * 
 * Grupo con información que definen el obligado a facturar: Emisor de la factura
 * 
 * @property string $additionalAccountID, Identificador de tipo de organización jurídica de la de persona.
 * @property Party $party Grupo con información generales sobre el obligado a Facturar.
 * 
 */

class AccountingCustomerParty {
    public $additionalAccountID;
    public $party;
    public $includeIndustryClassificationCode;

    public function __construct(
        $additionalAccountID,
        Party $party,
        $includeIndustryClassificationCode = false
    ) {
        $this->additionalAccountID = $additionalAccountID;
        $this->party = $party;
        $this->includeIndustryClassificationCode = $includeIndustryClassificationCode;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:AccountingCustomerParty');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:AdditionalAccountID', $this->additionalAccountID));
    
        $node->appendChild($this->party->toXML($dom, $this->includeIndustryClassificationCode));
        
    
        return $node;
    }
    
}

?>
