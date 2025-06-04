<?php
/**
 * Retorna o IP do cliente com base em $_SERVER['REMOTE_ADDR'].
 *
 * @return string IPv4 ou IPv6 válido, ou string vazia se inválido.
 */
function getClientIp(): string
{
    // Captura o endereço diretamente de REMOTE_ADDR
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    // Valida se é um IPv6 ou IPv4 válido
    if (
        filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ||
        filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
    ) {
        return $ip;
    }

    // Se não for válido, retorna string vazia
    return '';
}
?>