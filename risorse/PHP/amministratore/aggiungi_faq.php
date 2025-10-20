<?php
session_start();

// Solo admin
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'amministratore') {
    header("Location: ../../login.php");
    exit();
}

// Salva i dati compilati (per riproporli se c'è errore)
$_SESSION['old_data'] = [
    'domanda' => $_POST['domanda'] ?? '',
    'risposta' => $_POST['risposta'] ?? '',
    'categoria' => $_POST['categoria'] ?? ''
];

$domanda = $_POST['domanda'] ?? '';
$risposta = $_POST['risposta'] ?? '';
$categoria = $_POST['categoria'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($domanda) || empty($risposta) || empty($categoria)) {
        $_SESSION['errore_msg'] = "Tutti i campi sono obbligatori.";
        header('Location: ../../../aggiungi_faq.php');
        exit();
    }

    $xmlPath = '../../XML/FAQs.xml';
    if (!file_exists($xmlPath)) {
        $_SESSION['errore_msg'] = "Il file XML non esiste.";
        header('Location: ../../../aggiungi_faq.php');
        exit();
    }

    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    $doc->load($xmlPath);

    $faqs = $doc->getElementsByTagName('faq');

    // 🔍 Controllo duplicati
    foreach ($faqs as $faq) {
        $domanda_esistente = strtolower(trim($faq->getElementsByTagName('domanda')[0]->nodeValue ?? ''));
        if ($domanda_esistente === strtolower($domanda)) {
            $_SESSION['errore_msg'] = "Esiste già una FAQ con la stessa domanda.";
            header('Location: ../../../aggiungi_faq.php');
            exit();
        }
    }

    // Calcolo nuovo ID
    $lastId = 0;
    foreach ($faqs as $faq) {
        $id = (int)$faq->getAttribute('id');
        if ($id > $lastId) {
            $lastId = $id;
        }
    }

    // Creazione nuovo elemento <faq>
    $newFaq = $doc->createElement('faq');
    $newFaq->setAttribute('id', $lastId + 1);

    $elementoDomanda = $doc->createElement('domanda', htmlspecialchars($domanda));
    $elementoRisposta = $doc->createElement('risposta', htmlspecialchars($risposta));
    $elementoCategoria = $doc->createElement('categoria', htmlspecialchars($categoria));

    $newFaq->appendChild($elementoDomanda);
    $newFaq->appendChild($elementoRisposta);
    $newFaq->appendChild($elementoCategoria);

    $doc->documentElement->appendChild($newFaq);

    $salvato = $doc->save($xmlPath);

    if ($salvato !== false) {
        unset($_SESSION['old_data']);
        $_SESSION['successo_msg'] = "FAQ aggiunta con successo.";
    } else {
        $_SESSION['errore_msg'] = "Errore durante il salvataggio della FAQ.";
    }

    header('Location: ../../../gestione_contenuti_admin.php');
    exit();
}
?>
