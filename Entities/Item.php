<?php
/**
 * Grupo de información que describen las características del artículo o servicio.
 * 
 * @property string $description Descripción del artículo o servicio a que se refiere esta línea de la factura.
 * @property StandardItemIdentification $standardItemIdentification Grupo de datos de identificación del artículo o servicio de acuerdo con un estándar.
 * @property AdditionalItemProperty $additionalItemProperty1 Grupo de información para adicionar información específica del ítem que puede ser solicitada por autoridades o entidades diferentes a la DIAN.
 * @property AdditionalItemProperty $additionalItemProperty2 Grupo de información para adicionar información específica del ítem que puede ser solicitada por autoridades o entidades diferentes a la DIAN.
 * @property AdditionalItemProperty $additionalItemProperty3 Grupo de información para adicionar información específica del ítem que puede ser solicitada por autoridades o entidades diferentes a la DIAN.
 */
class Item {
    public $description;
    public $standardItemIdentification;
    public $additionalItemProperty1;
    public $additionalItemProperty2;
    public $additionalItemProperty3;
    public $transporte = false;
    public $withSellersItemIdentification;

    public function __construct(
        $description,
        StandardItemIdentification $standardItemIdentification,
        AdditionalItemProperty $additionalItemProperty1,
        AdditionalItemProperty $additionalItemProperty2,
        AdditionalItemProperty $additionalItemProperty3,
        $transporte,
        $withSellersItemIdentification = false
    ) {
        $this->description = $description;
        $this->standardItemIdentification = $standardItemIdentification;
        $this->additionalItemProperty1 = $additionalItemProperty1;
        $this->additionalItemProperty2 = $additionalItemProperty2;
        $this->additionalItemProperty3 = $additionalItemProperty3;
        $this->transporte = $transporte;
        $this->withSellersItemIdentification = $withSellersItemIdentification;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:Item');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:Description', $this->description));

        if ($this->withSellersItemIdentification) {
            $sellersItemIdentificationNode = $dom->createElement('cac:SellersItemIdentification');
            $sellersItemIdentificationNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', 'N/A'));
            $node->appendChild($sellersItemIdentificationNode);
        }
        
        $node->appendChild($this->standardItemIdentification->toXML($dom));

        if ($this->transporte) {
            $node->appendChild($this->additionalItemProperty1->toXML($dom));
            $node->appendChild($this->additionalItemProperty2->toXML($dom));
            $node->appendChild($this->additionalItemProperty3->toXML($dom));
        }

    
        return $node;
    }
    
}

?>
