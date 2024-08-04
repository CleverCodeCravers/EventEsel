<?php
require_once "helpers/termin_helpers.php";

function renderResultsTable($moegliche_termine, $teilnehmer_antworten) {
    ob_start();
    ?>
<div class="overflow-x-auto">
  <table class="w-full mb-4 bg-white border border-gray-500 rounded-lg border-dotted">
    <thead>
      <tr>
        <th class="text-left px-4 py-2 border-b border-gray-200 w-1/12 whitespace-nowrap">Datum</th>
        <th class="text-left px-4 py-2 border-b border-gray-200 w-1/12 whitespace-nowrap">Deine Stimme</th>
        <th class="text-left px-4 py-2 border-b border-gray-200 w-1/12 whitespace-nowrap">Stimmen bisher</th>
        <th class="text-left px-4 py-2 border-b border-gray-200 w-auto whitespace-nowrap">von</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($moegliche_termine as $termin): ?>
      <tr class="border-b border-gray-500">
        <td class="px-4 py-2 border-b border-gray-200"><?php echo date('d.m.Y', strtotime($termin['Datum'])); ?></td>
        <td class="px-4 py-2 border-b border-gray-200">
          <input type="checkbox" name="termine[]" value="<?php echo $termin['MoeglicherTerminId']; ?>" class="mr-2">
        </td>
        <td class="px-4 py-2 border-b border-gray-200">
          <?php echo countZusagen($termin['MoeglicherTerminId'], $teilnehmer_antworten); ?></td>
        <td class="px-4 py-2 border-b border-gray-200">
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

<?php
    return ob_get_clean();
}
?>