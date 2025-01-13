<?php

class InvoiceLine {
    public $ID;
    public $IDAttributes = [];
    public $invoicedQuantity;
    public $invoicedQuantityAttributes = [];
    public $lineExtensionAmount ;
    public $lineExtensionAmountAttributes = [];
    public $FreeOfChargeIndicator;
    public $taxTotal;
    public $item;
    public $price;

    public function __construct(
        $ID,
        $IDAttributes,
        $invoicedQuantity,
        $invoicedQuantityAttributes,
        $lineExtensionAmount,
        $lineExtensionAmountAttributes,
        $FreeOfChargeIndicator=null,
        TaxTotal $taxTotal=null,
        Item $item,
        Price $price
    ) {
        $this->ID = $ID;
        $this->IDAttributes = $IDAttributes;
        $this->invoicedQuantity = $invoicedQuantity;
        $this->invoicedQuantityAttributes = $invoicedQuantityAttributes;
        $this->lineExtensionAmount = $lineExtensionAmount;
        $this->lineExtensionAmountAttributes = $lineExtensionAmountAttributes;
        $this->FreeOfChargeIndicator = $FreeOfChargeIndicator;
        $this->taxTotal = $taxTotal;
        $this->item = $item;
        $this->price = $price;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:InvoiceLine');
        
        $IDNode = $dom->createElement('cbc:ID', $this->ID);
        
        foreach ($this->IDAttributes as $key => $value) {
            $IDNode->setAttribute($key, $value);
        }

        $node->appendChild($IDNode);

        $invoicedQuantityNode = $dom->createElement('cbc:InvoicedQuantity', $this->invoicedQuantity);

        foreach ($this->invoicedQuantityAttributes as $key => $value) {
            $invoicedQuantityNode->setAttribute($key, $value);
        }

        $node->appendChild($invoicedQuantityNode);

        $lineExtensionAmountNode = $dom->createElement('cbc:LineExtensionAmount', $this->lineExtensionAmount);

        foreach ($this->lineExtensionAmountAttributes as $key => $value) {
            $lineExtensionAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($lineExtensionAmountNode);

        if ($this->FreeOfChargeIndicator !== null) {
            $node->appendChild(XMLGenerator::createNode($dom, 'cbc:FreeOfChargeIndicator', $this->FreeOfChargeIndicator));
        }
        
        if ($this->taxTotal !== null) {
            $node->appendChild($this->taxTotal->toXML($dom));
        }
        $node->appendChild($this->item->toXML($dom));
        $node->appendChild($this->price->toXML($dom));
    
        return $node;
    }
    
}

?>
