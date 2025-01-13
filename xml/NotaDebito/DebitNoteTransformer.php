<?php
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('default_socket_timeout', 120); */

class DebitNoteTransformer
{

    private $dataProcesarVP;
    private $model;
    private $data_fac;
    private $cliente_id;
    private $referenciada;

    // Carga de todas las dependencias a nivel de clase
    // Esto evita el require repetido dentro de métodos
    public function __construct(array $data, array $data_fac, $referenciada)
    {

        require_once __DIR__ . '/../../core/XMLGenerator.php';
        require_once __DIR__ . '/../../core/Helper.php';
        require_once __DIR__ . '/../../core/Validator.php';
        require_once __DIR__ . '/../../Entities/Address.php';
        require_once __DIR__ . '/../../Entities/Party.php';
        require_once __DIR__ . '/../../Entities/DebitNote.php';
        require_once __DIR__ . '/../../Entities/UBLExtensions.php';
        require_once __DIR__ . '/../../Entities/UblExtension.php';
        require_once __DIR__ . '/../../Entities/ExtensionContent.php';
        require_once __DIR__ . '/../../Entities/DianExtensions.php';
        require_once __DIR__ . '/../../Entities/InvoiceSource.php';
        require_once __DIR__ . '/../../Entities/SoftwareProvider.php';
        require_once __DIR__ . '/../../Entities/AuthorizationProvider.php';
        require_once __DIR__ . '/../../Entities/AuthorizedInvoices.php';
        require_once __DIR__ . '/../../Entities/AuthorizationPeriod.php';
        require_once __DIR__ . '/../../Entities/InvoicePeriod.php';
        require_once __DIR__ . '/../../Entities/DiscrepancyResponse.php';
        require_once __DIR__ . '/../../Entities/BillingReference.php';
        require_once __DIR__ . '/../../Entities/AccountingSupplierParty.php';
        require_once __DIR__ . '/../../Entities/PartyLegalEntity.php';
        require_once __DIR__ . '/../../Entities/CorporateRegistrationScheme.php';
        require_once __DIR__ . '/../../Entities/PartyTaxScheme.php';
        require_once __DIR__ . '/../../Entities/TaxScheme.php';
        require_once __DIR__ . '/../../Entities/Country.php';
        require_once __DIR__ . '/../../Entities/AddressLine.php';
        require_once __DIR__ . '/../../Entities/PhysicalLocation.php';
        require_once __DIR__ . '/../../Entities/PartyName.php';
        require_once __DIR__ . '/../../Entities/Contact.php';
        require_once __DIR__ . '/../../Entities/CompanyID.php';
        require_once __DIR__ . '/../../Entities/AccountingCustomerParty.php';
        require_once __DIR__ . '/../../Entities/Delivery.php';
        require_once __DIR__ . '/../../Entities/PaymentMeans.php';
        require_once __DIR__ . '/../../Entities/PrepaidPayment.php';
        require_once __DIR__ . '/../../Entities/TaxTotal.php';
        require_once __DIR__ . '/../../Entities/TaxSubtotal.php';
        require_once __DIR__ . '/../../Entities/TaxCategory.php';
        require_once __DIR__ . '/../../Entities/RequestedMonetaryTotal.php';
        require_once __DIR__ . '/../../Entities/DebitNoteLine.php';
        require_once __DIR__ . '/../../Entities/Price.php';
        require_once __DIR__ . '/../../Entities/StandardItemIdentification.php';
        require_once __DIR__ . '/../../Entities/AdditionalItemProperty.php';
        require_once __DIR__ . '/../../Entities/Item.php';
        require_once __DIR__ . '/../../Model/DataModelClass.php';

        $this->dataProcesarVP = json_decode(json_encode($data['factura']), true);
        $this->data_fac = $data_fac;
        $this->cliente_id = $data_fac[0]['cliente_id'];
        $this->model = new DataModel();
        $this->referenciada = $referenciada;

        $printDataProcesarVP = false;
        $printDataFactura = false;

        if ($printDataProcesarVP) {
            echo "<pre>";
            print_r($this->dataProcesarVP);
            echo "</pre>";
            exit;
        }

        if ($printDataFactura) {
            echo "<pre>";
            print_r($this->data_fac);
            echo "</pre>";
            exit;
        }
    }

    /**
     * Genera el XML de la factura.
     */
    public function generateXML()
    {
        try {

            $dataAmbiente   = $this->model->getDataAmbiente();

            // Crear las extensiones UBL
            $ublExtensions = $this->createUblExtensions(); //OK

            // Crear la referencia de orden

            $ID = "";
            $issueDate = "";

            if (isset($this->dataProcesarVP['ordenDeCompra'])) {
                if (count($this->dataProcesarVP['ordenDeCompra']) > 0) {
                    $ID = $this->dataProcesarVP['ordenDeCompra'][0]['numeroOrden'];
                    $issueDate = date('Y-m-d', strtotime(trim($this->dataProcesarVP['ordenDeCompra'][0]['fecha'])));
                }
            }

            $ReferenceID = 'GMT4588';
            $ResponseCode = "2";
            $DiscrepancyResponse = null;
            $BillingReference    = null;

            //Valida si la nota credito es una nota credito referenciada
            if ($this->referenciada) {
                $DiscrepancyResponse = new DiscrepancyResponse($ReferenceID, $ResponseCode);
                $ID = 'GMT4588';
                $UUID = "2";
                $IssueDate = "2024-10-09";
                $BillingReference = new BillingReference($ID, $UUID, $IssueDate);
            }



            // Crear el AccountingSupplierParty
            $accountingSupplierParty = $this->createAccountingSupplierParty(); //Pendiente

            // Crear el AccountingCustomerParty
            $accountingCustomerParty = $this->createAccountingCustomerParty(); //Pendiente 1 etiqueta (98%)

            // Crear PaymentMeans

            /**
             * 1: Contado
             * 2: Crédito
             */

            $ID = $this->dataProcesarVP['mediosDePago'][0]['metodoDePago'];
            $paymentMeansCode = $this->dataProcesarVP['mediosDePago'][0]['medioPago'];
            $paymentDueDate = $this->dataProcesarVP['mediosDePago'][0]['fechaDeVencimiento'];
            $PaymentID = '1';
            $attributesID = [
                "schemeID" => "",
                "schemeName" => "",
            ];

            $paymentMeans = new PaymentMeans($ID, $paymentMeansCode, $paymentDueDate,$PaymentID,$attributesID);

            // Crear PrepaidPayment (anticipo - opcional)
            $prepaidPayment = $this->createPrepaidPayment();

            // Crear TaxTotal (ejemplo de Totales)
            $taxTotal = $this->createTaxTotal(); //Pendiente

            // Crear RequestedMonetaryTotal
            $requestedMonetaryTotal = $this->createRequestedMonetaryTotal(); //Pendiente

            // Crear las líneas de la factura
            $invoiceLines = $this->createInvoiceLines(); //Pendiente

            // Atributos del UUID
            $UUIDAtributes = [
                "schemeName" => "CUDE-SHA384",
            ];

            //print_r($this->dataProcesarVP);

            $UBLVersionID = "UBL 2.1";
            $CustomizationID = "12";
            $ProfileID = "DIAN 2.1: Factura Electrónica de Venta";
            $ProfileExecutionID = $dataAmbiente[0]['ambiente'] == 'P' ? '1' : '2'; // 1: Producción, 2: Pruebas

            Validator::validateExecutionID($ProfileExecutionID);

            $ID = $this->dataProcesarVP['consecutivoDocumento'];
            $UUID = Helper::generateCUFE($this->dataProcesarVP, $this->data_fac);
            $IssueDate = date('Y-m-d');
            $IssueTime = date('H:i:s') . '-05:00';
            //$DebitNoteTypeCode = "91";
            $Note = "";
            $TaxPointDate = '2024-01-01';
            $LineCountNumeric = count($this->dataProcesarVP['detalleDeFactura']);

            // Crear la factura
            $invoice = new DebitNote(
                $UBLVersionID,
                $CustomizationID,
                $ProfileID,
                $ublExtensions,
                $ProfileExecutionID,
                $ID,
                $UUID,
                $UUIDAtributes,
                $IssueDate,
                $IssueTime,
                $Note,
                $TaxPointDate,
                $LineCountNumeric,
                $DiscrepancyResponse,
                $BillingReference,
                $accountingSupplierParty,
                $accountingCustomerParty,
                $paymentMeans,
                $prepaidPayment,
                $taxTotal,
                $requestedMonetaryTotal,
                $invoiceLines
            );

            // Generar XML
            $xmlContent = $invoice->toXML();

            return $xmlContent;

        } catch (Exception $e) {
            exit('3-Excepción capturada: ' . $e->getMessage());
        }
    }

    private function getQRCode($ProviderID)
    {

        $dataAmbiente   = $this->model->getDataAmbiente();

        $ulrPruebas = "https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=";
        $ulrProduccion = "https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=";

        $url = $dataAmbiente[0]['ambiente'] == 'P' ? $ulrProduccion : $ulrPruebas;

        $cufe = Helper::generateCUFE($this->dataProcesarVP, $this->data_fac);

        $NumFac = $this->dataProcesarVP['consecutivoDocumento'];
        $FecFac = date('Y-m-d', strtotime(trim($this->dataProcesarVP['fechaEmision'])));
        $HorFac = date('H:i:s', strtotime(trim($this->dataProcesarVP['fechaEmision']))) . '-05:00';
        $NitFac = $ProviderID;
        $DocAdq = $this->dataProcesarVP['cliente']['informacionLegalCliente']['numeroIdentificacion'];
        $ValFac =  number_format($this->dataProcesarVP['totalSinImpuestos'] ?? 0, 2, '.', '');

        $ValIva = 0;

        foreach ($this->dataProcesarVP['impuestosGenerales'] as $impuesto) {
            if ($impuesto['codigoTOTALImp'] === '01') { // Código de IVA
                $ValIva = number_format($impuesto['valorTOTALImp'], 2, '.', '');
                break;
            }
        }

        $ValOtroIm = 0;

        foreach ($this->dataProcesarVP['impuestosGenerales'] as $impuesto) {
            if ($impuesto['codigoTOTALImp'] !== '01') { // Otros impuestos
                $ValOtroIm += $impuesto['valorTOTALImp'];
            }
        }

        $ValOtroIm = number_format($ValOtroIm, 2, '.', '');

        $ValTolFac = number_format($this->dataProcesarVP['totalMonto'] ?? 0, 2, '.', '');

        return "NumFac: " . $NumFac . " FecFac: " . $FecFac . " HorFac: " . $HorFac . " NitFac: " . $NitFac . " DocAdq: " . $DocAdq . " ValFac: " . $ValFac . " ValIva: " . $ValIva . " ValOtroIm: " . $ValOtroIm . " ValTolFac: " . $ValTolFac . " CUFE: " . $cufe . " " . $url . $cufe;
    }


    /**
     * Crea las extensiones UBL.
     */
    private function createUblExtensions()
    {

        try {

            $dataResolucion = $this->model->getDataResolucion();
            $dataEmpresa    = $this->model->getDataEmpresa();

            if (count($dataResolucion) == 0) {
                throw new Exception("No se encontraron datos de resolución activa con tipo de emision DIAN, por favor verifique la configuración (Parámetros Resolución Facturación)");
            }

            if (count($dataEmpresa) == 0) {
                throw new Exception("No se encontraron datos de la empresa, por favor verifique la configuración (Empresa)");
            }

            $resolucion_dian = trim($dataResolucion[0]['resolucion_dian']);
            $StartDate = trim($dataResolucion[0]['fecha_resolucion_dian']);
            $EndDate = trim($dataResolucion[0]['fecha_vencimiento_resolucion_dian']);
            $Prefix = trim($dataResolucion[0]['prefijo']);
            $From = trim($dataResolucion[0]['rango_inicial']);
            $To = trim($dataResolucion[0]['rango_final']);
            $softwareID = Helper::getSoftwareId();
            $pin = trim($dataResolucion[0]['pin']);
            $consecutivoDocumento = trim($this->data_fac[0]['consecutivo_factura']);
            $SoftwareSecurityCode  = hash('sha384', $softwareID . $pin . $consecutivoDocumento);

            $ProviderID = trim($dataEmpresa[0]['numero_identificacion']);
            $schemeID   = trim($dataEmpresa[0]['digito_verificacion']);

            $AuthorizationProviderID = Helper::getNitDian();

            // Validar los datos
            Validator::validateDataUbl($resolucion_dian, $StartDate, $EndDate, $Prefix, $From, $To, $ProviderID, $schemeID, $softwareID, $pin);

            // AuthorizationProvider
            $AuthorizationProvider = new AuthorizationProvider($AuthorizationProviderID);

            // Atributos para el SoftwareProvider
            $providerAttributes = [
                "schemeID" => $schemeID,
                "schemeName" => "31",
                "schemeAgencyID" => "195",
                "schemeAgencyName" => "CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)"
            ];

            $softwareAttributes = [
                "schemeAgencyID" => "195",
                "schemeAgencyName" => "CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)"
            ];

            $SoftwareProvider = new SoftwareProvider(
                $ProviderID,
                $softwareID,
                $providerAttributes,
                $softwareAttributes
            );

            // InvoiceSource
            $InvoiceSource = new InvoiceSource("CO");

            $QRCode = $this->getQRCode($ProviderID);

            // DianExtensions
            $dianExtensions = new DianExtensions(
                $SoftwareSecurityCode,
                $QRCode,
                null,
                $InvoiceSource,
                $SoftwareProvider,
                $AuthorizationProvider
            );

            // ExtensionContent
            $extensionContent = new ExtensionContent($dianExtensions);

            // UBLExtension
            $ublExtension = new UBLExtension($extensionContent);

            // UBLExtensions
            return new UBLExtensions($ublExtension);
        } catch (Exception $e) {
            exit('2-Excepción capturada: ' . $e->getMessage());
        }
    }

    /**
     * Crea el AccountingSupplierParty.
     */
    private function createAccountingSupplierParty()
    {

        $dataEmpresa  = $this->model->getDataEmpresa();
        $electronicMail = $dataEmpresa[0]['email'];

        $contact = new Contact(null, $electronicMail);

        $companyNIT = $dataEmpresa[0]['numero_identificacion'];
        $registrationName = $dataEmpresa[0]['razon_social'];
        $schemeID = $dataEmpresa[0]['digito_verificacion'];

        Validator::validateRequired($companyNIT, 'NIT de la Empresa');
        Validator::validateNIT($companyNIT, 'NIT de la Empresa');
        Validator::validateRequired($registrationName, 'Razón Social de la Empresa');
        Validator::validateRequired($schemeID, 'Digito de Verificación de la Empresa');

        $companyID = new CompanyID($companyNIT, [
            "schemeID" => $schemeID,
            "schemeName" => "31",
            "schemeAgencyID" => "195",
            "schemeAgencyName" => "CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)",
        ]);

        $ID = "NCE";
        $Name = "TRANSPORTES LOGISTICOS DE CARGA GMT S A S";

        $corporateRegistrationScheme = new CorporateRegistrationScheme($ID, $Name);

        $partyLegalEntity = new PartyLegalEntity($registrationName, $companyID, true, $corporateRegistrationScheme);

        $ID = "ZZ";
        $name = Helper::getNombreFiguraTributaria($ID);

        $taxScheme = new TaxScheme($ID, $name);

        $country = new Country("CO", "Colombia", "es");

        $line = $dataEmpresa[0]['direccion'];

        Validator::validateRequired($line, 'Dirección de la Empresa');

        $addressLine = new AddressLine($line);

        $ID = $dataEmpresa[0]['divipola'];
        $CityName = $dataEmpresa[0]['city_name'];
        $PostalZone = $dataEmpresa[0]['zona_postal'];
        $CountrySubentity = $dataEmpresa[0]['departamento'];
        $countrySubentityCode = $dataEmpresa[0]['divipola_dpto'];

        $registrationAddress = new Address($ID, $CityName, $PostalZone, $CountrySubentity, $countrySubentityCode, $addressLine, $country);

        $registrationName = $dataEmpresa[0]['razon_social'];
        $taxLevelCode = 'R-99-PN'; //No aplica - Otros

        $partyTaxScheme = new PartyTaxScheme($registrationName, $companyID, $taxLevelCode, $registrationAddress, $taxScheme, ["listName" => ""]);

        $ID = $dataEmpresa[0]['divipola'];
        $CityName = $dataEmpresa[0]['city_name'];
        $PostalZone = $dataEmpresa[0]['zona_postal'];
        $CountrySubentity = $dataEmpresa[0]['departamento'];
        $countrySubentityCode = $dataEmpresa[0]['divipola_dpto'];

        $addressPhysicalLocation = new Address("11001", "Bogotá, D.c. ", "110521", "Bogotá", "11", $addressLine, $country);
        $physicalLocation = new PhysicalLocation($addressPhysicalLocation);

        $name  = $dataEmpresa[0]['razon_social'];

        $partyName = new PartyName($name);

        $industryClassificationCode = $dataEmpresa[0]['codigo_ciiu'];

        Validator::validateRequired($industryClassificationCode, 'Código CIIU de la Empresa');

        $party = new Party($industryClassificationCode, $partyName, $physicalLocation, $partyTaxScheme, $partyLegalEntity, $contact);

        $additionalAccountID = '1';

        $attributesAdditionalAccount = [
            "schemeAgencyID" => "195",
            "schemeID" => "31",
            "schemeName" => "31"
        ];

        return new AccountingSupplierParty($additionalAccountID, $party,$attributesAdditionalAccount);
    }

    /**
     * Crea el AccountingCustomerParty.
     */
    private function createAccountingCustomerParty()
    {

        $dataCliente  = $this->model->getDataCliente($this->cliente_id);
        $dataEmpresa  = $this->model->getDataEmpresa();

        /**
         * Grupo de detalles con información de contacto del emisor
         */
        $electronicMail = $dataCliente[0]['email'];

        $contact = new Contact(null, $electronicMail);

        /**
         * Si el adquiriente es persona jurídica: AdditionalAccountID contiene “1” Si el adquiriente es persona natural: AdditionalAccountID contiene “2”
         *
         * @var string $additionalAccountID
         */
        $additionalAccountID = $dataCliente[0]['tipo_persona_id'];

        /**
         * Grupo de información legales del emisor
         */
        $companyNIT = $dataCliente[0]['numero_identificacion'];
        $registrationName = $dataCliente[0]['razon_social'];
        $schemeID = $dataCliente[0]['digito_verificacion'];

        Validator::validateRequired($companyNIT, 'NIT del cliente');
        Validator::validateNIT($companyNIT, 'NIT del cliente');
        Validator::validateRequired($registrationName, 'Razón Social del cliente');
        Validator::validateRequired($schemeID, 'Digito de Verificación del cliente');

        $companyID = new CompanyID($companyNIT, [
            "schemeID" => $schemeID,
            "schemeName" => "31",
            "schemeAgencyID" => "195",
            "schemeAgencyName" => "CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)",
        ]);

        $partyLegalEntity = new PartyLegalEntity($registrationName, $companyID, false);
        $country = new Country("CO", "Colombia", "es");
        $addressLine = new AddressLine("Cra 6 # 15 - 32");


        /**
         * Grupo para informar dirección fiscal del emisor 
         */

        $ID = $dataEmpresa[0]['divipola'];
        $CityName = $dataEmpresa[0]['city_name'];
        $PostalZone = $dataEmpresa[0]['zona_postal'];
        $CountrySubentity = $dataEmpresa[0]['departamento'];
        $countrySubentityCode = $dataEmpresa[0]['divipola_dpto'];
        $District = $dataEmpresa[0]['direccion'];

        $registrationAddress = new Address($ID, $CityName, $PostalZone, $CountrySubentity, $countrySubentityCode, $addressLine, $country, $District);

        /**
         * Grupo de detalles tributarios del emisor 
         */
        $ID = "ZZ";
        $name = Helper::getNombreFiguraTributaria($ID);

        $taxScheme = new TaxScheme($ID, $name);

        /**
         * Grupo de información tributarias del emisor
         */

        $registrationName = $dataCliente[0]['razon_social'];
        $taxLevelCode = $dataCliente[0]['obligaciones'];

        Validator::validateRequired($taxLevelCode, '(obligaciones del cliente - informacion tributaria)');

        $partyTaxScheme = new PartyTaxScheme($registrationName, $companyID, $taxLevelCode, $registrationAddress, $taxScheme, ["listName" => ""]);

        Validator::validateRequired($dataCliente[0]['divipola'], 'divipola de cliente');

        $ID = $dataCliente[0]['divipola'];

        Validator::validateRequired($ID, 'divipola de cliente');
        Validator::validateNumeric($ID, 'divipola de cliente', 5);

        $CityName = $dataCliente[0]['city_name'];
        $PostalZone = $dataCliente[0]['zona_postal'];

        Validator::validateRequired($CityName, 'city_name de cliente');
        Validator::validateRequired($PostalZone, 'zona_postal de cliente');

        $CountrySubentity = $dataCliente[0]['departamento'];

        Validator::validateRequired($CountrySubentity, 'departamento de cliente');

        $countrySubentityCode = $dataCliente[0]['divipola_dpto'];
        $AddressLine = $dataCliente[0]['direccion'];

        $addressPhysicalLocation = new Address($ID, $CityName, $PostalZone, $CountrySubentity, $countrySubentityCode, new AddressLine($AddressLine), $country);
        $physicalLocation = new PhysicalLocation($addressPhysicalLocation);

        $name  = $dataCliente[0]['razon_social'];

        $partyName = new PartyName($name);

        $industryClassificationCode = $dataCliente[0]['codigo_ciiu'];

        Validator::validateRequired($industryClassificationCode, 'Código CIIU de cliente');

        $party = new Party($industryClassificationCode,null, null, $partyTaxScheme, $partyLegalEntity, $contact);

        return new AccountingCustomerParty($additionalAccountID, $party,$includeIndustryClassificationCode = true);
    }

    /**
     * Crea la información de PrepaidPayment. (anticipo)
     */
    private function createPrepaidPayment()
    {
        $paidAmountAttributes = ["currencyID" => "COP"];

        $ID = "";
        $paidAmount = "0";

        return new PrepaidPayment($ID, $paidAmount, $paidAmountAttributes);
    }

    /**
     * Crea la información de TaxTotal.
     */
    private function createTaxTotal()
    {
        $taxableAmountAttributes = ["currencyID" => "COP"];
        $taxAmountAttributes = ["currencyID" => "COP"];

        $ID =  $this->dataProcesarVP['impuestosGenerales'][0]['codigoTOTALImp'];

        $name = Helper::getNombreFiguraTributaria($ID);

        $taxScheme = new TaxScheme($ID, $name);

        $percent = $this->dataProcesarVP['impuestosGenerales'][0]['porcentajeTOTALImp'];

        Validator::validateRequired($percent, 'Porcentaje de impuesto');

        $taxCategory = new TaxCategory($percent, $taxScheme);

        $taxableAmount = $this->dataProcesarVP['totalSinImpuestos'];
        $TaxAmount = $this->dataProcesarVP['totalBrutoConImpuesto'];
        $PerUnitAmount = "0";

        Validator::validateRequired($taxableAmount, 'Monto base para el cálculo del impuesto');
        Validator::validateRequired($TaxAmount, 'Monto total del impuesto');

        $taxSubtotal = new TaxSubtotal($taxableAmount, $taxableAmountAttributes, $TaxAmount, $taxAmountAttributes, $taxCategory, $PerUnitAmount);

        $TaxAmount = $this->dataProcesarVP['totalMonto'];
        $RoundingAmount = "0";

        Validator::validateRequired($TaxAmount, 'Monto total del impuesto');

        return new TaxTotal($TaxAmount, $taxAmountAttributes, $taxSubtotal,$RoundingAmount);
    }

    /**
     * Crea la información de RequestedMonetaryTotal.
     */
    private function createRequestedMonetaryTotal() //
    {
        $attributes = ["currencyID" => "COP"];

        $lineExtensionAmount = $this->dataProcesarVP['totalSinImpuestos'];
        $taxExclusiveAmount = $this->dataProcesarVP['totalBaseImponible'];
        $taxInclusiveAmount = $this->dataProcesarVP['totalBrutoConImpuesto'];
        $prepaidAmount = null;
        $payableAmount = $this->dataProcesarVP['totalMonto'];
        $PayableRoundingAmount = "0";

        return new RequestedMonetaryTotal(
            $lineExtensionAmount,
            $attributes,
            $taxExclusiveAmount,
            $attributes,
            $taxInclusiveAmount,
            $attributes,
            $prepaidAmount,
            $attributes,
            $payableAmount,
            $attributes,
            $PayableRoundingAmount
        );
    }

    /**
     * Crea las líneas de la factura (InvoiceLine).
     */
    private function createInvoiceLines()
    {
        $lines = [];

        $detalles = $this->dataProcesarVP['detalleDeFactura'];

        Validator::validateRequired($detalles, 'Detalles de la factura');

        foreach ($detalles as $detalle) {

            $ID = $detalle['secuencia'];
            $IDScheme = "1";
            $invoicedQuantity = $detalle['cantidadReal'];
            $lineExtensionAmount = $detalle['precioTotalSinImpuestos'];
            $unitCode = $detalle['unidadMedida'];
            $priceAmount = $detalle['precioVentaUnitario'];
            $baseQuantity = $detalle['cantidadReal'];
            $Item = $detalle['descripcion'];
            $withAdditionalProperties = true;
            $withTax = false;
            $codigoTOTALImp = null;
            $porcentajeTOTALImp = null;
            $estandarCodigoProducto = $detalle['estandarCodigoProducto'];
            $estandarCodigo = $detalle['estandarCodigo'];

            if (!empty($detalle['impuestosDetalles'])) {
                foreach ($detalle['impuestosDetalles'] as $impuesto) {
                    // Validar si el impuesto tiene un valor distinto de cero
                    if ($impuesto['valorTOTALImp'] > 0) {
                        $withTax = true;

                        $codigoTOTALImp = $impuesto['codigoTOTALImp'];
                        $porcentajeTOTALImp = $impuesto['porcentajeTOTALImp'];
                        $baseImponibleTOTALImp = $impuesto['baseImponibleTOTALImp'];
                        $valorTOTALImp = $impuesto['valorTOTALImp'];
                    }
                }
            }


            $line = $this->createInvoiceLine(
                $ID,
                $IDScheme,
                $invoicedQuantity,
                $lineExtensionAmount,
                $unitCode,
                $priceAmount,
                $baseQuantity,
                $Item,
                $withAdditionalProperties,
                $withTax,
                $codigoTOTALImp,
                $porcentajeTOTALImp,
                $baseImponibleTOTALImp,
                $valorTOTALImp,
                $estandarCodigoProducto,
                $estandarCodigo
            );

            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * Método auxiliar para crear una línea de factura (InvoiceLine).
     *
     * @param string $ID Valor del ID de la línea.
     * @param string $IDScheme Valor del schemeID del ID.
     * @param string $invoicedQuantity Cantidad facturada.
     * @param string $lineExtensionAmount Monto de la línea.
     * @param string $unitCode Unidad de medida.
     * @param string $priceAmount Precio unitario.
     * @param string $baseQuantity Cantidad base.
     * @param string $Item Descripción del item.
     * @param bool $withAdditionalProperties Indica si se agregan propiedades adicionales.
     * @param bool $withTax Indica si la línea tiene impuestos (true) o no (false).
     * @param string $codigoTOTALImp Código del impuesto.
     * @param string $porcentajeTOTALImp Porcentaje del impuesto.
     * @param string $baseImponibleTOTALImp Base imponible del impuesto.
     * @param string $valorTOTALImp Valor total del impuesto.
     * @param string $estandarCodigoProducto Código del producto.
     * @param string $estandarCodigo Código estandar.
     * 
     */
    private function createInvoiceLine(
        $ID,
        $IDScheme,
        $invoicedQuantity,
        $lineExtensionAmount,
        $unitCode,
        $priceAmount,
        $baseQuantity,
        $Item,
        $withAdditionalProperties = false,
        $withTax = true,
        $codigoTOTALImp = null,
        $porcentajeTOTALImp = null,
        $baseImponibleTOTALImp = null,
        $valorTOTALImp = null,
        $estandarCodigoProducto = null,
        $estandarCodigo = null
    ) {
        $priceAmountAttributes = ["currencyID" => "COP"];
        $baseQuantityAttributes = ["unitCode" => $unitCode];
        $price = new Price($priceAmount, $priceAmountAttributes, $baseQuantity, $baseQuantityAttributes);

        $ID = $estandarCodigoProducto;
        $schemeID = $estandarCodigo;

        $standardItemIdentification = new StandardItemIdentification($ID, ["schemeID" => $schemeID,"schemeName"=> "Estándar de adopción del contribuyente"]);

        // Propiedades adicionales solo si se requiere
        if ($withAdditionalProperties) {
            $valueQuantityAttributes = ["unitCode" => $unitCode];
            $additionalItemProperty3 = new AdditionalItemProperty("03", "250000", "10", $valueQuantityAttributes, true);
            $additionalItemProperty2 = new AdditionalItemProperty("02", "REM0001", "", [], false);
            $additionalItemProperty1 = new AdditionalItemProperty("01", "13456", "", [], false);
        } else {
            $noData = new AdditionalItemProperty("", "", "", [], false);
            $additionalItemProperty1 = $noData;
            $additionalItemProperty2 = $noData;
            $additionalItemProperty3 = $noData;
        }

        $withSellersItemIdentification = true;

        $item = new Item($Item, $standardItemIdentification, $additionalItemProperty1, $additionalItemProperty2, $additionalItemProperty3, $withAdditionalProperties,$withSellersItemIdentification);

        $lineExtensionAmountAttributes = ["currencyID" => "COP"];
        $invoicedQuantityAttributes = ["unitCode" => $unitCode];
        $IDAttributes = [];

        $taxTotal = null;
        if ($withTax) {

            $ID = $codigoTOTALImp;
            $name = Helper::getNombreFiguraTributaria($ID);

            $taxScheme = new TaxScheme($ID, $name);
            $taxCategory = new TaxCategory($porcentajeTOTALImp, $taxScheme);

            $taxAmountAttributes = ["currencyID" => "COP"];
            $taxableAmountAttributes = ["currencyID" => "COP"];
            $RoundingAmount = "0";

            $taxSubtotal = new TaxSubtotal($baseImponibleTOTALImp, $taxableAmountAttributes, $valorTOTALImp, $taxAmountAttributes, $taxCategory);
            $taxTotal = new TaxTotal($valorTOTALImp, $taxAmountAttributes, $taxSubtotal,$RoundingAmount);
        }

        return new DebitNoteLine(
            $ID,
            $IDAttributes,
            $invoicedQuantity,
            $invoicedQuantityAttributes,
            $lineExtensionAmount,
            $lineExtensionAmountAttributes,
            $taxTotal,
            $item,
            $price
        );
    }
}
