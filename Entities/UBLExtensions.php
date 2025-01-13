<?php

class UBLExtensions {

    public $UBLExtension;

    public function __construct($UBLExtension) {
        $this->UBLExtension = $UBLExtension;
    }

    public function toXML($dom) {
        $node = $dom->createElement('ext:UBLExtensions');
        $node->appendChild($this->UBLExtension->toXML($dom)); // Agregar contenido dinámico
        return $node;
    }
}

?>