<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('default_socket_timeout', 120);*/

class NominaElectronicaAttachedDocument
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
        require_once __DIR__ . '/../../Entities/ParentDocumentLineReference.php';
        require_once __DIR__ . '/../../Model/DataModelClass.php';
        require_once __DIR__ . '/../../core/Helper.php';
        require_once __DIR__ . '/../../core/Validator.php';

        $this->model = new DataModel();
        
    }

    public function generateXML(array $data, array $data_fac)
    {

        try {

            $dataProcesarVP = json_decode(json_encode($data['factura']), true);
            $cliente_id = $data_fac[0]['cliente_id'];
            $dataEmpresa = $this->model->getDataEmpresa();
            $dataCliente  = $this->model->getDataCliente($cliente_id);
            $dataAmbiente   = $this->model->getDataAmbiente();

            $notaCreditoReferenciada= true;
            $xmlNominaElectronica   = Helper::getRawXMLCreditNote($data, $data_fac,$notaCreditoReferenciada);
            $xmlApplicationResponse = Helper::getRawXMLCreditNoteApplicationResponse($data, $data_fac);

            $externalReferenceInvoice             = new ExternalReference('text/xml', 'UTF-8', "$xmlNominaElectronica");
            $externalReferenceApplicationResponse = new ExternalReference('text/xml', 'UTF-8', "$xmlApplicationResponse");

            $lineID = 1;
            $ID = "1";
            $UUID = Helper::generateCUFE($dataProcesarVP, $data_fac);
            $IssueDate = date('Y-m-d');
            $ValidationResultCode = "02";
            $ValidationDate = date('Y-m-d');
            $ValidationTime = date('H:i:s') . '-05:00';

            $ParentDocumentLineReference = new ParentDocumentLineReference($lineID, $ID, $UUID, $IssueDate, $ValidationResultCode, $ValidationDate, $ValidationTime, $externalReferenceApplicationResponse);

            $taxScheme = new TaxScheme("01", "IVA");

            $companyNIT = $dataCliente[0]['numero_identificacion'];
            $schemeID = $dataCliente[0]['digito_verificacion'];

            Validator::validateRequired($companyNIT, 'Número de Identificación de la Empresa');

            $companyID = new CompanyID($companyNIT, [
                "schemeID" => $schemeID,
                "schemeName" => "31",
                "schemeAgencyID" => "195"
            ]);

            $registrationName = $dataCliente[0]['razon_social'];
            $taxLevelCode = 'R-99-PN'; //No aplica - Otros

            $partyTaxScheme = new PartyTaxScheme($registrationName, $companyID, $taxLevelCode, null, $taxScheme, ["listName" => "No aplica"]);

            $receiverParty = new ReceiverParty($partyTaxScheme);


            $ID = "ZZ";
            $name = Helper::getNombreFiguraTributaria($ID);

            $taxScheme = new TaxScheme($ID, $name);

            $companyNIT = $dataEmpresa[0]['numero_identificacion'];
            $schemeID = $dataEmpresa[0]['digito_verificacion'];
            $registrationName = $dataEmpresa[0]['razon_social'];

            $companyID = new CompanyID($companyNIT, [
                "schemeID" => $schemeID,
                "schemeName" => "31",
                "schemeAgencyID" => "195"
            ]);

            $partyTaxScheme = new PartyTaxScheme($registrationName, $companyID, "R-99-PN", null, $taxScheme, ["listName" => "No aplica"]);

            $senderParty = new SenderParty($partyTaxScheme);

            $consecutivoDocumento = $dataProcesarVP['consecutivoDocumento'];
            $ublVersion = "UBL 2.";
            $customizationID = "Documentos adjuntos";
            $profileID = "Factura Electrónica de Venta";
            $ProfileExecutionID = $dataAmbiente[0]['ambiente'] == 'P' ? '1' : '2'; // 1: Producción, 2: Pruebas

            Validator::validateExecutionID($ProfileExecutionID);


            $IssueDate = date('Y-m-d');
            $IssueTime = date('H:i:s');
            $ID = Helper::generateNumericID($consecutivoDocumento, $companyNIT, $IssueDate);
            $note = "Contenedor Attached Document $consecutivoDocumento";
            $documentType = "Contenedor de Factura Electrónica";
            $parentDocumentID = $consecutivoDocumento;


            $attachedDocumentNode = new AttachedDocument($ublVersion, $customizationID, $profileID, $ProfileExecutionID, $ID, $IssueDate, $IssueTime, $note, $documentType, $parentDocumentID, $senderParty, $receiverParty, $externalReferenceInvoice, $ParentDocumentLineReference);

            $xmlAttachedDocument = $attachedDocumentNode->toXML();

            $printXmlApplicationResponse = false;
            $printXmlNominaElectronica = true;
            $printXmlAttachedDocument = false;

            if ($printXmlAttachedDocument) {

                $fileName = 'NominaElectronicaAttachedDocumentGenerate.xml';
                $filePath = __DIR__ . "/xmlGenerados/$fileName";
                file_put_contents($filePath, $xmlAttachedDocument);

                header('Content-Type: application/xml');
                exit($xmlAttachedDocument);
            }

            if ($printXmlNominaElectronica) {
                $fileName = 'NominaElectronicaGenerate.xml';
                $filePath = __DIR__ . "/xmlGenerados/$fileName";
                file_put_contents($filePath, $xmlNominaElectronica);

                header('Content-Type: application/xml');
                exit($xmlNominaElectronica);
            }

            if ($printXmlApplicationResponse) {

                $fileName = 'NominaElectronicaAplicationResponseGenerate.xml';
                $filePath = __DIR__ . "/xmlGenerados/$fileName";
                file_put_contents($filePath, $xmlApplicationResponse);

                header('Content-Type: application/xml');
                exit($xmlApplicationResponse);
            }

        } catch (Exception $e) {
            $nameFile = 'InvoiceContainerBuilder';
            exit('Excepción capturada en ' . $nameFile . ': ' . $e->getMessage());
        }
    }
}
