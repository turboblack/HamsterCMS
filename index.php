<?php
$default_template = 'plain';
$default_directory = 'files';
$page = isset($_GET['p']) ? "./{$default_directory}/".str_replace(['/', '\\', '.txt'], '', trim($_GET['p'], '/')).'.txt' : null;

// If no specific page is requested or if the requested page is not found, get the first page in alphabetical order
if ($page === null || !file_exists($page)) {
    $nav = glob("{$default_directory}/*.txt"); /* Change the path to the folder with the files */
    usort($nav, function ($a, $b) { /* Sort by filename alphabetically */
        return strcmp(basename($a), basename($b));
    });

    // Set the first page in alphabetical order as the default page
    $page = reset($nav);
}

$template_file = "./templates/". (file_exists("{$page}_") ? trim(file_get_contents("{$page}_")) : "{$default_template}") ."/index.html";
if (!file_exists($template_file)) {
    echo "Template not found ({$template_file})";
    header("Status: 404 Not Found");
    exit;
}

$output = file_get_contents($template_file);
/* fill contents in template */
$output = str_replace('[[CONTENTS]]', file_get_contents($page), $output);
/* enter dynamic navigation into template. Navigation can be referenced in page or template */
$navigation = '';
if (str_contains($output, '[[NAVIGATION]]')) {
    $nav = glob("{$default_directory}/*.txt"); /* Change the path to the folder with the files */
    sort($nav); 
    foreach ($nav as $file) {
        $link = preg_replace('/^files\/(.*)\.txt$/i', '$1', $file);
        $navigation .= "<a href=\"/" . ($link == "index" ? "" : urlencode($link)) . "\">{$link}</a><br>\n"; /* Use links like /news */
    }
    $output = str_replace('[[NAVIGATION]]', $navigation, $output);
}

/* look for included files and enter their content. Included files can be referenced in pages and templates */
preg_match_all('/\[\[([^\]]+\.txt)\]\]/', $output, $matches); /* would match e.g. [[about.txt]] and about.txt (in matchgroup 1) */
if (!empty($matches[1])) {
    foreach ($matches[1] as $match) {
        $filename = "./includes/{$match}";
        if (file_exists($filename)) {
            $file_contents = file_get_contents($filename);
            $output = str_replace("[[{$match}]]", $file_contents, $output);
        }
    }
}

echo $output;
?>
