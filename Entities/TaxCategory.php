<?php
/**
 * Class TaxCategory
 *
 * Grupo de información sobre el tributo 
 *
 * @property string $percent Tarifa del tributo
 * @property TaxScheme $taxScheme Grupo de información sobre el esquema del impuesto
 * 
 */
class TaxCategory {
    public $percent;
    public $taxScheme;

    public function __construct(
        $percent,
        TaxScheme $taxScheme
    ) {
        $this->percent = $percent;
        $this->taxScheme = $taxScheme;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:TaxCategory');

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Percent', $this->percent));

        $node->appendChild($this->taxScheme->toXML($dom));
    
        return $node;
    }
    
}

?>
