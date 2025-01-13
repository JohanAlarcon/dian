<?php

class AuthorizationProvider {
    public $authorizationProviderID;

    public function __construct($authorizationProviderID) {
        $this->authorizationProviderID = $authorizationProviderID;
    }

    public function toXML($dom) {

        $authorizationProviderAttributes = array(
            'schemeID' => '4',
            'schemeName' => '31',
            'schemeAgencyID' => '195',
            'schemeAgencyName' => 'CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)'
        );

        $node = $dom->createElement('sts:AuthorizationProvider');

        $providerNode = $dom->createElement('sts:AuthorizationProviderID', $this->authorizationProviderID);
        foreach ($authorizationProviderAttributes as $key => $value) {
            $providerNode->setAttribute($key, $value);
        }
        $node->appendChild($providerNode);

        return $node;
    }
}

?>
