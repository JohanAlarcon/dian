<?php

class AuthorizedInvoices {
    public $prefix;
    public $from;
    public $to;

    public function __construct($prefix,$from,$to) {
        $this->prefix = $prefix;
        $this->from = $from;
        $this->to = $to;
    }

    public function toXML($dom) {
        $node = $dom->createElement('sts:AuthorizedInvoices');
        $node->appendChild(XMLGenerator::createNode($dom, 'sts:Prefix', $this->prefix));
        $node->appendChild(XMLGenerator::createNode($dom, 'sts:From', $this->from));
        $node->appendChild(XMLGenerator::createNode($dom, 'sts:To', $this->to));
        return $node;
    }
}

?>