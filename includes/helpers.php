<?php
// helpers.php

/**
 * Calcula a porcentagem de um valor em relação a um total.
 */
function calculatePercentage($value, $total)
{
    if ($total == 0) {
        return 0; // Evitar divisão por zero
    }
    return round(($value / $total) * 100);
}

/**
 * Formata a data no formato brasileiro.
 */
function formatDate($dateString)
{
    if (empty($dateString)) {
        return '';
    }
    $timestamp = strtotime($dateString);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Formata o status da tarefa.
 */
function formatStatus($status)
{
    $labels = [
        'pendente' => 'Pendente',
        'em_andamento' => 'Em Andamento',
        'concluido' => 'Concluído'
    ];
    return $labels[$status] ?? $status;
}

/**
 * Formata a prioridade da tarefa.
 */
function formatPriority($priority)
{
    $labels = [
        'baixa' => 'Baixa',
        'media' => 'Média',
        'alta' => 'Alta'
    ];
    return $labels[$priority] ?? $priority;
}
