<?php
/**
 * Diff de Texto - Comparar dos textos línea por línea
 */
header('Content-Type: text/html; charset=utf-8');

$texto1 = $texto2 = '';
$diff = null; $stats = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto1 = $_POST['texto1'] ?? '';
    $texto2 = $_POST['texto2'] ?? '';

    $lineas1 = explode("\n", str_replace("\r\n", "\n", $texto1));
    $lineas2 = explode("\n", str_replace("\r\n", "\n", $texto2));

    $max = max(count($lineas1), count($lineas2));
    $diff = [];
    $agregadas = $eliminadas = $iguales = 0;

    for ($i = 0; $i < $max; $i++) {
        $l1 = $lineas1[$i] ?? null;
        $l2 = $lineas2[$i] ?? null;

        if ($l1 === null && $l2 !== null) {
            $diff[] = ['tipo' => 'add', 'num' => $i+1, 'texto' => $l2];
            $agregadas++;
        } elseif ($l1 !== null && $l2 === null) {
            $diff[] = ['tipo' => 'del', 'num' => $i+1, 'texto' => $l1];
            $eliminadas++;
        } elseif ($l1 !== $l2) {
            $diff[] = ['tipo' => 'del', 'num' => $i+1, 'texto' => $l1];
            $diff[] = ['tipo' => 'add', 'num' => $i+1, 'texto' => $l2];
            $eliminadas++; $agregadas++;
        } else {
            $diff[] = ['tipo' => 'same', 'num' => $i+1, 'texto' => $l1];
            $iguales++;
        }
    }
    $stats = compact('agregadas', 'eliminadas', 'iguales');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Comparador de Texto (Diff) Online | ConfiguroWeb</title>
<meta name="description" content="Compara dos textos línea por línea y resalta las diferencias. Útil para revisar cambios en código o documentos. Gratis en ConfiguroWeb.">
<meta name="keywords" content="diff texto, comparador texto, comparar documentos, diferencias, line diff">
<link rel="canonical" href="https://demoscweb.com/github/php-diff-texto/">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebApplication","name":"Comparador de Texto","applicationCategory":"UtilitiesApplication","operatingSystem":"Any","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"},"author":{"@type":"Person","name":"ConfiguroWeb","url":"https://configuroweb.com"}}
</script>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
  <h1>⚖️ Comparador de Texto</h1>
  <p class="subtitle">Encuentra las diferencias entre dos textos</p>
</header>
<main>
  <form method="POST">
    <div class="grid-2">
      <div>
        <label for="texto1">Texto original</label>
        <textarea name="texto1" id="texto1" rows="8" placeholder="Pega aquí el texto original..."><?php echo htmlspecialchars($texto1); ?></textarea>
      </div>
      <div>
        <label for="texto2">Texto modificado</label>
        <textarea name="texto2" id="texto2" rows="8" placeholder="Pega aquí el texto modificado..."><?php echo htmlspecialchars($texto2); ?></textarea>
      </div>
    </div>
    <button type="submit" class="btn-primary">⚖️ Comparar</button>
  </form>

  <?php if ($diff !== null): ?>
  <div class="resultados">
    <h2>Diferencias encontradas</h2>
    <div class="grid-3">
      <div class="tarjeta-sm"><span class="etiqueta">Líneas iguales</span><span class="valor-sm"><?php echo $stats['iguales']; ?></span></div>
      <div class="tarjeta-sm"><span class="etiqueta">Eliminadas (−)</span><span class="valor-sm neg"><?php echo $stats['eliminadas']; ?></span></div>
      <div class="tarjeta-sm"><span class="etiqueta">Agregadas (+)</span><span class="valor-sm pos"><?php echo $stats['agregadas']; ?></span></div>
    </div>

    <div style="background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:8px;font-family:'Consolas',monospace;font-size:.9rem;overflow-x:auto;margin-top:1rem">
      <?php foreach ($diff as $linea): ?>
        <?php
          $color = $simbolo = '';
          if ($linea['tipo']==='add')  { $color='#4caf50'; $simbolo='+'; }
          elseif ($linea['tipo']==='del') { $color='#f44336'; $simbolo='-'; }
          else { $color='#888'; $simbolo=' '; }
        ?>
        <div style="color:<?php echo $color; ?>;padding:2px 0;white-space:pre-wrap">
          <?php echo $simbolo; ?> <?php echo str_pad($linea['num'], 3, ' ', STR_PAD_LEFT); ?> │ <?php echo htmlspecialchars($linea['texto']); ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <section class="info">
    <h2>¿Cómo funciona?</h2>
    <p>El comparador analiza ambos textos <strong>línea por línea</strong> y muestra:</p>
    <ul style="line-height:1.8">
      <li><span style="color:#f44336">●</span> Líneas eliminadas (estaban en el original pero no en el modificado)</li>
      <li><span style="color:#4caf50">●</span> Líneas agregadas (nuevas en el texto modificado)</li>
      <li><span style="color:#888">●</span> Líneas sin cambios</li>
    </ul>
    <p>Ideal para revisar ediciones en contratos, código, artículos y cualquier documento de texto.</p>
  </section>
</main>
<footer>
  <p>Desarrollado por <a href="https://configuroweb.com" target="_blank">ConfiguroWeb</a> ·
     <a href="https://appscweb.com/citas/" target="_blank">Sistema de Citas</a> ·
     <a href="https://appscweb.com/negocios/" target="_blank">Gestión de Negocios</a></p>
  <p>&copy; <?php echo date('Y'); ?> ConfiguroWeb</p>
</footer>
<script src="assets/script.js"></script>
</body>
</html>