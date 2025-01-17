<?php

/**
 * Função para depurar variáveis com formatação legível
 *
 * @param mixed $var Variável a ser depurada
 * @param bool $exit Define se o script deve ser encerrado após a depuração (padrão: false)
 * @return void
 */
function Debug($var, $exit = false) {
    echo "<pre style='background-color: #f4f4f4; color: #333; padding: 10px; border: 1px solid #ccc; border-radius: 5px;'>";
    print_r($var);
    echo "</pre>";
    
    if ($exit) {
        exit;
    }
}

/**
 * Função para capturar e exibir detalhes de erros e exceções
 *
 * @param Exception|Throwable $e Objeto de exceção ou erro
 * @param bool $returnHtml Define se o resultado deve ser retornado como string HTML (padrão: false)
 * @return void|string
 */
function Error($e, $returnHtml = false) {
    $output = "<div style='background-color: #ffe0e0; color: #900; padding: 15px; border: 1px solid #f00; border-radius: 5px;'>";
    $output .= "<strong>Erro ou Exceção Capturado:</strong> " . $e->getMessage() . "<br>";
    $output .= "<strong>Arquivo:</strong> " . $e->getFile() . "<br>";
    $output .= "<strong>Linha:</strong> " . $e->getLine() . "<br>";
    $output .= "<strong>Pilha de Execução:</strong><pre>" . $e->getTraceAsString() . "</pre>";
    $output .= "<strong>Detalhes da Pilha:</strong><br>";
    foreach ($e->getTrace() as $trace) {
        $output .= "Função: " . $trace['function'] . "<br>";
        $output .= "Arquivo: " . (isset($trace['file']) ? $trace['file'] : 'N/A') . "<br>";
        $output .= "Linha: " . (isset($trace['line']) ? $trace['line'] : 'N/A') . "<br><br>";
    }
    $output .= "</div>";
    if ($returnHtml) {
        return $output;
    }
    echo $output;
}
