<?php

class UBLExtension {
    public $extensionContent;

    public function __construct($extensionContent) {
        $this->extensionContent = $extensionContent; // Este será el nodo principal, como DianExtensions
    }

    public function toXML($dom) {
        $node = $dom->createElement('ext:UBLExtension');
        $node->appendChild($this->extensionContent->toXML($dom)); // Agregar contenido dinámico
        return $node;
    }
}

?>
