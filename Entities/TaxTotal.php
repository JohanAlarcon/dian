<?php
/**
 * Class TaxTotal
 *
 * Grupo de campos para información total de impuestos
 *
 * @property string $taxAmount Monto total de impuestos
 * @property array $taxAmountAttributes Código de moneda de la transacción , debe ser COP.
 * @property TaxSubtotal $taxSubtotal Subtotal de impuestos
 * 
 */
class TaxTotal {
    public $taxAmount ;
    public $taxAmountAttributes = [];
    public $taxSubtotal;
    public $RoundingAmount;

    public function __construct(
        $taxAmount,
        $taxAmountAttributes,
        TaxSubtotal $taxSubtotal=null,
        $RoundingAmount = null
    ) {
        $this->taxAmount = $taxAmount;
        $this->taxAmountAttributes = $taxAmountAttributes;
        $this->taxSubtotal = $taxSubtotal;
        $this->RoundingAmount = $RoundingAmount;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:TaxTotal');

        $paidAmountNode = $dom->createElement('cbc:TaxAmount', $this->taxAmount);
        
        foreach ($this->taxAmountAttributes as $key => $value) {
            $paidAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($paidAmountNode);

        if ($this->RoundingAmount != null){
            $nodeRoundingAmount = XMLGenerator::createNode($dom, 'cbc:RoundingAmount', $this->RoundingAmount);
            $nodeRoundingAmount->setAttribute('currencyID', 'COP');
            $node->appendChild($nodeRoundingAmount);
        }

        if ($this->taxSubtotal != null){
            $node->appendChild($this->taxSubtotal->toXML($dom));
        }
    
        return $node;
    }
    
}

?>
