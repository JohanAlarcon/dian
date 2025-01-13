<?php
/**
 * Class PaymentMeans
 * 
 * Grupo de campos para información relacionadas con el pago de la factura.
 * 
 * @property string $ID Identificador del medio de pago.
 * @property string $paymentMeansCode Código del medio de pago.
 * @property string $paymentDueDate Fecha de vencimiento de la factura.
 * 
 */
class PaymentMeans {
    public $ID;
    public $paymentMeansCode;
    public $paymentDueDate;
    public $PaymentID;
    public $attributesID;

    public function __construct(
        $ID,
        $paymentMeansCode,
        $paymentDueDate,
        $PaymentID = null,
        $attributesID = []
    ) {
        $this->ID = $ID;
        $this->paymentMeansCode = $paymentMeansCode;
        $this->paymentDueDate = $paymentDueDate;
        $this->PaymentID = $PaymentID;
        $this->attributesID = $attributesID;

    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:PaymentMeans');
        
        $nodeID = XMLGenerator::createNode($dom, 'cbc:ID', $this->ID);

        foreach ($this->attributesID as $key => $value) {
            $nodeID->setAttribute($key, $value);
        }

        $node->appendChild($nodeID);

        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:PaymentMeansCode', $this->paymentMeansCode));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:PaymentDueDate', $this->paymentDueDate));

        if($this->PaymentID){
            $node->appendChild(XMLGenerator::createNode($dom, 'cbc:PaymentID', $this->PaymentID));
        }
    
        return $node;
    }
    
}

?>
