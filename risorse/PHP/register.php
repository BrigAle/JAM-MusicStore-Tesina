<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once 'connection.php';
    $connection = new mysqli($host, $user, $password, $db);

    if ($connection->connect_error) {
        die("Connessione fallita: " . $connection->connect_error);
    }

    // Dati dal form (con escape)
    $username  = $connection->real_escape_string($_POST['username']);
    $email     = $connection->real_escape_string($_POST['email']);
    $password  = $connection->real_escape_string($_POST['password']);
    $password_confirm = $connection->real_escape_string($_POST['conferma_password']);
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);

    // Controllo username già esistente
    $check_user = "SELECT id FROM utente WHERE username ='$username'";
    $result_user = $connection->query($check_user);
    if ($result_user->num_rows > 0) {
        $_SESSION['error_user'] = true;
        header('location:../../register.php');
        exit();
    }

    // Controllo email già esistente
    $check_email = "SELECT id FROM utente WHERE email ='$email'";
    $result_email = $connection->query($check_email);
    if ($result_email->num_rows > 0) {
        $_SESSION['error_email'] = true;
        header('location:../../register.php');
        exit();
    }

    // Controllo password uguali
    if ($password !== $password_confirm) {
        $_SESSION['error_password'] = true;
        header('location:../../register.php');
        exit();
    }

    // Inserisco nel DB
    $queryR = "INSERT INTO utente (username,email,password) VALUES ('$username','$email','$hashPassword')";
    $result = $connection->query($queryR);

    $successPHP = $result ? true : false;

    // --- Inserimento nel file XML ---
    $nomeVal     = $connection->real_escape_string($_POST['nome']);
    $cognomeVal  = $connection->real_escape_string($_POST['cognome']);
    $telefonoVal = $connection->real_escape_string($_POST['telefono']);
    $indirizzoVal= $connection->real_escape_string($_POST['indirizzo']);

    $xmlFile = "../XML/utenti.xml";
    if (!file_exists($xmlFile)) {
        die("Errore: Il file XML non esiste.");
    }

    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    $doc->load($xmlFile);

    $utenti = $doc->documentElement; // root <utenti>
    $utente = $doc->createElement('utente');

    // Creo i nodi con i valori
    $utente->appendChild($doc->createElement('nome', $nomeVal));
    $utente->appendChild($doc->createElement('cognome', $cognomeVal));
    $utente->appendChild($doc->createElement('telefono', $telefonoVal));
    $utente->appendChild($doc->createElement('indirizzo', $indirizzoVal));
    $utente->appendChild($doc->createElement('reputazione', 0));
    $utente->appendChild($doc->createElement('stato', 1));
    $utente->appendChild($doc->createElement('portafoglio', 0.0));
    $utente->appendChild($doc->createElement('crediti', 0.0));
    $utente->appendChild($doc->createElement('data_iscrizione', date("Y-m-d")));

    // Genero nuovo id
    $numeroUtenti = $doc->getElementsByTagName('utente');
    $idUtente = 0;
    if ($numeroUtenti->length > 0) {
        $ultimoUtente = $numeroUtenti->item($numeroUtenti->length - 1);
        if ($ultimoUtente->hasAttribute('id')) {
            $idUtente = (int)$ultimoUtente->getAttribute('id');
        }
    }
    $utente->setAttribute('id', $idUtente + 1);

    // Aggiungo l’utente
    $utenti->appendChild($utente);

    // Salvo nel file XML
    $doc->save($xmlFile);

    // Verifica inserimento XML
    $xmlRicerca = simplexml_load_file($xmlFile);
    $idUtenteAggiunto = $idUtente + 1;
    $successXML = false;
    foreach ($xmlRicerca->utente as $user) {
        if ((int)$user['id'] === $idUtenteAggiunto) {
            $successXML = true;
            break;
        }
    }

    // Sessione finale
    $_SESSION['success'] = ($successPHP && $successXML);

    header('location:../../register.php');
    exit();
}
?>
