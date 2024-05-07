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

// 读取并解析日志
$articleLogs = parseLogFile(ARTICLE_LOG_PATH);
$userLogs = parseLogFile(USER_LOG_PATH);

// 合并日志
$combinedLogs = array_merge($articleLogs, $userLogs);

// 聚合统计数据
$dailyVisits = aggregateStats($combinedLogs);

// 对日访问次数进行降序排序
arsort($dailyVisits);

// 输出结果
foreach ($dailyVisits as $date => $count) {
    echo "Date: $date - Visits: $count<br>";
}
?>
