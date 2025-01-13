<?php

/**
 * Class AccountingSupplierParty
 * 
 * Grupo con información que definen el obligado a facturar: Emisor de la factura
 * 
 * @property string $additionalAccountID, Identificador de tipo de organización jurídica de la de persona.
 * @property Party $party Grupo con información generales sobre el obligado a Facturar.
 * 
 */

class AccountingSupplierParty {
    public $additionalAccountID;
    public $party;
    public $attributes;

    public function __construct(
        $additionalAccountID,
        Party $party,
        $attributes = array()
    ) {
        $this->additionalAccountID = $additionalAccountID;
        $this->party = $party;
        $this->attributes = $attributes;
    }

    public function toXML($dom) {
        
        $node = $dom->createElement('cac:AccountingSupplierParty');

        $additionalAccounNode = $dom->createElement('cbc:AdditionalAccountID', $this->additionalAccountID);

        foreach ($this->attributes as $key => $value) {
            $additionalAccounNode->setAttribute($key, $value);
        }

        $node->appendChild($additionalAccounNode);
    
        $node->appendChild($this->party->toXML($dom));
        
    
        return $node;
    }
    
}

?>
