<?php

class SoftwareProvider {
    public $providerID;
    public $softwareID;

    // Atributos de las etiquetas
    public $providerAttributes = [];
    public $softwareAttributes = [];

    public function __construct($providerID, $softwareID, $providerAttributes = [], $softwareAttributes = []) {
        $this->providerID = $providerID;
        $this->softwareID = $softwareID;

        // Asignar atributos a cada etiqueta
        $this->providerAttributes = $providerAttributes;
        $this->softwareAttributes = $softwareAttributes;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('sts:SoftwareProvider');

        // Crear el nodo sts:ProviderID con atributos
        $providerNode = $dom->createElement('sts:ProviderID', $this->providerID);
        foreach ($this->providerAttributes as $key => $value) {
            $providerNode->setAttribute($key, $value);
        }
        $node->appendChild($providerNode);

        // Crear el nodo sts:SoftwareID con atributos
        $softwareNode = $dom->createElement('sts:SoftwareID', $this->softwareID);
        foreach ($this->softwareAttributes as $key => $value) {
            $softwareNode->setAttribute($key, $value);
        }
        $node->appendChild($softwareNode);

        return $node;
    }
}

?>
