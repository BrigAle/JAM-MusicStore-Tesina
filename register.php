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
      <!-- cliente links -->
      <a href="catalogo.php">Catalogo</a>
      <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
      <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
      <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
      <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
    </div>

  </div>

  <?php
  $old = $_SESSION['old_data'] ?? [
    'nome' => '',
    'cognome' => '',
    'username' => '',
    'email' => '',
    'telefono' => '',
    'indirizzo' => ''
  ];
  ?>

  <div class="content">
    <!-- registrazione -->
    <div class="register_form">
      <div class="register_container">
        <h2>Registrazione</h2>

        <form action="risorse/PHP/register.php" method="POST">
          <div class="row">
            <div class="col">
              <label for="nome">Nome</label>
              <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($old['nome']); ?>">
            </div>
            <div class="col">
              <label for="cognome">Cognome</label>
              <input type="text" id="cognome" name="cognome" required value="<?php echo htmlspecialchars($old['cognome']); ?>">
            </div>
          </div>

          <label for="username">Username</label>
          <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($old['username']); ?>">

          <label for="email">Email</label>
          <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($old['email']); ?>">

          <div class="row">
            <div class="col">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" required>
            </div>
            <div class="col">
              <label for="conferma_password">Conferma Password</label>
              <input type="password" id="conferma_password" name="conferma_password" required>
            </div>
          </div>

          <label for="tel">Telefono</label>
          <input type="tel" id="tel" name="telefono" value="<?php echo htmlspecialchars($old['telefono']); ?>">

          <label for="indirizzo">Indirizzo</label>
          <input type="text" id="indirizzo" name="indirizzo" required value="<?php echo htmlspecialchars($old['indirizzo']); ?>">

          <input type="submit" value="Registrati">
        </form>

        <?php if (isset($_SESSION['error_user'])) {
          echo '<p class="msg error">Username già esistente. Scegline un altro.</p>';
          unset($_SESSION['error_user']);
        } ?>

        <?php if (isset($_SESSION['error_email'])) {
          echo '<p class="msg error">Email già esistente. Scegline un\'altra.</p>';
          unset($_SESSION['error_email']);
        } ?>

        <?php if (isset($_SESSION['error_password'])) {
          echo '<p class="msg error">Le password non corrispondono. Riprova.</p>';
          unset($_SESSION['error_password']);
        } ?>

        <?php if (isset($_SESSION['success'])) {
          echo '<p class="msg success">Registrazione avvenuta con successo! Effettua il login.</p>';
          unset($_SESSION['success']);
        } ?>

      </div>
    </div>
  </div>

  <?php
  unset($_SESSION['old_data']);
  ?>


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