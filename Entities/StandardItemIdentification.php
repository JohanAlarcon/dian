<?php
/**
 * 
 * Clase StandardItemIdentification
 * 
 * Grupo de datos de identificación del artículo o servicio de acuerdo con un estándar
 * 
 * @property string $ID Código de acuerdo con el estándar descrito en el atributo ID/@schemeAgencyID.
 * @property array $IDAttributes Atributos del identificador del artículo o servicio.
 * 
 */
class StandardItemIdentification {
    public $ID;
    public $IDAttributes = [];

    public function __construct(
        $ID,
        $IDAttributes
    ) {
        $this->ID = $ID;
        $this->IDAttributes = $IDAttributes;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:StandardItemIdentification');
        
        $IDNode = $dom->createElement('cbc:ID', $this->ID);

        foreach ($this->IDAttributes as $key => $value) {
            $IDNode->setAttribute($key, $value);
        }

        $node->appendChild($IDNode);
        
    
        return $node;
    }
    
}

?>
