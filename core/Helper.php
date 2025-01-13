<?php

class Helper
{

    public static function getNombreFiguraTributaria($codigo)
    {

        try {

            $figuras = [
                '01' => 'IVA',                             // Impuesto sobre la Ventas
                '02' => 'IC',                              // Impuesto al Consumo Departamental Nominal
                '03' => 'ICA',                             // Impuesto de Industria, Comercio y Aviso
                '04' => 'INC',                             // Impuesto Nacional al Consumo
                '05' => 'ReteIVA',                         // Retención sobre el IVA
                '06' => 'ReteRenta',                       // Retención sobre Renta
                '07' => 'ReteICA',                         // Retención sobre el ICA
                '08' => 'IC Porcentual',                   // Impuesto al Consumo Departamental Porcentual
                '20' => 'FtoHorticultura',                 // Cuota de Fomento Hortifrutícola
                '21' => 'Timbre',                          // Impuesto de Timbre
                '22' => 'INC Bolsas',                      // Impuesto Nacional al Consumo de Bolsa Plástica
                '23' => 'INCarbono',                       // Impuesto Nacional del Carbono
                '24' => 'INCombustibles',                  // Impuesto Nacional a los Combustibles
                '25' => 'Sobretasa Combustibles',          // Sobretasa a los combustibles
                '26' => 'Sordicom',                        // Contribución minoristas (Combustibles)
                '27' => 'IC Datos',                        // Impuesto al Consumo de Datos
                '31' => 'ICL',                             // Impuesto al Consumo de Licores
                '32' => 'INPP',                            // Impuesto nacional productos plásticos
                '34' => 'IBUA',                            // Impuesto a las bebidas ultraprocesadas azucaradas
                '35' => 'ICUI',                            // Impuesto a los productos comestibles ultraprocesados
                '36' => 'ADV',                             // AD VALOREM
                'ZZ' => 'Otros tributos, tasas, contribuciones y similares',
            ];

            if (isset($figuras[$codigo])) {
                return $figuras[$codigo];
            } else {
                throw new Exception("Código de figura tributaria no encontrado: $codigo");
            }
        } catch (Exception $e) {
            exit('4-Excepción capturada: ' . $e->getMessage());
        }
    }

    public static function getRawXMLInvoince($params, $data_fac)
    {

        require_once __DIR__ . '/../xml/Factura/InvoiceTransformer.php';

        $transformer = new InvoiceTransformer($params, $data_fac);
        $xml = $transformer->generateXML();

        return $xml;
    }

    public static function getRawXMLCreditNote($params, $data_fac,$notaCreditoReferenciada)
    {

        
        require_once __DIR__ . '/../xml/NotaCredito/CreditNoteTransformer.php';
        
        $transformer = new CreditNoteTransformer($params, $data_fac,$notaCreditoReferenciada);
        $xml = $transformer->generateXML();
        
        return $xml;
    }

    public static function getRawXMLDebitNote($params, $data_fac,$notaDebitoReferenciada)
    {

        require_once __DIR__ . '/../xml/NotaDebito/DebitNoteTransformer.php';

        $transformer = new DebitNoteTransformer($params, $data_fac,$notaDebitoReferenciada);
        $xml = $transformer->generateXML();

        return $xml;
    }

    public static function getValorImpuesto($impuestosTotales, $codigoTOTALImp)
    {

        $valorImpuesto = 0.00;

        foreach ($impuestosTotales as $impuesto) {
            if ($impuesto['codigoTOTALImp'] == $codigoTOTALImp) {
                $valorImpuesto = $impuesto['montoTotal'];
                break;
            }
        }

        return $valorImpuesto;
    }

    public static function generateNumericID($consecutivoDocumento, $nitEmpresa, $fecha)
    {
        $rawID = $consecutivoDocumento . $nitEmpresa . $fecha;

        $numericID = preg_replace('/\D/', '', crc32($rawID));

        return substr($numericID, 0, 12);
    }

    public static function getRawXMLApplicationResponse($params, $data_fac)
    {

        require_once __DIR__ . '/../xml/Factura/InvoiceApplicationResponse.php';

        $builder = new InvoiceApplicationResponse();
        $xml = $builder->generateXML($params, $data_fac);

        return $xml;
    }

    public static function getRawXMLCreditNoteApplicationResponse($params, $data_fac)
    {

        require_once __DIR__ . '/../xml/NotaCredito/CreditNoteApplicationResponse.php';

        $builder = new CreditNoteApplicationResponse();
        $xml = $builder->generateXML($params, $data_fac);

        return $xml;
    }

    public static function getRawXMLDebitNoteApplicationResponse($params, $data_fac)
    {

        require_once __DIR__ . '/../xml/NotaDebito/DebitNoteApplicationResponse.php';

        $builder = new DebitNoteApplicationResponse();
        $xml = $builder->generateXML($params, $data_fac);

        return $xml;
    }

    public static function getCTEc()
    {

        require_once 'MethodSignature.php';
        
        $firmador = new MethodSignature();

        $accountCode  = Helper::getNitEmpresa();
        $accountCodeT = Helper::getNitEmpresa();
        $softwareCode = Helper::getSoftwareCode();

        $action = 'http://wcf.dian.colombia/WcfDianCustomerServices/GetNumberingRange';
        $xml = '<soap:Body><wcf:GetNumberingRange><wcf:accountCode>' . $accountCode . '</wcf:accountCode><wcf:accountCodeT>' . $accountCodeT . '</wcf:accountCodeT><wcf:softwareCode>' . $softwareCode . '</wcf:softwareCode></wcf:GetNumberingRange></soap:Body>';

        $respuesta = $firmador->enviarXML($xml,$action);

        exit('Respuesta del metodo GetNumberingRange : ' . $respuesta);
       
    }


    public static function generateCUFE($dataProcesarVP, $data_fac)
    {

        require_once __DIR__ . '/../Model/DataModelClass.php';

        $model = new DataModel();

        $errors = [];

        $dataEmpresa    = $model->getDataEmpresa();
        $dataAmbiente   = $model->getDataAmbiente();

        $cliente = $dataProcesarVP['cliente']['informacionLegalCliente'];
        $numeroIdentificacion = $cliente['numeroIdentificacion'];

        $NumFac = trim($data_fac[0]['consecutivo_factura']);
        $FecFac = trim($data_fac[0]['fecha']); // Formato esperado YYYY-MM-DD
        $HorFac = date('H:i:s', strtotime(trim($data_fac[0]['con_fecha_factura'])));

        $ValFac = trim($data_fac[0]['totalSinImpuestos']);

        $ValImp1 = Helper::getValorImpuesto($dataProcesarVP['impuestosTotales'], '01');
        $ValImp2 = Helper::getValorImpuesto($dataProcesarVP['impuestosTotales'], '04');
        $ValImp3 = Helper::getValorImpuesto($dataProcesarVP['impuestosTotales'], '03');

        $ValTot = trim($data_fac[0]['valor']);
        $NitFE = trim($dataEmpresa[0]['numero_identificacion']);
        $NumAdq = trim($numeroIdentificacion);
        $TipoAmbiente = $dataAmbiente[0]['ambiente'] == 'P' ? '1' : '2'; // 1: Producción, 2: Pruebas
        //$CTEc = Helper::getCTEc();
        $CTEc = 12345678;

        if (empty($NumFac)) {
            $errors[] = "El número de factura (NumFac) es obligatorio y debe contener prefijo y consecutivo.";
        }

        // Validación FecFac (YYYY-MM-DD)
        $d = DateTime::createFromFormat('Y-m-d', $FecFac);
        if (!$d || $d->format('Y-m-d') !== $FecFac) {
            $errors[] = "La fecha de la factura (FecFac) es obligatoria y debe tener el formato YYYY-MM-DD.";
        }

        // Validación HorFac (HH:MM:SS)
        if (!preg_match('/^(2[0-3]|[01]\d):([0-5]\d):([0-5]\d)$/', $HorFac)) {
            $errors[] = "La hora de la factura (HorFac) es obligatoria, debe tener el formato HH:MM:SS.";
        }

        // Función de validación para valores monetarios
        $validateMoney = function ($value, $fieldName) use (&$errors) {
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
                $errors[] = "El valor del campo {$fieldName} debe ser numérico, con dos decimales, sin separadores de miles (ej: 1234.56), valor actual: {$value}";
            }
        };

        // Validar ValFac, ValImp1, ValImp2, ValImp3, ValTot
        $validateMoney($ValFac, 'ValFac');
        $validateMoney($ValImp1, 'ValImp1');
        $validateMoney($ValImp2, 'ValImp2');
        $validateMoney($ValImp3, 'ValImp3');
        $validateMoney($ValTot, 'ValTot');

        $CodImp1 = '01';
        $CodImp2 = '04';
        $CodImp3 = '03';

        // Validar NitFE (solo números, sin guiones)
        if (!ctype_digit($NitFE)) {
            $errors[] = "El NIT del Facturador Electrónico (NitFE) debe ser numérico, sin guiones y sin dígito de verificación, valor actual: {$NitFE}";
        }

        // Validar NumAdq (solo números)
        if (!ctype_digit($NumAdq)) {
            $errors[] = "El número de identificación del adquiriente (NumAdq) debe ser numérico, sin guiones y sin dígito de verificación, valor actual: {$NumAdq}";
        }

        // Validar CTEc no vacío
        if (empty($CTEc)) {
            $errors[] = "La Clave Técnica (CTEc) es obligatoria.";
        }

        // Validar TipoAmbiente (ejemplo: 1=producción, 2=habilitación)
        // Ajustar esta validación según las especificaciones exactas de la DIAN.
        $validAmbientes = ['1', '2', '3'];
        if (!in_array($TipoAmbiente, $validAmbientes)) {
            $errors[] = "El Tipo de Ambiente (TipoAmbiente) es inválido. Debe corresponder a un ambiente autorizado por la DIAN (1,2 o 3), valor actual: {$TipoAmbiente}";
        }

        if (!empty($errors)) {
            throw new Exception("Errores de validación en CUFE<br><br>: " . implode('<br>', $errors));
        }

        $cufeBase = $NumFac . $FecFac . $HorFac . $ValFac . $CodImp1 . $ValImp1 . $CodImp2 . $ValImp2 . $CodImp3 . $ValImp3 . $ValTot . $NitFE . $NumAdq . $CTEc . $TipoAmbiente;

        $cufe = hash('sha384', $cufeBase);

        return $cufe;
    }

    public static function getNitDian()
    {
        return '800197268';
    }

    public static function getNitEmpresa()
    {
        require_once __DIR__ . '/../Model/DataModelClass.php';

        $model = new DataModel();

        $dataEmpresa = $model->getDataEmpresa();

        return $dataEmpresa[0]['numero_identificacion'];
    }

    public static function getSoftwareCode()
    {

        $softwareCode = '6ce93671-9f2b-4c6d-8da7-1f5765601e46';
       /*  $softwareCode = 'd239d0aa-cf56-43f4-a038-ad616e16dfdd';
        $softwareCode = 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c';
        $softwareCode = 'a83780ce-effd-4552-8fc3-3aa9cba710ee'; */

        return $softwareCode;
    }

    public static function getSoftwareId()
    {

        require_once __DIR__ . '/../Model/DataModelClass.php';

        $model = new DataModel();

        $dataResolucion = $model->getDataResolucion();

        $softwareId = trim($dataResolucion[0]['identificador_software']);

        if (empty($softwareId)) {
            throw new Exception("El identificador de software es obligatorio.");
        }

        return $softwareId;
    }
}
