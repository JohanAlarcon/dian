<?php
/**
 * 
 * Clase PartyTaxScheme
 * 
 * Grupo de información tributarias del emisor.
 * 
 * @property string $registrationName Nombre o Razón Social del emisor.
 * @property CompanyID $companyID ID del Participante del consorcio.
 * @property string $taxLevelCode Obligaciones o responsabilidades del contribuyente; incluye el régimen al que pertenece el emisor.
 * @property Address $registrationAddress Grupo para informar Direccion fiscal.
 * @property TaxScheme $taxScheme Grupo de detalles tributarios del emisor.
 * Atributos del nodo TaxLevelCode
 * @listName Régimen al que pertenece el emisor del consorcio
 */
class PartyTaxScheme {
    public $registrationName;
    public $companyID;
    public $taxLevelCode;
    public $registrationAddress;
    public $taxScheme;
    public $taxLevelCodeAttributes = [];

    public function __construct(
        $registrationName,
        $companyID,
        $taxLevelCode,
        Address $registrationAddress = null,
        TaxScheme $taxScheme,
        $taxLevelCodeAttributes = []
    ) {
        $this->registrationName = $registrationName;
        $this->companyID = $companyID;
        $this->taxLevelCode = $taxLevelCode;
        $this->registrationAddress = $registrationAddress;
        $this->taxScheme = $taxScheme;
        $this->taxLevelCodeAttributes = $taxLevelCodeAttributes;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:PartyTaxScheme');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:RegistrationName', $this->registrationName));
        $node->appendChild($this->companyID->toXML($dom));

        // Agregar TaxLevelCode si hay atributos o valor

        if (!empty($this->taxLevelCodeAttributes) || !empty($this->taxLevelCode)) {
            $taxLevelCodeNode = $dom->createElement('cbc:TaxLevelCode', $this->taxLevelCode);

            foreach ($this->taxLevelCodeAttributes as $key => $value) {
                $taxLevelCodeNode->setAttribute($key, $value);
            }

            $node->appendChild($taxLevelCodeNode);
        }

        // Agregar RegistrationAddress si no es nulo
        if ($this->registrationAddress !== null) {
            $node->appendChild($this->registrationAddress->toXML($dom, 'cac:RegistrationAddress'));
        }

        $node->appendChild($this->taxScheme->toXML($dom));
        
        return $node;
    }
}
?>
