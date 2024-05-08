<?php
$logFile = 'article_logs.txt';
$lines = file($logFile);

// 初始化统计数组
$hourlyAccess = array_fill(0, 24, 0);
$pageAccess = [];
$operationCount = [];

foreach ($lines as $line) {
    $parts = explode(' ', $line);
    $details = [];
    foreach ($parts as $part) {
        list($key, $value) = explode(':', $part);
        $details[$key] = trim($value);
    }
    // 获取datails中AT的下一个键
    $pos = array_search('AT', array_keys($details)) + 1;
    $t = array_keys($details);
    $nextKey = $t[$pos];

    $hourlyAccess[(int)$nextKey]++;

    // 页面访问统计
    if (!empty($details['Article'])) {
        if (!isset($pageAccess[$details['Article']])) {
            $pageAccess[$details['Article']] = 0;
        }
        $pageAccess[$details['Article']]++;
    }

    // 操作统计
    if (!empty($details['Action'])) {
        if (!isset($operationCount[$details['Action']])) {
            $operationCount[$details['Action']] = 0;
        }
        $operationCount[$details['Action']]++;
    }
}

// 最繁忙的时段
$maxAccesses = max($hourlyAccess);
$peakHour = array_search($maxAccesses, $hourlyAccess);

// 最频繁访问的页面
$maxPageAccess = max($pageAccess);
$mostVisitedPage = array_search($maxPageAccess, $pageAccess);

// 最常见的操作
$maxOperations = max($operationCount);
$mostCommonOperation = array_search($maxOperations, $operationCount);

echo "The busiest hour: $peakHour:00 - " . ($peakHour + 1) . ":00 with $maxAccesses accesses<br>";
echo "The most visited article:Article ID $mostVisitedPage with $maxPageAccess visits<br>";
echo "The most common operation: $mostCommonOperation with $maxOperations occurrences<br>";
?>
