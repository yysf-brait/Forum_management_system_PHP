<?php
session_start();


define("ARTICLE_LOG_PATH", "article_logs.txt");
define("USER_LOG_PATH", "user.txt");

function parseLogFile($filePath) {
    $fileContents = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $logEntries = [];
    foreach ($fileContents as $line) {
        $parts = explode(' ', $line);
        $entry = [];
        foreach ($parts as $part) {
            list($key, $value) = explode(':', $part);
            $entry[$key] = $value;
        }
        $logEntries[] = $entry;
    }
    return $logEntries;
}

function aggregateStats($logEntries) {
    $dailyVisits = [];
    foreach ($logEntries as $entry) {
        $date = explode(' ', $entry['AT'])[0];
        if (!isset($dailyVisits[$date])) {
            $dailyVisits[$date] = 0;
        }
        $dailyVisits[$date]++;
    }
    return $dailyVisits;
}

$articleLogs = parseLogFile(ARTICLE_LOG_PATH);
$userLogs = parseLogFile(USER_LOG_PATH);

$combinedLogs = array_merge($articleLogs, $userLogs);

$dailyVisits = aggregateStats($combinedLogs);

arsort($dailyVisits);

foreach ($dailyVisits as $date => $count) {
    echo "Date: $date - Visits: $count<br>";
}
?>
