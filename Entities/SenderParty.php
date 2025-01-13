<?php

class SenderParty{

    public $partyTaxScheme;

    public function __construct(
        PartyTaxScheme $partyTaxScheme
    ) {
        $this->partyTaxScheme = $partyTaxScheme;
    }

    public function toXML($dom) {
     
        $senderPartyNode = $dom->createElement('cac:SenderParty');

        $senderPartyNode->appendChild($this->partyTaxScheme->toXML($dom));

        return $senderPartyNode;

    }

}

?>
