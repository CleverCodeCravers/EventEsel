<?php
require_once "helpers/termin_helpers.php";

function renderResultsTable($moegliche_termine, $teilnehmer_antworten) {
    ob_start();
    ?>
<div class="overflow-x-auto">
  <table class="w-full mb-4 bg-white border-x border-y border-gray-500">
    <thead>
      <tr>
        <th class="text-left px-4 py-2 border-r border-gray-200 w-1/12 whitespace-nowrap">Datum</th>
        <th class="text-left px-4 py-2 border-r border-gray-200 w-1/12 whitespace-nowrap">Deine Stimme</th>
        <th class="text-left px-4 py-2 border-r border-gray-200 w-1/12 whitespace-nowrap">Stimmen bisher</th>
        <th class="text-left px-4 py-2 w-auto whitespace-nowrap">von</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($moegliche_termine as $termin): ?>
      <tr class="border-b border-gray-500">
        <td class="px-4 py-2 border-r border-gray-200"><?php echo date('d.m.Y', strtotime($termin['Datum'])); ?></td>
        <td class="px-4 py-2 border-r border-gray-200">
          <input type="checkbox" name="termine[]" value="<?php echo $termin['MoeglicherTerminId']; ?>" class="mr-2">
        </td>
        <td class="px-4 py-2 border-r border-gray-200">
          <?php echo countZusagen($termin['MoeglicherTerminId'], $teilnehmer_antworten); ?></td>
        <td class="px-4 py-2">
          <?php
          $teilnehmerMitZusage = [];
          foreach ($teilnehmer_antworten as $teilnehmer => $antworten) {
              if (isset($antworten[$termin['MoeglicherTerminId']])) { 
                  $teilnehmerMitZusage[] = htmlspecialchars($teilnehmer);
              }
          }
          echo implode(', ', $teilnehmerMitZusage);
          ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php /*
<div class="space-y-4">

  <?php foreach ($moegliche_termine as $termin): ?>
<div class="w-full md:w-1/3">
  <!-- Responsiv für verschiedene Bildschirmgrößen -->
  <button type="button"
    class="flex justify-between w-full px-4 py-2 text-left bg-gray-200 rounded-md focus:outline-none"
    onclick="toggleDropdown('<?php echo $termin['MoeglicherTerminId']; ?>')">
    <span><?php echo date('d.m.Y', strtotime($termin['Datum'])); ?></span>
    <span><?php echo countZusagen($termin['MoeglicherTerminId'], $teilnehmer_antworten); ?> Zusagen</span>
  </button>
  <div id="dropdown-<?php echo $termin['MoeglicherTerminId']; ?>"
    class="hidden mt-2 bg-white border rounded-md shadow-md max-w-full sm:max-w-xs">
    <ul class="max-h-48 overflow-y-auto">
      <?php foreach ($teilnehmer_antworten as $teilnehmer => $antworten): ?>
      <?php if (isset($antworten[$termin['MoeglicherTerminId']])): // Nur Teilnehmer mit Zusage ?>
      <li class="px-4 py-2"><?php echo htmlspecialchars($teilnehmer); ?> ✓</li>
      <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php endforeach; ?>
</div>

<script>
function toggleDropdown(id) {
  const dropdown = document.getElementById('dropdown-' + id);
  dropdown.classList.toggle('hidden');
}
</script>
*/
?>

<?php
    return ob_get_clean();
}
?>