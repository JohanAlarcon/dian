<?php

class Validator
{

    public static function validateLength($value, $fieldName, $maxLength)
    {
        if (strlen($value) > $maxLength) {
            throw new Exception("El campo {$fieldName} no puede exceder los {$maxLength} caracteres.");
        }
    }

    public static function validateRegex($value, $fieldName, $pattern)
    {
        if (!preg_match($pattern, $value)) {
            throw new Exception("El campo {$fieldName} no cumple con el formato requerido.");
        }
    }

    public static function validateExecutionID($value)
    {
        if (!in_array($value, ['1', '2'])) {
            throw new Exception("El campo ProfileExecutionID debe ser '1' (Producción) o '2' (Pruebas).");
        }
    }

    // Validación de campos obligatorios
    public static function validateRequired($value, $fieldName)
    {
        if (empty($value)) {
            throw new Exception("El campo {$fieldName} es obligatorio.");
        }
    }

    // Validación de formato de fecha
    public static function validateDate($date, $fieldName, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        if (!$d || $d->format($format) !== $date) {
            throw new Exception("El campo {$fieldName} debe tener un formato de fecha válido ({$format}).");
        }
    }

    // Validación de formato de hora
    public static function validateTime($time, $fieldName)
    {
        if (!preg_match('/^(2[0-3]|[01]\d):([0-5]\d):([0-5]\d)$/', $time)) {
            throw new Exception("El campo {$fieldName} debe tener un formato de hora válido (HH:MM:SS).");
        }
    }

    // Validación de números (con longitud opcional)
    public static function validateNumeric($value, $fieldName, $maxLength = null)
    {
        if (!is_numeric($value)) {
            throw new Exception("El campo {$fieldName} debe ser numérico.");
        }
        if ($maxLength && strlen($value) > $maxLength) {
            throw new Exception("El campo {$fieldName} no puede exceder los {$maxLength} caracteres.");
        }
    }

    // Validación de formato de moneda (con dos decimales)
    public static function validateMoney($value, $fieldName)
    {
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
            throw new Exception("El campo {$fieldName} debe ser un valor monetario válido (ej: 1234.56).");
        }
    }

    // Validación de NIT
    public static function validateNIT($value, $fieldName)
    {
        if (!ctype_digit($value)) {
            throw new Exception("El campo {$fieldName} debe ser numérico y no debe contener caracteres especiales.");
        }
    }

    // Validación de CUFE (longitud exacta y caracteres permitidos)
    public static function validateCUFE($value, $fieldName)
    {
        if (strlen($value) !== 96) {
            throw new Exception("El campo {$fieldName} debe tener exactamente 96 caracteres.");
        }
        if (!ctype_xdigit($value)) {
            throw new Exception("El campo {$fieldName} debe contener solo caracteres hexadecimales.");
        }
    }

    // Validación de un campo dentro de un rango de valores
    public static function validateInArray($value, $fieldName, $validValues)
    {
        if (!in_array($value, $validValues)) {
            throw new Exception("El campo {$fieldName} debe contener un valor válido: " . implode(', ', $validValues));
        }
    }

    public static function validateDataUbl($resolucion_dian, $StartDate, $EndDate, $Prefix, $From, $To, $ProviderID, $schemeID, $softwareID, $pin)
    {

        if (empty($resolucion_dian)) {
            throw new Exception("El campo 'resolucion_dian' es obligatorio (resolucion_dian)");
        }
        if (empty($StartDate)) {
            throw new Exception("El campo 'fecha_resolucion_dian' es obligatorio (StartDate)");
        }
        if (empty($EndDate)) {
            throw new Exception("El campo 'fecha_fin' es obligatorio (EndDate)");
        }
        if (empty($Prefix)) {
            throw new Exception("El campo 'prefijo' es obligatorio (Prefix)");
        }
        if (empty($From)) {
            throw new Exception("El campo 'rango_inicial' es obligatorio (From)");
        }
        if (empty($To)) {
            throw new Exception("El campo 'rango_final' es obligatorio (To)");
        }
        if (empty($ProviderID)) {
            throw new Exception("El campo 'numero_identificacion' es obligatorio (ProviderID)");
        }
        if (empty($schemeID)) {
            throw new Exception("El campo 'digito_verificacion' es obligatorio (schemeID)");
        }
        if (empty($softwareID)) {
            throw new Exception("El campo 'identificador_software' es obligatorio (softwareID)");
        }

        if (empty($pin)) {
            throw new Exception("El campo 'pin' es obligatorio (pin)");
        }
    }
}
