<?php

class NominaElectronicaApplicationResponse
{

    private $model;

    public function __construct()
    {

        require_once __DIR__ . '/../../core/XMLGenerator.php';
        require_once __DIR__ . '/../../Entities/AttachedDocument.php';
        require_once __DIR__ . '/../../Entities/CompanyID.php';
        require_once __DIR__ . '/../../Entities/PartyTaxScheme.php';
        require_once __DIR__ . '/../../Entities/SenderParty.php';
        require_once __DIR__ . '/../../Entities/TaxScheme.php';
        require_once __DIR__ . '/../../Entities/ReceiverParty.php';
        require_once __DIR__ . '/../../Entities/ExternalReference.php';
        require_once __DIR__ . '/../../Entities/ApplicationResponse.php';
        require_once __DIR__ . '/../../Entities/DocumentResponse.php';
        require_once __DIR__ . '/../../Entities/Response.php';
        require_once __DIR__ . '/../../Entities/DocumentReference.php';
        require_once __DIR__ . '/../../Entities/LineReference.php';
        require_once __DIR__ . '/../../Entities/LineResponse.php';
        require_once __DIR__ . '/../../Entities/UBLExtensions.php';
        require_once __DIR__ . '/../../Entities/UBLExtension.php';
        require_once __DIR__ . '/../../Entities/DianExtensions.php';
        require_once __DIR__ . '/../../Entities/SoftwareProvider.php';
        require_once __DIR__ . '/../../Entities/AuthorizationProvider.php';
        require_once __DIR__ . '/../../Entities/InvoiceSource.php';
        require_once __DIR__ . '/../../Entities/ExtensionContent.php';
        require_once __DIR__ . '/../../core/Helper.php';
        require_once __DIR__ . '/../../core/Validator.php';
        require_once __DIR__ . '/../../Model/DataModelClass.php';

        $this->model = new DataModel();
    }

    public function generateXML(array $data, array $data_fac)
    {

        $dataProcesarVP = json_decode(json_encode($data['factura']), true);

        $dataEmpresa = $this->model->getDataEmpresa();

        $companyNIT = $dataEmpresa[0]['numero_identificacion'];
        $schemeID = $dataEmpresa[0]['digito_verificacion'];
        $registrationName = $dataEmpresa[0]['razon_social'];

        $taxScheme = new TaxScheme("01", "IVA");

        $companyID = new CompanyID($companyNIT, [
            "schemeID" => $schemeID,
            "schemeName" => "31",
        ]);

        $partyTaxScheme = new PartyTaxScheme($registrationName, $companyID, "", null, $taxScheme, "");

        $receiverParty = new ReceiverParty($partyTaxScheme);

        $taxScheme = new TaxScheme("01", "IVA");

        $companyNIT = Helper::getNitDian();

        $companyID = new CompanyID($companyNIT, [
            "schemeID" => "4",
            "schemeName" => "31",
        ]);

        $partyTaxScheme = new PartyTaxScheme("Unidad Especial Dirección de Impuestos y Aduanas Nacionales", $companyID, "", null, $taxScheme, "");

        $senderParty = new SenderParty($partyTaxScheme);

        $lineResponse = new LineResponse(new LineReference("1"), new Response("0000", "0"));

        $ResponseCode = "02";
        $Description = "Documento validado por la DIAN";
        $ID = $dataProcesarVP['consecutivoDocumento'];
        $UUID = Helper::generateCUFE($dataProcesarVP, $data_fac);

        $documentResponse = new DocumentResponse(
            new Response($ResponseCode, $Description),
            new DocumentReference($ID,$UUID, ["schemeName" => "CUFE-SHA384"]),
            $lineResponse
        );

        //----------------------------------------------------------

        $SoftwareSecurityCode = Helper::getSoftwareCode();
        $ProviderID =  $dataEmpresa[0]['numero_identificacion'];
        $schemeID   = $dataEmpresa[0]['digito_verificacion'];
        $AuthorizationProviderID = Helper::getNitDian();
        $softwareID = Helper::getSoftwareId();

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

        // DianExtensions
        $dianExtensions = new DianExtensions(
            $SoftwareSecurityCode,
            null,
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
        $ublExtensions = new UBLExtensions($ublExtension);


        //--------------------------------------------------------------

        // Crear nodo raíz ApplicationResponse

        $ublVersion = 'UBL 2.1';
        $customizationID = '1';
        $profileID = 'DIAN 2.1';
        $dataAmbiente   = $this->model->getDataAmbiente();
        $profileExecutionID = $dataAmbiente[0]['ambiente'] == 'P' ? '1' : '2'; // 1: Producción, 2: Pruebas
        $consecutivoDocumento = $dataProcesarVP['consecutivoDocumento'];
        $IssueDate = date('Y-m-d');
        $IssueTime = date('H:i:s').'-05:00';
        $ID = Helper::generateNumericID($consecutivoDocumento, $companyNIT, $IssueDate);//Consecutivo propio del generador del evento

        $applicationResponseNode = new ApplicationResponse($ublExtensions, $ublVersion,$customizationID,$profileID,$profileExecutionID, $ID, $UUID, ["schemeName" => "CUDE-SHA384"], $IssueDate,$IssueTime, $senderParty, $receiverParty, $documentResponse);

        // Generar XML
        $xmlContent = $applicationResponseNode->toXML();
        return $xmlContent;
    }
}
