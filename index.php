<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jam Music Store</title>
  <link rel="stylesheet" href="risorse/CSS/style.css" type="text/css" />
</head>

<body>
  <div class="header">
    <div class="logo">
      <img src="risorse/IMG/JAM_logo.png" alt="JAM Music Store" />
    </div>

    <div class="navSearch">
      <form action="index.php" method="get">
        <div class="searchContainer">

          <select name="categoria">
            <option value="tutto">Tutto</option>
            <option value="artisti">Artisti</option>
            <option value="album">Album</option>
          </select>

          <input type="text" name="query" placeholder="Cerca brani, artisti, album..." />
          <button type="submit">🔍</button>

          <!-- Checkbox nascosto -->
          <input type="checkbox" id="advanced_commutator" style="display: none;" />
          <label for="advanced_commutator" class="label_commutator">Ricerca avanzata</label>

          <!-- Questo deve essere subito dopo il checkbox -->
          <div class="advanced_filters">
            <div class="filters_container">
              <h3>Filtri avanzati</h3>
              <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
              <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
              <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
            </div>
          </div>

        </div>
      </form>
    </div>

    <div class="navLink">
      <a href="#"><img src="risorse/IMG/home.png" alt="casetta" /></a>
      <a href="#">Catalogo</a>
      <a href="#">Contatti</a>
      <a href="#">Login</a>
    </div>
  </div>

  <div class="content">
    <!-- contenuto -->
  </div>

  <div class="pdp">
    <p>&copy; 2025 JAM Music Store</p>
  </div>
</body>

</html>
