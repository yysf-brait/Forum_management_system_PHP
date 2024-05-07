<?php
$logFile = 'article_logs.txt';
$lines = file($logFile);
$stats = [];

foreach ($lines as $line) {
    $parts = explode(' ', $line);
    $details = [];
    foreach ($parts as $part) {
        list($key, $value) = explode(':', $part);
        $details[$key] = $value;
    }



    $date = explode(' ', $details['AT'])[0]; // 获取日期部分


    if (!isset($stats[$date])) {
        $stats[$date] = [
            'createdArticles' => [],
            'deletedArticles' => [],
            'mostViewedArticles' => [],
            'mostEditedArticles' => []
        ];
    }

    // $details['Action']清除尾部空白字符
    $details['Action'] = trim($details['Action']);

    switch ($details['Action']) {
        case 'Create':
            if (!isset($stats[$date]['createdArticles'][$details['Article']])) {
                $stats[$date]['createdArticles'][$details['Article']] = 0;
            }
            $stats[$date]['createdArticles'][$details['Article']]++;
            break;
        case 'Delete':
            if (!isset($stats[$date]['deletedArticles'][$details['Article']])) {
                $stats[$date]['deletedArticles'][$details['Article']] = 0;
            }
            $stats[$date]['deletedArticles'][$details['Article']]++;
            break;
        case 'Read':
            if (!isset($stats[$date]['mostViewedArticles'][$details['Article']])) {
                $stats[$date]['mostViewedArticles'][$details['Article']] = 0;
            }
            $stats[$date]['mostViewedArticles'][$details['Article']]++;
            break;
        case 'Update':
            if (!isset($stats[$date]['mostEditedArticles'][$details['Article']])) {
                $stats[$date]['mostEditedArticles'][$details['Article']] = 0;
            }
            $stats[$date]['mostEditedArticles'][$details['Article']]++;
            break;
    }
}


// 输出结果
foreach ($stats as $date => $data) {
    echo "日期: $date\t";
    echo "新增文章数: " . count($data['createdArticles']) . "\t";
    echo "删除文章数: " . count($data['deletedArticles']) . "\t";

    if (!empty($data['mostViewedArticles'])) {
        arsort($data['mostViewedArticles']);
        $mostViewed = key($data['mostViewedArticles']);
        $views = $data['mostViewedArticles'][$mostViewed];
        echo "查看最多的文章: Article $mostViewed (查看次数: $views)\t";
    } else {
        echo "没有文章查看记录\t";
    }

    if (!empty($data['mostEditedArticles'])) {
        arsort($data['mostEditedArticles']);
        $mostEdited = key($data['mostEditedArticles']);
        $edits = $data['mostEditedArticles'][$mostEdited];
        echo "编辑最多的文章: Article $mostEdited (编辑次数: $edits)\t";
    } else {
        echo "没有文章编辑记录\t";
    }
    echo "<br>--------------------------------------------<br>";
}
?>
