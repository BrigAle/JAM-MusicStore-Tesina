<?php
session_start();
$old_username = $_SESSION['old_data']['username'] ?? '';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jam Music Store</title>
  <link rel="stylesheet" href="risorse/CSS/style.css" type="text/css" />
  <link rel="icon" href="risorse/IMG/jam.ico" type="image/x-icon" />
</head>

<body>
  <div class="header">
    <div class="logo">
      <a href="homepage.php"><img src="risorse/IMG/JAM_logo (2).png" alt="JAM Music Store" /></a>
    </div>

    <div class="navSearch">
      <form action="risorse/PHP/ricerca_catalogo.php" method="get">
        <div class="searchContainer">
          <input type="text" name="query" placeholder="Cerca brani o categorie..." />
          <button type="submit" name="tipo" value="nome">Per nome prodotto</button>
          <button type="submit" name="tipo" value="categoria">Per categoria</button>
        </div>
      </form>
    </div>

    <div class="navLink">
      <!-- admin -->
      <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'amministratore'): ?>
        <a href="amministrazione.php">admin</a>
      <?php endif; ?>
      <!-- gestore  -->
      <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'):
        echo "<a href=\"gestione.php\">gestore</a>";
      endif; ?>
      <!-- cliente  -->
      <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
        <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo"></a>
      <?php endif; ?>
      <!-- visitatore -->
      <a href="catalogo.php">Catalogo</a>
      <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
      <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
      <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
      <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
    </div>

  </div>


  <!-- contenuto per login -->
  <div class="content">
    <?php
    // Se già loggato, reindirizza
    if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true') {
      header('Location: homepage.php');
      exit();
    }
    ?>

    <div class="login_container">
      <div class="login_form">
        <form action="risorse/PHP/login.php" method="post">
          <h2>Accedi al tuo account</h2>

          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($old_username); ?>">

          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>

          <?php
          if (isset($_SESSION['error_username'])) {
            echo "<p class='msg error'>Utente non registrato</p>";
            unset($_SESSION['error_username']);
          }
          if (isset($_SESSION['error_password'])) {
            echo "<p class='msg error'>Password errata</p>";
            unset($_SESSION['error_password']);
          }
          if (isset($_SESSION['error_users'])) {
            echo "<p class='msg error'>Errore nel recupero degli utenti</p>";
            unset($_SESSION['error_users']);
          }
          if (isset($_SESSION['error_banned'])) {
            echo "<p class='msg error'>Il tuo account è stato bloccato. Contatta l'amministratore.</p>";
            unset($_SESSION['error_banned']);
          }
          if (isset($_SESSION['error_connection'])) {
            echo "<p class='msg error'>Errore di connessione al database. Riprova più tardi.</p>";
            unset($_SESSION['error_connection']);
          }
          if (isset($_SESSION['success_registrazione'])) {
            if ($_SESSION['success_registrazione']) {
              echo "<p class='msg success'>Registrazione avvenuta con successo! Effettua il login.</p>";
            } else {
              echo "<p class='msg error'>Errore durante la registrazione. Riprova.</p>";
            }
            unset($_SESSION['success_registrazione']);
          }

        
          unset($_SESSION['old_data']);
          ?>

          <input type="submit" value="Accedi" />
          <p>Non sei registrato? <a href="register.php" style="color: darkviolet; background-color: #1F1F1F;">Registrati qui</a></p>
        </form>
      </div>
    </div>
  </div>




  <div class="pdp">
    <div class="pdp-center">
      <p>&copy; 2025 JAM Music Store</p>
    </div>
    <div class="pdp-right">
      <a href="FAQs.php">FAQs</a>
    </div>
  </div>
</body>

</html>