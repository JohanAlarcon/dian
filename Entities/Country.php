<?php
/**
 * 
 * CLase Country
 * 
 * Grupo con información sobre el país 
 * 
 * @property string $identificationCode Código de identificación del país, CO -> Colombia.
 * @property string $name Nombre del país.
 * @property string $languageID Identificador del lenguaje, es -> espanol.
 * 
 */
class Country {
    public $identificationCode;
    public $name;
    public $languageID;

    public function __construct(
        $identificationCode, 
        $name,
        $languageID
    ) {
        $this->identificationCode = $identificationCode;
        $this->name = $name;
        $this->languageID = $languageID;
    }

    public function toXML($dom) {

        $NameAttributes = [
            'languageID' => $this->languageID
        ];

        // Crear el nodo principal
        $node = $dom->createElement('cac:Country');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:IdentificationCode', $this->identificationCode));

        $nodeName = $dom->createElement('cbc:Name', $this->name);

        foreach ($NameAttributes as $key => $value) {
            $nodeName->setAttribute($key, $value);
        }

        $node->appendChild($nodeName);
    
        return $node;
    }
    
}

?>
