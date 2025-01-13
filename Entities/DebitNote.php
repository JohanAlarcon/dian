<?php

/**
 * Class DebitNote
 *
 * Representa una factura electrónica conforme al estándar UBL 2.1.
 * Factura Electrónica - DebitNote (raíz)
 *
 * @property string $UBLVersionID       Versión base de UBL usada para crear este perfil, Debe ser "2.1".
 * @property string $CustomizationID    Indicador del tipo de operación: 
 *                                      - 09->AIU, 10->Estándar, 11->Mandatos, 12->Transporte, 14->Notarios, 
 *                                      - 15->Compra Divisas, 16->Venta Divisas.
 * @property string $ProfileID          Versión del Formato, p.ej. "DIAN 2.1: Factura Electrónica de Venta"
 * @property UBLExtensions $UBLExtensions Extensiones específicas del estándar UBL.
 * @property string $ProfileExecutionID Identificador del perfil de ejecución, 1->Producción, 2->Pruebas.
 * @property string $ID                 Número de documento (factura o factura cambiaria). Incluye prefijo + consecutivo autorizado por la DIAN.
 * @property string $UUID               CUFE: Código Único de Facturación Electrónica.
 * @property array  $UUIDAtributes      Atributos del UUID, p.ej. schemeName -> CUDE-SHA384, CUFE-SHA384.
 * @property string $IssueDate          Fecha de emisión de la factura.
 * @property string $IssueTime          Hora de emisión de la factura.
 * @property string $DebitNoteTypeCode    Código del tipo de factura (01, 02, 03, 04, 91, 92, 96).
 * @property string $Note               Nota de la factura, texto libre.
 * @property string $DocumentCurrencyCode Divisa de la factura, debe ser "COP".
 * @property string $LineCountNumeric   Cantidad de líneas de detalle (InvoiceLine).
 *
 * @property InvoicePeriod             $InvoicePeriod             Información de orden de referencia.
 * @property DiscrepancyResponse       $DiscrepancyResponse      Información de discrepancias.
 * @property BillingReference          $BillingReference         Información de referencia a facturas anteriores.
 * @property AccountingSupplierParty   $AccountingSupplierParty  Emisor de la factura.
 * @property AccountingCustomerParty   $AccountingCustomerParty  Receptor de la factura.
 * @property PaymentMeans              $PaymentMeans             Medios de pago.
 * @property PrepaidPayment            $PrepaidPayment           Información sobre anticipos.
 * @property TaxTotal                  $TaxTotal                 Información total de impuestos.
 * @property RequestedMonetaryTotal        $RequestedMonetaryTotal       Totales monetarios.
 * @property array $invoiceline        Líneas de la factura (InvoiceLine).
 */
class DebitNote
{
    // ------------------------------------------------------------------------------------------
    // Propiedades generales de configuración UBL
    // ------------------------------------------------------------------------------------------
    public $ublVersion;
    public $customizationID;
    public $profileID;
    public $UBLExtensions;
    public $profileExecutionID;

    // ------------------------------------------------------------------------------------------
    // Datos específicos de la factura
    // ------------------------------------------------------------------------------------------
    public $ID;
    public $UUID;
    public $UUIDAtributes = [];
    public $issueDate;
    public $issueTime;
    //public $DebitNoteTypeCode;
    public $note;
    public $TaxPointDate;
    public $lineCountNumeric;

    // ------------------------------------------------------------------------------------------
    // Información agregada (referencias, partes, detalles)
    // ------------------------------------------------------------------------------------------
    public $DiscrepancyResponse;
    public $BillingReference;
    public $accountingSupplierParty;
    public $accountingCustomerParty;
    public $paymentMeans;
    public $prepaidPayment;
    public $taxTotal;
    public $requestedMonetaryTotal;
    public $invoiceline = [];

    /**
     * Constructor de la clase DebitNote.
     *
     * @param string                 $ublVersion
     * @param string                 $customizationID
     * @param string                 $profileID
     * @param UBLExtensions          $UBLExtensions
     * @param string                 $profileExecutionID
     * @param string                 $ID
     * @param string                 $UUID
     * @param array                  $UUIDAtributes
     * @param string                 $issueDate
     * @param string                 $issueTime
     * @param string                 $DebitNoteTypeCode
     * @param string                 $note
     * @param string                 $lineCountNumeric
     * @param DiscrepancyResponse    $DiscrepancyResponse
     * @param BillingReference       $BillingReference
     * @param AccountingSupplierParty $accountingSupplierParty
     * @param AccountingCustomerParty $accountingCustomerParty
     * @param PaymentMeans           $paymentMeans
     * @param PrepaidPayment         $prepaidPayment
     * @param TaxTotal               $taxTotal
     * @param RequestedMonetaryTotal     $requestedMonetaryTotal
     * @param array                  $invoiceline
     */
    public function __construct(
        $ublVersion,
        $customizationID,
        $profileID,
        UBLExtensions $UBLExtensions,
        $profileExecutionID,
        $ID,
        $UUID,
        $UUIDAtributes,
        $issueDate,
        $issueTime,
        $note,
        $TaxPointDate,
        $lineCountNumeric,
        DiscrepancyResponse $DiscrepancyResponse = null,
        BillingReference $BillingReference = null,
        AccountingSupplierParty $accountingSupplierParty,
        AccountingCustomerParty $accountingCustomerParty,
        PaymentMeans $paymentMeans,
        PrepaidPayment $prepaidPayment,
        TaxTotal $taxTotal,
        RequestedMonetaryTotal $requestedMonetaryTotal,
        $invoiceline
    ) {
        $this->ublVersion = $ublVersion;
        $this->customizationID = $customizationID;
        $this->profileID = $profileID;
        $this->UBLExtensions = $UBLExtensions;
        $this->profileExecutionID = $profileExecutionID;
        $this->ID = $ID;
        $this->UUID = $UUID;
        $this->UUIDAtributes = $UUIDAtributes;
        $this->issueDate = $issueDate;
        $this->issueTime = $issueTime;
        //$this->DebitNoteTypeCode = $DebitNoteTypeCode;
        $this->note = $note;
        $this->TaxPointDate = $TaxPointDate;
        $this->lineCountNumeric = $lineCountNumeric;
        $this->DiscrepancyResponse = $DiscrepancyResponse;
        $this->BillingReference = $BillingReference;
        $this->accountingSupplierParty = $accountingSupplierParty;
        $this->accountingCustomerParty = $accountingCustomerParty;
        $this->paymentMeans = $paymentMeans;
        $this->prepaidPayment = $prepaidPayment;
        $this->taxTotal = $taxTotal;
        $this->requestedMonetaryTotal = $requestedMonetaryTotal;
        $this->invoiceline = $invoiceline;
    }

    /**
     * Genera el XML de la factura.
     *
     * @return string Contenido XML en formato string.
     */
    public function toXML()
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;    // Para mejorar la legibilidad del XML
        $dom->xmlStandalone = false;  // standalone="no"

        // Crear nodo raíz DebitNote
        $invoiceNode = $dom->createElement('DebitNote');

        // --------------------------------------------------------------------------------------
        // Atributos del elemento <DebitNote>
        // --------------------------------------------------------------------------------------
        $invoiceNode->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2');
        $invoiceNode->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $invoiceNode->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        $invoiceNode->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $invoiceNode->setAttribute('xmlns:ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
        $invoiceNode->setAttribute('xmlns:qdt', 'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2');
        $invoiceNode->setAttribute('xmlns:sac', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2');
        $invoiceNode->setAttribute('xmlns:sbc', 'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2');
        $invoiceNode->setAttribute('xmlns:sts', 'dian:gov:co:facturaelectronica:Structures-2-1');
        $invoiceNode->setAttribute('xmlns:udt', 'urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2');
        $invoiceNode->setAttribute('xmlns:xades', 'http://uri.etsi.org/01903/v1.3.2#');
        $invoiceNode->setAttribute('xmlns:xades141', 'http://uri.etsi.org/01903/v1.4.1#');
        $invoiceNode->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $invoiceNode->setAttribute('xsi:schemaLocation', 'urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2     http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-DebitNote-2.1.xsd');

        // --------------------------------------------------------------------------------------
        // Sección: UBLExtensions
        // --------------------------------------------------------------------------------------
        $invoiceNode->appendChild($this->UBLExtensions->toXML($dom));

        // --------------------------------------------------------------------------------------
        // Sección: Datos básicos de la factura (CBC)
        // --------------------------------------------------------------------------------------
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:UBLVersionID', $this->ublVersion));
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:CustomizationID', $this->customizationID));
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ProfileID', $this->profileID));
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ProfileExecutionID', $this->profileExecutionID));
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:ID', $this->ID));

        // --------------------------------------------------------------------------------------
        // Sección: UUID (CUFE)
        // --------------------------------------------------------------------------------------
        $UUIDNode = $dom->createElement('cbc:UUID', $this->UUID);
        $UUIDNode->setAttribute('schemeID', $this->profileExecutionID);
        foreach ($this->UUIDAtributes as $key => $value) {
            $UUIDNode->setAttribute($key, $value);
        }
        $invoiceNode->appendChild($UUIDNode);

        // --------------------------------------------------------------------------------------
        // Fechas, Tipo, Notas
        // --------------------------------------------------------------------------------------
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueDate', $this->issueDate));
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:IssueTime', $this->issueTime));
        //$invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:DebitNoteTypeCode', $this->DebitNoteTypeCode));
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:Note', $this->note));
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:TaxPointDate', $this->TaxPointDate));

        // --------------------------------------------------------------------------------------
        // Divisa de la Factura (DocumentCurrencyCode)
        // --------------------------------------------------------------------------------------
        $documentCurrencyCodeNode = $dom->createElement('cbc:DocumentCurrencyCode', 'COP');
        $invoiceNode->appendChild($documentCurrencyCodeNode);

        // --------------------------------------------------------------------------------------
        // Número de líneas en la factura
        // --------------------------------------------------------------------------------------
        $invoiceNode->appendChild(XMLGenerator::createNode($dom, 'cbc:LineCountNumeric', $this->lineCountNumeric));

        // --------------------------------------------------------------------------------------
        // Referencias y Partes involucradas
        // --------------------------------------------------------------------------------------

        if ($this->DiscrepancyResponse) {
            $invoiceNode->appendChild($this->DiscrepancyResponse->toXML($dom));
        }

        if ($this->BillingReference) {
            $invoiceNode->appendChild($this->BillingReference->toXML($dom));
        }

        $invoiceNode->appendChild($this->accountingSupplierParty->toXML($dom));
        $invoiceNode->appendChild($this->accountingCustomerParty->toXML($dom));
        $invoiceNode->appendChild($this->paymentMeans->toXML($dom));
        //$invoiceNode->appendChild($this->prepaidPayment->toXML($dom));
        $invoiceNode->appendChild($this->taxTotal->toXML($dom));
        $invoiceNode->appendChild($this->requestedMonetaryTotal->toXML($dom));

        // --------------------------------------------------------------------------------------
        // Líneas de la factura
        // --------------------------------------------------------------------------------------
        foreach ($this->invoiceline as $line) {
            $invoiceNode->appendChild($line->toXML($dom));
        }

        // --------------------------------------------------------------------------------------
        // Finalizar y retornar el XML
        // --------------------------------------------------------------------------------------
        $dom->appendChild($invoiceNode);
        return $dom->saveXML();
    }
}
