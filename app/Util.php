<?php

namespace App;

class Util
{
    public static function formatarDataHora($data)
    {
        return date('d/m/Y H:i:s', strtotime($data));
    }

    public static function formatarReais($valor)
{
    // Formata o valor como moeda brasileira (R$)
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
}
