<?php

/**
 * 
 * Clase PartyLegalEntity
 * 
 * Grupo de información legal de la empresa.
 * 
 * @property string $registrationName Nombre o Razón Social del emisor.
 * @property CompanyID $companyID NIT del emisor.
 * @property CorporateRegistrationScheme $corporateRegistrationScheme Grupo de información de registro del transportador.
 * 
 */
class PartyLegalEntity {
    public $registrationName;
    public $companyID;
    public $includeCorporateRegistrationScheme;
    public $corporateRegistrationScheme;

    public function __construct(
        $registrationName,
        CompanyID $companyID,
        $includeCorporateRegistrationScheme=false,
        $corporateRegistrationScheme=null
    ) {
        $this->registrationName = $registrationName;
        $this->companyID = $companyID;
        $this->includeCorporateRegistrationScheme = $includeCorporateRegistrationScheme;
        $this->corporateRegistrationScheme = $corporateRegistrationScheme;
    }

    public function toXML($dom) {
        // Crear el nodo principal
        $node = $dom->createElement('cac:PartyLegalEntity');
        
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:RegistrationName', $this->registrationName));
        
        $node->appendChild($this->companyID->toXML($dom));

        if ($this->includeCorporateRegistrationScheme) {
            $node->appendChild($this->corporateRegistrationScheme->toXML($dom));
        } 
        
        return $node;
    }
    
}
