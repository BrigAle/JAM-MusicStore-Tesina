<?php
session_start();
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
      <!-- admin links -->
      <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'amministratore'): ?>
        <a href="amministrazione.php">admin</a>
      <?php endif; ?>
      <!-- gestore links -->
      <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'):
        echo "<a href=\"gestione.php\">gestore</a>";
      endif; ?>
      <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
        <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo"></a>
      <?php endif; ?>
      <a href="catalogo.php">Catalogo</a>
      <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
      <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
      <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
      <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
    </div>

  </div>


  <!-- contenuto per login -->
  <div class="content">
    <div class="login_container">
      <div class="login_form">

        <form action="risorse/PHP/login.php" method="post">
          <h2>Accedi al tuo account</h2>
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required />


          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required />
          <?php
          if (isset($_SESSION['error_username']) && $_SESSION['error_username'] == true) {
            echo "<h3>Utente non registrato</h3>";
            unset($_SESSION['error_username']);
          }
          if (isset($_SESSION['error_password']) && $_SESSION['error_password'] == true) {
            echo "<h3>Password errata</h3>";
            unset($_SESSION['error_password']);
          }
          if (isset($_SESSION['error_users']) && $_SESSION['error_users'] == true) {
            echo "<h3>Errore nel recupero degli utenti</h3>";
            unset($_SESSION['error_users']);
          }
          // se ho stato bannato non faccio fare il login
          if (isset($_SESSION['error_banned']) && $_SESSION['error_banned'] == true) {
            echo "<h3>Il tuo account è stato bloccato. Contatta l'amministratore per maggiori informazioni.</h3>";
            unset($_SESSION['error_banned']);
          }
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