<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'amministratore') {
    header("Location: ../../login.php");
    exit();
}
$domanda = $_POST['domanda'] ?? '';
$risposta = $_POST['risposta'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$doc = new DOMDocument();
$doc->load('../../XML/FAQs.xml');
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$faqs = $doc->getElementsByTagName('faq');

// cerco ultimo id
$lastId = 0;
foreach ($faqs as $faq) {
    $id = (int)$faq->getAttribute('id');
    if ($id > $lastId)
        $lastId = $id;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($domanda) && !empty($risposta) && !empty($categoria)) {
    $newFaq = $doc->createElement('faq');
    $newFaq->setAttribute('id', $lastId + 1);

    $elementoDomanda = $doc->createElement('domanda', htmlspecialchars($domanda));
    $elementoRisposta = $doc->createElement('risposta', htmlspecialchars($risposta));
    $elementoCategoria = $doc->createElement('categoria', htmlspecialchars($categoria));

    $newFaq->appendChild($elementoDomanda);
    $newFaq->appendChild($elementoRisposta);
    $newFaq->appendChild($elementoCategoria);

    $salvato = $doc->save('../../XML/FAQs.xml');

    if ($salvato !== false) {
        $_SESSION['successo_msg'] = "FAQ aggiunta con successo.";
    } else {
        $_SESSION['errore_msg'] = "Errore durante l'aggiunta della FAQ.";
    }


    header('Location: ../../../gestione_contenuti_admin.php');
    exit();
}
