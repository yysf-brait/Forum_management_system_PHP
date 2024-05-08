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



    $date = explode(' ', $details['AT'])[0]; 

    if (!isset($stats[$date])) {
        $stats[$date] = [
            'createdArticles' => [],
            'deletedArticles' => [],
            'mostViewedArticles' => [],
            'mostEditedArticles' => []
        ];
    }

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


foreach ($stats as $date => $data) {
    echo "date: $date\t";
    echo "new articles created: " . count($data['createdArticles']) . "\t";
    echo "deleted articles: " . count($data['deletedArticles']) . "\t";

    if (!empty($data['mostViewedArticles'])) {
        arsort($data['mostViewedArticles']);
        $mostViewed = key($data['mostViewedArticles']);
        $views = $data['mostViewedArticles'][$mostViewed];
        echo "most viewed article: Article $mostViewed (views: $views)\t";
    } else {
        echo "no article view records\t";
    }

    if (!empty($data['mostEditedArticles'])) {
        arsort($data['mostEditedArticles']);
        $mostEdited = key($data['mostEditedArticles']);
        $edits = $data['mostEditedArticles'][$mostEdited];
        echo "most edited article: Article $mostEdited (edits: $edits)\t";
    } else {
        echo "no article edit records\t";
    }
    echo "<br>--------------------------------------------<br>";
}
?>
