<?php

/**
 * Class RequestedMonetaryTotal
 *
 * Grupo de campos para información relacionadas con los valores totales aplicables a la factura
 *
 * @property string $lineExtensionAmount Total Valor Bruto antes de tributos: Total valor bruto, suma de los valores brutos de las líneas de la factura.
 * @property array $lineExtensionAmountAttributes Código de moneda de la transacción, @currencyID -> COP.
 * @property string $taxExclusiveAmount Total Valor Base Imponible : Base imponible para el cálculo de los tributos.
 * @property array $taxExclusiveAmountAttributes Total Valor Base Imponible : Base imponible para el cálculo de los tributos, @currencyID -> COP.
 * @property string $taxInclusiveAmount Total de Valor Bruto más tributos .
 * @property array $taxInclusiveAmountAttributes Código de moneda de la transacción, @currencyID -> COP.
 * @property string $prepaidAmount Anticipo Total: Suma de todos los pagos anticipados.
 * @property array $prepaidAmountAttributes Código de moneda de la transacción, @currencyID -> COP.
 * @property string $payableAmount Valor de la Factura: Valor total de ítems (incluyendo cargos y descuentos a nivel de ítems)+valor tributos + valor cargos – valor descuentos.
 * @property array $payableAmountAttributes Código de moneda de la transacción, @currencyID -> COP.
 * 
 */
class RequestedMonetaryTotal
{
    public $lineExtensionAmount;
    public $lineExtensionAmountAttributes = [];
    public $taxExclusiveAmount;
    public $taxExclusiveAmountAttributes = [];
    public $taxInclusiveAmount;
    public $taxInclusiveAmountAttributes = [];
    public $prepaidAmount;
    public $prepaidAmountAttributes = [];
    public $payableAmount;
    public $payableAmountAttributes = [];
    public $PayableRoundingAmount;

    public function __construct(
        $lineExtensionAmount,
        $lineExtensionAmountAttributes,
        $taxExclusiveAmount,
        $taxExclusiveAmountAttributes,
        $taxInclusiveAmount,
        $taxInclusiveAmountAttributes,
        $prepaidAmount = null,
        $prepaidAmountAttributes,
        $payableAmount,
        $payableAmountAttributes,
        $PayableRoundingAmount = null
    ) {
        $this->lineExtensionAmount = $lineExtensionAmount;
        $this->lineExtensionAmountAttributes = $lineExtensionAmountAttributes;
        $this->taxExclusiveAmount = $taxExclusiveAmount;
        $this->taxExclusiveAmountAttributes = $taxExclusiveAmountAttributes;
        $this->taxInclusiveAmount = $taxInclusiveAmount;
        $this->taxInclusiveAmountAttributes = $taxInclusiveAmountAttributes;
        $this->prepaidAmount = $prepaidAmount;
        $this->prepaidAmountAttributes = $prepaidAmountAttributes;
        $this->payableAmount = $payableAmount;
        $this->payableAmountAttributes = $payableAmountAttributes;
        $this->PayableRoundingAmount = $PayableRoundingAmount;
    }

    public function toXML($dom)
    {
        // Crear el nodo principal
        $node = $dom->createElement('cac:RequestedMonetaryTotal');

        $lineExtensionAmountNode = $dom->createElement('cbc:LineExtensionAmount', $this->lineExtensionAmount);

        foreach ($this->lineExtensionAmountAttributes as $key => $value) {
            $lineExtensionAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($lineExtensionAmountNode);

        $taxExclusiveAmountNode = $dom->createElement('cbc:TaxExclusiveAmount', $this->taxExclusiveAmount);

        foreach ($this->taxExclusiveAmountAttributes as $key => $value) {
            $taxExclusiveAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($taxExclusiveAmountNode);

        $taxInclusiveAmountNode = $dom->createElement('cbc:TaxInclusiveAmount', $this->taxInclusiveAmount);

        foreach ($this->taxInclusiveAmountAttributes as $key => $value) {
            $taxInclusiveAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($taxInclusiveAmountNode);

        if ($this->prepaidAmount != null) {

            $prepaidAmountNode = $dom->createElement('cbc:PrepaidAmount', $this->prepaidAmount);

            foreach ($this->prepaidAmountAttributes as $key => $value) {
                $prepaidAmountNode->setAttribute($key, $value);
            }

            $node->appendChild($prepaidAmountNode);
        }


        if ($this->PayableRoundingAmount != null) {

            $nodePayableRoundingAmount = XMLGenerator::createNode($dom, 'cbc:PayableRoundingAmount', $this->PayableRoundingAmount);
            $nodePayableRoundingAmount->setAttribute('currencyID', 'COP');
            $node->appendChild($nodePayableRoundingAmount);

        }

        $payableAmountNode = $dom->createElement('cbc:PayableAmount', $this->payableAmount);

        foreach ($this->payableAmountAttributes as $key => $value) {
            $payableAmountNode->setAttribute($key, $value);
        }

        $node->appendChild($payableAmountNode);

        return $node;
    }
}
