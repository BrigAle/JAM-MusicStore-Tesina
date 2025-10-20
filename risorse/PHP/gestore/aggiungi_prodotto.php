<?php
session_start();

if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Salvo i dati in sessione per riproporli in caso di errore
    $_SESSION['old_data'] = [
        'nome' => $_POST['nome'] ?? '',
        'categoria' => $_POST['categoria'] ?? '',
        'descrizione' => $_POST['descrizione'] ?? '',
        'prezzo' => $_POST['prezzo'] ?? '',
        'bonus' => $_POST['bonus'] ?? ''
    ];

    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $prezzo = trim($_POST['prezzo'] ?? '');
    $bonus = trim($_POST['bonus'] ?? '');
    $immagine = $_FILES['immagine'] ?? null;

    // 🔍 Controlli base
    if (empty($nome) || empty($categoria) || empty($descrizione) || empty($prezzo) || !$immagine || $immagine['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['aggiungi_prodotto_successo'] = false;
        header("Location: ../../../aggiungi_prodotto.php");
        exit();
    }

    $xmlFile = '../../../risorse/XML/prodotti.xml';
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    if (!$doc->load($xmlFile)) {
        $_SESSION['aggiungi_prodotto_successo'] = false;
        header("Location: ../../../aggiungi_prodotto.php");
        exit();
    }

    foreach
    ($doc->getElementsByTagName('prodotto') as $p) {
            $nomeProdotto = $p->getElementsByTagName('nome')->item(0)->nodeValue;
            if (strcasecmp($nomeProdotto, $nome) == 0) {
                $_SESSION['errore_msg'] = "Esiste già un prodotto con questo nome.";
                header("Location: ../../../aggiungi_prodotto.php");
                exit();
            }
        }
    }

    // Calcolo ID
    $lastId = 0;
    foreach ($doc->getElementsByTagName('prodotto') as $p) {
        $id = (int)$p->getAttribute('id');
        if ($id > $lastId) $lastId = $id;
    }
    $nuovoId = $lastId + 1;

    // Salvo l'immagine
    $targetDir = "../../../risorse/IMG/prodotti/";
    $nomeImmagine = basename($immagine['name']);
    $targetFile = $targetDir . $nomeImmagine;
    if (!move_uploaded_file($immagine["tmp_name"], $targetFile)) {
        $_SESSION['aggiungi_prodotto_successo'] = false;
        header("Location: ../../../aggiungi_prodotto.php");
        exit();
    }

    // Aggiungo il prodotto
    $newProdotto = $doc->createElement('prodotto');
    $newProdotto->setAttribute('id', $nuovoId);

    $newProdotto->appendChild($doc->createElement('nome', htmlspecialchars($nome)));
    $newProdotto->appendChild($doc->createElement('categoria', htmlspecialchars($categoria)));
    $newProdotto->appendChild($doc->createElement('descrizione', htmlspecialchars($descrizione)));
    $newProdotto->appendChild($doc->createElement('prezzo', htmlspecialchars($prezzo)));
    $newProdotto->appendChild($doc->createElement('bonus', htmlspecialchars($bonus ?: '0.00')));
    $newProdotto->appendChild($doc->createElement('data_inserimento', date('Y-m-d')));
    $newProdotto->appendChild($doc->createElement('immagine', htmlspecialchars($nomeImmagine)));

    $doc->documentElement->appendChild($newProdotto);
    $doc->save($xmlFile);

    unset($_SESSION['old_data']); // pulisco i dati dopo successo
    $_SESSION['aggiungi_prodotto_successo'] = true;
    header("Location: ../../../gestione_prodotti_gestore.php");
    exit();

?>
