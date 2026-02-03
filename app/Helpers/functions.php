<?php

if (!function_exists('mascaraTelefone')) {
    function mascaraTelefone($value)
    {
        $value = preg_replace("/[^0-9]/", "", $value);
        $len = strlen($value);

        if ($len == 11) {
            return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $value);
        } elseif ($len == 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $value);
        }

        return $value;
    }
}
