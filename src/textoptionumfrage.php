<?php
require_once 'database.php';
require_once 'helpers/messages.php';
require_once 'operations/textoptionumfrage_operations.php';
require_once 'render/render_textoption_results.php';

$conn = getDatabaseConnection();

$error_message = "";
$success_message = "";
$umfrage = null;
$textoptionen = [];
$teilnehmer_antworten = [];

// Code aus der URL holen
$code = isset($_GET['code']) ? $_GET['code'] : '';

if (empty($code)) {
    setErrorMessage($error_message, "Kein gültiger Code angegeben.");
} else {
    $result = loadTextoptionUmfrage($conn, $code);
    if ($result->num_rows > 0) {
        $umfrage = $result->fetch_assoc();
        $result_optionen = loadTextoptionen($conn, $umfrage['TextoptionenumfrageId']);
        while ($option = $result_optionen->fetch_assoc()) {
            $textoptionen[] = $option;
        }
        $result_antworten = loadTextoptionAntworten($conn, $umfrage['TextoptionenumfrageId']);
        while ($antwort = $result_antworten->fetch_assoc()) {
            $teilnehmer_antworten[$antwort['Teilnehmer']][] = $antwort['TextoptionId'];
        }
    } else {
        setErrorMessage($error_message, "Keine gültige Umfrage für diesen Code gefunden.");
    }
}

// Verarbeitung der Abstimmung
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $teilnehmer = trim($_POST['teilnehmer']);
    $gewaehlte_optionen = isset($_POST['optionen']) ? $_POST['optionen'] : [];

    if (empty($teilnehmer)) {
        setErrorMessage($error_message, "Bitte geben Sie Ihren Namen ein.");
    } else {
        // Antworten in die Datenbank einfügen
        $success = true;
        foreach ($gewaehlte_optionen as $option_id) {
            $sql_antwort = "INSERT INTO TextoptionAntwort (Textoption, Teilnehmer) VALUES (?, ?)";
            $stmt_antwort = $conn->prepare($sql_antwort);
            $stmt_antwort->bind_param("is", $option_id, $teilnehmer);

            if (!$stmt_antwort->execute()) {
                $success = false;
                break;
            }
        }

        if ($success) {
            setSuccessMessage($success_message, "Ihre Antwort wurde erfolgreich gespeichert. Vielen Dank für Ihre Teilnahme!");
            
            // Reload the page to show updated results
            header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?code=" . $code . "&saved=1");
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
  <title>Textoptionumfrage</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-full">
  <div class="min-h-full">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Textoptionumfrage</h1>
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
            <?php if (isset($_GET['saved']) && $_GET['saved'] == 1): ?>
            <div class="bg-green-500 text-white font-bold text-lg text-center p-4 mb-4 rounded">
              Vielen Dank für deine Abstimmung!
            </div>
            <?php endif; ?>

            <?php if ($umfrage): ?>
            <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($umfrage['Titel']); ?></h2>
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($umfrage['Beschreibung'])); ?></p>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?code=' . $code);?>"
              class="space-y-6">

              <h3 class="text-xl font-bold mb-2">Abstimmung:</h3>
              <div>
                <label for="teilnehmer" class="block text-sm font-medium text-gray-700">Dein Name:</label>
                <input type="text" id="teilnehmer" name="teilnehmer" required
                  class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
              </div>

              <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                  <?php echo renderTextoptionResultsTable($textoptionen, $teilnehmer_antworten); ?>
                </div>
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