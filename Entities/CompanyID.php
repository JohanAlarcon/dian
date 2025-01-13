<?php
/**
 * 
 * Clase CompanyID
 * 
 * ID del Participante del consorcio.
 * 
 * @property string $value ID del Participante del consorcio.
 * @property array $attributes Atributos del nodo.
 * @schemeAgencyID Debe ser informado el literal “195”
 * @schemeAgencyName Debe ser informado el literal “CO, DIAN (Direccion de Impuestos y Aduanas Nacionales)”
 * @schemeID DV del NIT
 * @schemeName
 * 
 */
class CompanyID {
    private $value;
    private $attributes;

    public function __construct($value, $attributes = []) {
        $this->value = $value;
        $this->attributes = $attributes;
    }

    public function toXML(DOMDocument $doc) {
        // Crear el nodo <cbc:CompanyID>
        $node = $doc->createElement('cbc:CompanyID', $this->value);

        // Agregar los atributos al nodo
        foreach ($this->attributes as $key => $value) {
            $node->setAttribute($key, $value);
        }

        return $node;
    }
}

?>