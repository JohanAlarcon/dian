<?php

class AuthorizationPeriod {
    public $startDate;
    public $endDate;

    public function __construct($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function toXML($dom) {
        $node = $dom->createElement('sts:AuthorizationPeriod');
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:StartDate', $this->startDate));
        $node->appendChild(XMLGenerator::createNode($dom, 'cbc:EndDate', $this->endDate));
        return $node;
    }
}

?>