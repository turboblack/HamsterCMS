<?php
$default_template = 'plain';
$default_directory = 'files';
$blog_directory = 'blog';

// Determine if the request is for a blog post
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));
$is_blog_post = count($path_parts) == 2 && $path_parts[0] == 'blog' && !empty($path_parts[1]);

$page = null;
$blog_page = null;
$template_file = null;

if ($is_blog_post) {
    $blog_post_name = urldecode($path_parts[1]); // Decode the blog post name from the URL
    $blog_page = "./{$blog_directory}/" . str_replace(['/', '\\'], '', $blog_post_name) . '.txt';

    if (!file_exists($blog_page)) {
        // Если блог-пост не найден — показать error404
        header("HTTP/1.0 404 Not Found");
        readfile("error404.html");
        exit;
    }

    // Use the specific blog post page as the page to load
    $page = $blog_page;
} else {
    if (isset($_GET['p'])) {
        $requested = str_replace(['/', '\\'], '', trim($_GET['p'], '/'));
        $page = "./{$default_directory}/{$requested}.txt";

        if (!file_exists($page)) {
            // Если обычная страница не найдена — показать error404
            header("HTTP/1.0 404 Not Found");
            readfile("error404.html");
            exit;
        }
    } else {
        // Ничего не запрошено — показать первую страницу
        $nav = glob("{$default_directory}/*.txt");
        usort($nav, function ($a, $b) {
            return strcmp(basename($a), basename($b));
        });

        $page = reset($nav);
    }
}

// Determine the template file
$template_file = null;
$template_directory = __DIR__ . '/templates'; // Absolute path to templates directory
if ($is_blog_post) {
    // For blog posts, use the specified template in blog/{post_name}.txt_
    $blog_template_file = "./{$blog_directory}/" . basename($page, '.txt') . '.txt_';
    if (file_exists($blog_template_file)) {
        $template_name = trim(file_get_contents($blog_template_file));
        $template_file = "{$template_directory}/{$template_name}/index.html";
    }
} else {
    // For regular pages, use a specific template if available, otherwise use default
    $page_template_file = "./{$default_directory}/" . basename($page, '.txt') . '.txt_';
    if (file_exists($page_template_file)) {
        $template_name = trim(file_get_contents($page_template_file));
        $template_file = "{$template_directory}/{$template_name}/index.html";
    } else {
        $template_file = "{$template_directory}/{$default_template}/index.html";
    }
}

if (!file_exists($template_file)) {
    // Handle case where template file doesn't exist
    header("HTTP/1.0 500 Internal Server Error");
    echo "Template file not found or inaccessible.";
    exit;
}

$output = file_get_contents($template_file);

// Replace [[CONTENTS]] with the appropriate content
if ($is_blog_post) {
    $output = str_replace('[[CONTENTS]]', file_get_contents($blog_page), $output);
} else {
    $output = str_replace('[[CONTENTS]]', file_get_contents($page), $output);
}

// Insert dynamic navigation into the template
$navigation = '';
if (str_contains($output, '[[NAVIGATION]]')) {
    $nav = glob("{$default_directory}/*.txt");
    sort($nav);
    foreach ($nav as $file) {
        $link = preg_replace('/^files\/(.*)\.txt$/i', '$1', $file);
        $navigation .= "<a href=\"/" . ($link == "index" ? "" : urlencode($link)) . "\">{$link}</a><br>\n";
    }
    $output = str_replace('[[NAVIGATION]]', $navigation, $output);
}

// Insert blog post content or a list of all posts
if (str_contains($output, '[[BLOG_CONTENT]]')) {
    if ($blog_page !== null && file_exists($blog_page)) {
        $blog_content = file_get_contents($blog_page);
        $output = str_replace('[[BLOG_CONTENT]]', $blog_content, $output);
    } else {
        $blog_list = '';
        $blog_posts = glob("{$blog_directory}/*.txt");
        foreach ($blog_posts as $post) {
            $post_name = basename($post, '.txt');
            $blog_list .= "<a href=\"/blog/" . urlencode($post_name) . "\">{$post_name}</a><br>\n";
        }
        $output = str_replace('[[BLOG_CONTENT]]', $blog_list, $output);
    }
}

// Find included files and insert their content
preg_match_all('/\[\[([^\]]+\.txt)\]\]/', $output, $matches);
if (!empty($matches[1])) {
    foreach ($matches[1] as $match) {
        $filename = "./includes/{$match}";
        if (file_exists($filename)) {
            $file_contents = file_get_contents($filename);
            $output = str_replace("[[{$match}]]", $file_contents, $output);
        }
    }
}

// Replace paths to resources like images with absolute paths
$output = str_replace('src="templates/', 'src="/templates/', $output);
$output = str_replace('href="templates/', 'href="/templates/', $output);

echo $output;
?>
