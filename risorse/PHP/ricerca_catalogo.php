<?php
session_start();

// Controlla parametri
if (isset($_GET['query'], $_GET['tipo'])) {
    $query = trim($_GET['query']);
    $tipo = trim($_GET['tipo']);

    // Sanificazione
    $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
    $tipo = ($tipo === 'categoria') ? 'categoria' : 'nome';

    // Salva in sessione per il catalogo
    $_SESSION['search_query'] = $query;
    $_SESSION['search_tipo'] = $tipo;

    // Reindirizza al catalogo principale
    header("Location: ../../catalogo.php");
    exit;
} else {
    header("Location: ../../homepage.php");
    exit;
}
?>
