<?php
/**
 * 
 * Grupo con datos de una persona o entidad sobre la Direccion del lugar físico de expedición del documento.
 * 
 * @property string $ID Identificador de la Direccion.
 * @property string $cityName Nombre de la ciudad.
 * @property string $postalZone Código postal.
 * @property string $countrySubentity Subentidad territorial.
 * @property string $countrySubentityCode Código de la subentidad territorial.
 * @property AddressLine $addressLine Grupo con la Direccion de la entidad.
 * @property Country $country Grupo con la información del país.
 * 
 */
class Address {
    public $ID;
    public $cityName;
    public $postalZone;
    public $countrySubentity;
    public $countrySubentityCode;
    public $addressLine;
    public $country;
    public $District;

    public function __construct(
        $ID, 
        $cityName, 
        $postalZone,
        $countrySubentity,
        $countrySubentityCode,
        AddressLine $addressLine, 
        Country $country,
        $District = null
    ) {
        $this->ID = $ID;
        $this->cityName = $cityName;
        $this->postalZone = $postalZone;
        $this->countrySubentity = $countrySubentity;
        $this->countrySubentityCode = $countrySubentityCode;
        $this->addressLine = $addressLine;
        $this->country = $country;
        $this->District = $District;
    }

    public function toXML(DOMDocument $doc, $name) {
        $addressNode = $doc->createElement($name);

        $addressNode->appendChild(XMLGenerator::createNode($doc, 'cbc:ID', $this->ID));
        $addressNode->appendChild(XMLGenerator::createNode($doc, 'cbc:CityName', $this->cityName));
        $addressNode->appendChild(XMLGenerator::createNode($doc, 'cbc:PostalZone', $this->postalZone));
        $addressNode->appendChild(XMLGenerator::createNode($doc, 'cbc:CountrySubentity', $this->countrySubentity));
        $addressNode->appendChild(XMLGenerator::createNode($doc, 'cbc:CountrySubentityCode', $this->countrySubentityCode));

        if($this->District){
            $addressNode->appendChild(XMLGenerator::createNode($doc, 'cbc:District', $this->District));
        }
        
        $addressNode->appendChild($this->addressLine->toXML($doc));
        $addressNode->appendChild($this->country->toXML($doc));

        return $addressNode;
    }
}


?>