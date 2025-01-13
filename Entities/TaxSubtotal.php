<?php
/**
 * Class TaxSubtotal
 *
 * Grupo de información que definen los valores del tributo
 *
 * @property string $taxableAmount Base Imponible sobre la que se calcula el valor del tributo
 * @property array $taxableAmountAttributes Código de moneda de la transacción, debe ser COP.
 * @property string $taxAmount Valor del tributo: producto del porcentaje aplicado sobre la base imponible.
 * @property array $taxAmountAttributes Código de moneda de la transacción, debe ser COP.
 * @property TaxCategory $taxCategory Grupo de información sobre el tributo 
 * 
 */
class TaxSubtotal {
    public $taxableAmount;
    public $taxableAmountAttributes = [];
    public $taxAmount;
    public $taxAmountAttributes = [];
    public $taxCategory;
    public $PerUnitAmount;

    public function __construct(
        $taxableAmount,
        $taxableAmountAttributes,
        $taxAmount,
        $taxAmountAttributes,
        TaxCategory $taxCategory,
        $PerUnitAmount = null
    ) {
        $this->taxableAmount = $taxableAmount;
        $this->taxableAmountAttributes = $taxableAmountAttributes;
        $this->taxAmount = $taxAmount;
        $this->taxAmountAttributes = $taxAmountAttributes;
        $this->taxCategory = $taxCategory;
        $this->PerUnitAmount = $PerUnitAmount;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:TaxSubtotal');

        $taxableAmountNode = $dom->createElement('cbc:TaxableAmount', $this->taxableAmount);
        
        foreach ($this->taxableAmountAttributes as $key => $value) {
            $taxableAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($taxableAmountNode);

        $taxAmountNode = $dom->createElement('cbc:TaxAmount', $this->taxAmount);
        
        foreach ($this->taxAmountAttributes as $key => $value) {
            $taxAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($taxAmountNode);

        if ($this->PerUnitAmount != null) {
            $nodePerUnitAmount = XMLGenerator::createNode($dom, 'cbc:PerUnitAmount', $this->PerUnitAmount);
            $nodePerUnitAmount->setAttribute('currencyID', 'COP');
            $node->appendChild($nodePerUnitAmount);
        }
        
        $node->appendChild($this->taxCategory->toXML($dom));
    
        return $node;
    }
    
}

?>
