<?php
require_once "helpers/termin_helpers.php";

function renderResultsTable($moegliche_termine, $teilnehmer_antworten) {
    ob_start();
    ?>
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
<?php
    return ob_get_clean();
}
?>