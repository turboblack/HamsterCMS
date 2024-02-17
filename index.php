<?php
$default_template = 'plain';
$default_directory = 'files';
$page = isset($_GET['p']) ? "./{$default_directory}/".str_replace(['/', '\\', '.txt'], '', trim($_GET['p'], '/')).'.txt' : "./{$default_directory}/index.txt";
/* 404 if requested page file does not exist */
if (!file_exists($page)) {
    echo ("ERROR 404 - Page Not Found ({$page})");
    header("Status: 404 Not Found");
    exit;
}
/* 404 if requested template file does not exist */
$template_file = "./templates/". (file_exists("{$page}_") ? trim(file_get_contents("{$page}_")) : "{$default_template}") ."/index.html";
if (!file_exists($template_file)) {
    echo "Template not found ({$template_file})";
    header("Status: 404 Not Found");
    exit;
}
/* 404 if requested page is marked as include only */
$page_contents = file_get_contents($page);
if (preg_match('/<!--.*include.*-->/', $page_contents, $matches)) {
    echo ("ERROR 404 - Page Not Found ({$page}).");
    header("Status: 404 Not Found");
    exit;
}
/* fill contents in template */
$output = str_replace('[[CONTENTS]]', $page_contents, file_get_contents($template_file));
/* enter dynamic navigation into template. Navigation can be referenced in page or template */
$navigation = '';
if (str_contains($output, '[[NAVIGATION]]')) {
    $nav = glob("{$default_directory}/*.txt"); /* Change the path to the folder with the files */
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
        $navigation .= "<a href=\"/" . ($link == "index" ? "" : urlencode($link)) . "\">{$link}</a><br>\n"; /* Use links like /news */
    }
    $output = str_replace('[[NAVIGATION]]', $navigation, $output);
}
/* look for included files and enter their content. Included files can be referenced in pages and templates */
preg_match_all('/\[\[([^\]]+\.txt)\]\]/', $output, $matches); /* would match e.g. [[about.txt]] and about.txt (in matchgroup 1) */
if ($matches && $matches[1]) {
    foreach ($matches as $groups) {
        foreach ($groups as $match) {
            if (empty($match) || str_starts_with($match, '[[')) continue;
            $filename = "./{$default_directory}/{$match}";
            if (file_exists($filename)) {
                $file_contents = file_get_contents($filename);
                $output = str_replace("[[{$match}]]", $file_contents, $output);
            }
        }
    }
}
echo $output;
