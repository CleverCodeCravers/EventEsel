<?php
function renderTextoptionResultsTable($textoptionen, $teilnehmer_antworten) {
    $html = '<table class="min-w-full divide-y divide-gray-200">';
    $html .= '<thead class="bg-gray-50"><tr>';
    $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Option</th>';
    $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auswahl</th>';
    $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stimmen</th>';
    $html .= '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teilnehmer</th>';
    $html .= '</tr></thead>';
    $html .= '<tbody class="bg-white divide-y divide-gray-200">';

    foreach ($textoptionen as $option) {
        $votes = 0;
        $voters = [];
        foreach ($teilnehmer_antworten as $teilnehmer => $antworten) {
            if (in_array($option['TextoptionId'], $antworten)) {
                $votes++;
                $voters[] = htmlspecialchars($teilnehmer);
            }
        }

        $html .= '<tr>';
        $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . htmlspecialchars($option['Text']) . '</td>';
        $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">';
        $html .= '<input type="checkbox" name="optionen[]" value="' . $option['TextoptionId'] . '" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">';
        $html .= '</td>';
        $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . $votes . '</td>';
        $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . implode(', ', $voters) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}