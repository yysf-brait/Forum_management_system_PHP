<?php
$logFile = 'article_logs.txt';  // 修改为你的日志文件路径
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

    // 时间统计
    $timestamp = strtotime($details['AT']);
    $hour = date('G', $timestamp);
    $hourlyAccess[(int)$hour]++;

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

echo "最繁忙的时段: $peakHour:00 - " . ($peakHour + 1) . ":00 with $maxAccesses accesses\n";
echo "最频繁访问的页面: Article ID $mostVisitedPage with $maxPageAccess visits\n";
echo "最常见的操作: $mostCommonOperation with $maxOperations occurrences\n";
?>
