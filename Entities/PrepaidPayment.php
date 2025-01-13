<?php
/**
 * Class PrepaidPayment
 *
 * Grupo de campos para información relacionadas con un anticipo
 *
 * @property string $ID Identificador del pago prepagado.
 * @property string $paidAmount Monto pagado.
 * @property array $paidAmountAttributes Atributos del monto pagado, debe ser COP.
 * 
 */
class PrepaidPayment {
    public $ID;
    public $paidAmount;
    public $paidAmountAttributes = [];

    public function __construct(
        $ID,
        $paidAmount,
        $paidAmountAttributes
    ) {
        $this->ID = $ID;
        $this->paidAmount = $paidAmount;
        $this->paidAmountAttributes = $paidAmountAttributes;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:PrepaidPayment');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));
    
        $paidAmountNode = $dom->createElement('cbc:PaidAmount', $this->paidAmount);
        
        foreach ($this->paidAmountAttributes as $key => $value) {
            $paidAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($paidAmountNode);
    
        return $node;
    }
    
}

?>
