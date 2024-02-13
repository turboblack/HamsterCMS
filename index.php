<?php
$default_template = 'plain';
$default_directory = './files/';
$page = isset($_GET['p']) ? trim($_GET['p'], '/') . '.txt' : 'index.txt';
$page = $default_directory . str_replace(['/', '\\'], '', $page);
if (!file_exists($page)) {
    echo ("ERROR 404 - Page Not Found");
    header("Status: 404 Not Found");
    exit;
}
$template_name = file_exists($page . '_') ? "./templates/" . trim(file_get_contents($page . '_')) . "/index.html" : "./templates/$default_template/index.html";
if (!file_exists($template_name)) {
    echo "Template not found ($template_name)";
    header("Status: 404 Not Found");
    exit;
}
$template = file_get_contents($template_name);
$contents = file_get_contents($page);
$navigation = '';
$nav = glob('files/*.txt'); /* Change the path to the folder with the files */
usort($nav, function ($a, $b) { /* sort by last edited, but 'index.txt' to top */
    if (basename($a) == 'index.txt') {
        return -1;
    } elseif (basename($b) == 'index.txt') {
        return 1;
    } else {
        return filemtime($b) - filemtime($a);
    }
});
foreach ($nav as $file) {
    $link = preg_replace('/^files\/(.*)\.txt$/i', '$1', $file);
    $navigation .= "<a href=\"/" . ($link == "index" ? "" : urlencode($link)) . "\">$link</a><br>\n"; /* Use links like /news */
}
$template = str_replace('[[CONTENTS]]', $contents, $template);
$template = str_replace('[[NAVIGATION]]', $navigation, $template);
preg_match('/\[\[([^\]]+\.txt)\]\]/', $template, $matches);
if ($matches && $matches[1]) {
    foreach ($matches as $match) {
        if (str_starts_with($match, '[[')) continue;
        $filename = $default_directory . $match;
        if (file_exists($filename)) {
            $file_contents = file_get_contents($filename);
            $template = str_replace('[[' . $match . ']]', $file_contents, $template);
        }
    }
}
echo $template;
