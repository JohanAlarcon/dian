<?php
/**
 * 
 * Clase CorporateRegistrationScheme
 * 
 * Grupo de información de registro del emisor.
 * 
 * @property string $ID Prefijo de la facturación usada para el punto de venta.
 * 
 */
class CorporateRegistrationScheme {
    public $ID;
    public $Name;

    public function __construct(
        $ID,
        $Name = null
    ) {
        $this->ID = $ID;
        $this->Name = $Name;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:CorporateRegistrationScheme');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));

        if ($this->Name) {
            $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Name', $this->Name));
        }
        
        return $node;
    }
    
}

?>
