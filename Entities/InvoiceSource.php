<?php

class InvoiceSource {
    public $identificationCode;

    public function __construct($identificationCode) {
        $this->identificationCode = $identificationCode;
    }

    public function toXML($dom) {

        $IdentificationCodeAttributes = array(
            'listAgencyID' => '6',
            'listAgencyName' => 'United Nations Economic Commission for Europe',
            'listSchemeURI' => 'urn:oasis:names:specification:ubl:codelist:gc:CountryIdentificationCode-2.1',
        );

        $node = $dom->createElement('sts:InvoiceSource');
        
        $identificationCodeNode = $dom->createElement('cbc:IdentificationCode', $this->identificationCode);

        foreach ($IdentificationCodeAttributes as $key => $value) {
            $identificationCodeNode->setAttribute($key, $value);
        }

        $node->appendChild($identificationCodeNode);

        return $node;
    }
}

?>