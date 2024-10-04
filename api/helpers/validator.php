<?php
class Validator
{
    private static $filename = null;
    private static $search_value = null;
    private static $password_error = null;
    private static $file_error = null;
    private static $search_error = null;

    // Método para obtener el error al validar una contraseña.
    public static function getPasswordError()
    {
        return self::$password_error;
    }

    // Método para obtener el nombre del archivo validado.
    public static function getFilename()
    {
        return self::$filename;
    }

    // Método para obtener el error al validar un archivo.
    public static function getFileError()
    {
        return self::$file_error;
    }

    // Método para obtener el valor de búsqueda.
    public static function getSearchValue()
    {
        return self::$search_value;
    }

    // Método para obtener el error al validar una búsqueda.
    public static function getSearchError()
    {
        return self::$search_error;
    }

    // Sanear campos de un formulario eliminando espacios en blanco al principio y al final.
    public static function validateForm($fields)
    {
        foreach ($fields as $index => $value) {
            $value = trim($value);
            $fields[$index] = $value;
        }
        return $fields;
    }

    // Validar si un número es natural (entero mayor o igual a uno).
    public static function validateNaturalNumber($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
            return true;
        } else {
            return false;
        }
    }

    // Validar si un número es natural dentro del rango de descuento (1-100).
    public static function validateNaturalNumberDiscount($value)
    {
        $options = array(
            'options' => array(
                'min_range' => 1,
                'max_range' => 100
            )
        );

        if (filter_var($value, FILTER_VALIDATE_INT, $options)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar si un valor es un número flotante mayor o igual a uno.
    public static function validateFloat($value)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) && $value >= 1.0) {
            return true;
        } else {
            return false;
        }
    }

    // Validar un archivo de imagen y establecer un nombre único para el archivo.
    public static function validateImageFile($file, $dimension)
    {
        if (is_uploaded_file($file['tmp_name'])) {
            $image = getimagesize($file['tmp_name']);
            if ($file['size'] > 2097152) {
                self::$file_error = 'El tamaño de la imagen debe ser menor a 2MB';
                return false;
            } elseif ($image[0] < $dimension) {
                self::$file_error = 'La dimensión de la imagen es menor a ' . $dimension . 'px';
                return false;
            } elseif ($image[0] != $image[1]) {
                self::$file_error = 'La imagen no es cuadrada';
                return false;
            } elseif ($image['mime'] == 'image/jpeg' || $image['mime'] == 'image/png') {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                self::$filename = uniqid() . '.' . $extension;
                return true;
            } else {
                self::$file_error = 'El tipo de imagen debe ser jpg o png';
                return false;
            }
        } else {
            return false;
        }
    }

    // Validar un correo electrónico.
    public static function validateEmail($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar un valor booleano.
    public static function validateBoolean($value)
    {
        if ($value == 1 || $value == 0) {
            return true;
        } else {
            return false;
        }
    }

    // Validar una cadena de texto permitiendo letras, dígitos, espacios y signos de puntuación.
    public static function validateString($value)
    {
        if (preg_match('/^[a-zA-Z0-9ñÑáÁéÉíÍóÓúÚ\s\,\;\.]+$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar una cadena de texto permitiendo solo letras y espacios.
    public static function validateAlphabetic($value)
    {
        if (preg_match('/^[a-zA-ZñÑáÁéÉíÍóÓúÚ\s]+$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar una cadena de texto permitiendo letras, dígitos y espacios.
    public static function validateAlphanumeric($value)
    {
        if (preg_match('/^[a-zA-Z0-9ñÑáÁéÉíÍóÓúÚ\s]+$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar la longitud de una cadena de texto.
    public static function validateLength($value, $min, $max)
    {
        if (strlen($value) >= $min && strlen($value) <= $max) {
            return true;
        } else {
            return false;
        }
    }

    // Validar un valor monetario permitiendo hasta dos cifras decimales.
    public static function validateMoney($value)
    {
        if (preg_match('/^[0-9]+(?:\.[0-9]{1,2})?$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar una contraseña y establecer mensajes de error si es necesario.
    public static function validatePassword($value)
    {
        if (strlen($value) < 8) {
            self::$password_error = 'La contraseña es menor a 8 caracteres';
            return false;
        } elseif (strlen($value) <= 72) {
            return true;
        } else {
            self::$password_error = 'La contraseña es mayor a 72 caracteres';
            return false;
        }
    }

    // Validar el formato de un DUI (Documento Único de Identidad).
    public static function validateDUI($value)
    {
        if (preg_match('/^[0-9]{8}[-][0-9]{1}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar un número telefónico con formato específico.
    public static function validatePhone($value)
    {
        if (preg_match('/^[2,6,7]{1}[0-9]{3}[-][0-9]{4}$/', $value)) {
            return true;
        } else {
            return false;
        }
    }

    // Validar una fecha.
    public static function validateDate($value)
    {
        $date = explode('-', $value);
        if (checkdate($date[1], $date[2], $date[0])) {
            return true;
        } else {
            return false;
        }
    }

    // Validar un valor de búsqueda y establecer mensajes de error si es necesario.
    public static function validateSearch($value)
    {
        if (trim($value) == '') {
            self::$search_error = 'Ingrese un valor para buscar';
            return false;
        } elseif (str_word_count($value) > 3) {
            self::$search_error = 'La búsqueda contiene más de 3 palabras';
            return false;
        } elseif (self::validateString($value)) {
            self::$search_value = $value;
            return true;
        } else {
            self::$search_error = 'La búsqueda contiene caracteres prohibidos';
            return false;
        }
    }

    // Guardar un archivo subido en el servidor.
    public static function saveFile($file, $path)
    {
        if (!$file) {
            return false;
        } elseif (move_uploaded_file($file['tmp_name'], $path . self::$filename)) {
            return true;
        } else {
            return false;
        }
    }

    // Cambiar un archivo en el servidor y eliminar el archivo anterior.
    public static function changeFile($file, $path, $old_filename)
    {
        if (!self::saveFile($file, $path)) {
            return false;
        } elseif (self::deleteFile($path, $old_filename)) {
            return true;
        } else {
            return false;
        }
    }

    // Eliminar un archivo del servidor.
    public static function deleteFile($path, $filename)
    {
        if ($filename == 'default.png') {
            return true;
        } elseif (@unlink($path . $filename)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function generateRandomString($length = 24) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
