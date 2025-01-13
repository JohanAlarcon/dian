<?php
/**
 * 
 * Clase Price
 * 
 * Grupo de información que describen los precios del artículo o servicio
 * 
 * @property string $priceAmount Valor del artículo o servicio.
 * @property array $priceAmountAttributes Atributos del precio de la línea.
 * @property string $baseQuantity La cantidad real sobre la cual el precio aplica .
 * @property array $baseQuantityAttributes Atributos de la cantidad base.
 * 
 */
class Price {
    public $priceAmount;
    public $priceAmountAttributes = [];
    public $baseQuantity;
    public $baseQuantityAttributes = [];

    public function __construct(
        $priceAmount,
        $priceAmountAttributes,
        $baseQuantity,
        $baseQuantityAttributes
    ) {
        $this->priceAmount = $priceAmount;
        $this->priceAmountAttributes = $priceAmountAttributes;
        $this->baseQuantity = $baseQuantity;
        $this->baseQuantityAttributes = $baseQuantityAttributes;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:Price');
        
        $priceAmountNode = $dom->createElement('cbc:PriceAmount', $this->priceAmount);

        foreach ($this->priceAmountAttributes as $key => $value) {
            $priceAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($priceAmountNode);

        $baseQuantityNode = $dom->createElement('cbc:BaseQuantity', $this->baseQuantity);

        foreach ($this->baseQuantityAttributes as $key => $value) {
            $baseQuantityNode->setAttribute($key, $value);
        }

        $node->appendChild($baseQuantityNode);

        return $node;
    }
    
}

?>
