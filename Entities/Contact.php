<?php
/**
 * 
 * Clase Contact
 * 
 * Grupo de detalles con información de contacto del emisor.
 * 
 * @property string $telephone Número de teléfono, celular u otro.
 * @property string $electronicMail Correo electrónico de contacto.
 * 
 */
class Contact {
    public $telephone;
    public $electronicMail;

    public function __construct(
        $telephone = null,
        $electronicMail
    ) {
        $this->telephone = $telephone;
        $this->electronicMail = $electronicMail;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:Contact');
        
        if ($this->telephone) {
            $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Telephone', $this->telephone));

        }

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ElectronicMail', $this->electronicMail));        
    
        return $node;
    }
    
}

?>
