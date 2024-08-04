<?php
require_once 'config.php';
require_once 'database.php';
require_once 'helpers/messages.php';
require_once 'operations/terminumfrage_operations.php';
require_once 'render/render_results.php';

$conn = getDatabaseConnection();

$error_message = "";
$success_message = "";
$umfrage = null;
$moegliche_termine = [];
$teilnehmer_antworten = [];

// Code aus der URL holen
$code = isset($_GET['code']) ? $_GET['code'] : '';

if (empty($code)) {
    setErrorMessage($error_message, "Kein gültiger Code angegeben.");
} else {
    $result = loadUmfrage($conn, $code);
    if ($result->num_rows > 0) {
        $umfrage = $result->fetch_assoc();
        $result_termine = loadMoeglicheTermine($conn, $umfrage['TerminumfrageId']);
        while ($termin = $result_termine->fetch_assoc()) {
            $moegliche_termine[] = $termin;
        }
        $result_antworten = loadTeilnehmerAntworten($conn, $umfrage['TerminumfrageId']);
        while ($antwort = $result_antworten->fetch_assoc()) {
            $teilnehmer_antworten[$antwort['Teilnehmer']][$antwort['MoeglicherTermin']] = true;
        }
    } else {
        setErrorMessage($error_message, "Keine gültige Umfrage für diesen Code gefunden.");
    }
}

// Verarbeitung der Abstimmung
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $teilnehmer = trim($_POST['teilnehmer']);
    $gewaehlte_termine = isset($_POST['termine']) ? $_POST['termine'] : [];

    if (empty($teilnehmer)) {
        setErrorMessage($error_message, "Bitte geben Sie Ihren Namen ein.");
    } else {
        // Antwort in die Datenbank einfügen
        $sql_antwort = "INSERT INTO TerminAntwort (Teilnehmer) VALUES (?)";
        $stmt_antwort = $conn->prepare($sql_antwort);
        $stmt_antwort->bind_param("s", $teilnehmer);

        if ($stmt_antwort->execute()) {
            $antwort_id = $stmt_antwort->insert_id;

            // Gewählte Termine einfügen
            if (!empty($gewaehlte_termine)) {
                $sql_termin_antwort = "INSERT INTO TerminAntwortMoeglicherTermin (TerminAntwort, MoeglicherTermin) VALUES (?, ?)";
                $stmt_termin_antwort = $conn->prepare($sql_termin_antwort);

                foreach ($gewaehlte_termine as $termin_id) {
                    $stmt_termin_antwort->bind_param("ii", $antwort_id, $termin_id);
                    $stmt_termin_antwort->execute();
                }
            }

            setSuccessMessage($success_message, "Ihre Antwort wurde erfolgreich gespeichert. Vielen Dank für Ihre Teilnahme!");
            
            // Redirect nach erfolgreichem Absenden
            header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?code=" . $code);
            exit();
        } else {
            setErrorMessage($error_message, "Fehler beim Speichern Ihrer Antwort. Bitte versuchen Sie es erneut.");
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="de" class="h-full bg-gray-100">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terminumfrage</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-full">
  <div class="min-h-full">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Terminumfrage</h1>
      </div>
    </header>
    <main>
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
            <?php if (!empty($error_message)): ?>
            <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
            <p class="text-green-500 mb-4"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if ($umfrage): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($umfrage['Titel']); ?></h2>
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($umfrage['Beschreibung'])); ?></p>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?code=' . $code);?>"
              class="space-y-6">
              <div>
                <label for="teilnehmer" class="block text-sm font-medium text-gray-700">Ihr Name:</label>
                <input type="text" id="teilnehmer" name="teilnehmer" required
                  class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
              </div>

              <h3 class="text-xl font-bold mb-2">Abstimmungsergebnisse:</h3>
              <div class="overflow-x-auto">
                <?php echo renderResultsTable($moegliche_termine, $teilnehmer_antworten); ?>
              </div>

              <div>
                <button type="submit" name="submit"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Abstimmen
                </button>
              </div>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>

</html>