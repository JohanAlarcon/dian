<?php

class ExtensionContent {
    public $dianExtensions;

    public function __construct($dianExtensions) {
        $this->dianExtensions = $dianExtensions; // Este será el nodo principal, como DianExtensions
    }

    public function toXML($dom) {
        $node = $dom->createElement('ext:ExtensionContent');
        $node->appendChild($this->dianExtensions->toXML($dom)); // Agregar contenido dinámico
        return $node;
    }
}

?>
