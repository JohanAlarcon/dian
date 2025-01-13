<?php

class ReceiverParty{

    public $partyTaxScheme;

    public function __construct(
        PartyTaxScheme $partyTaxScheme
    ) {
        $this->partyTaxScheme = $partyTaxScheme;
    }

    public function toXML($dom) {
     
        $receiverPartyNode = $dom->createElement('cac:ReceiverParty');

        $receiverPartyNode->appendChild($this->partyTaxScheme->toXML($dom));

        return $receiverPartyNode;

    }

}

?>
