<?php

class XMLGenerator {
    /**
     * Crea un nodo que pertenece al documento especificado.
     *
     * @param DOMDocument $doc El documento al que pertenecerá el nodo.
     * @param string $name El nombre del nodo.
     * @param string $value El valor del nodo.
     * @return DOMElement El nodo creado.
     */
    public static function createNode(DOMDocument $doc, $name, $value) {
        $element = $doc->createElement($name, htmlspecialchars($value));
        return $element;
    }
}
?>
