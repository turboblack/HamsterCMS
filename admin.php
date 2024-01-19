<?php
session_start();
error_reporting(0);

$passw1 = md5("login");
$passw2 = md5("password");

print"<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
  <meta http-equiv='Content-Language' content='EN'>
  <style>
    input[type='submit'], input[type='reset'] {
      width: auto; 
      height: 30px; 
      background-color: #fff;
      color: #000;
    }

    input[type='submit']:active, input[type='reset']:active {
      background-color: #cc0000;
      color: #fff;
    }
  </style>
</head>
<body style=background-color:#fff>
  <center>";

if ($_POST['pas1'] && $_POST['pas2']) {
  $_SESSION['pass1'] = $_POST['pas1'];
  $_SESSION['pass2'] = $_POST['pas2'];
}

if (md5($_SESSION['pass1']) != $passw1 || md5($_SESSION['pass2']) != $passw2) {
  die('<br /><br /><br /><br /><br /><br /><form method=post>
  <input name=pas1 type=password placeholder="Enter login"><br>
  <input name=pas2 type=password placeholder="Enter Password"><br>
  <input type=submit value=go></form>');
}

$file_name = $_POST['file_name'];


if ($_POST['perez']) {
  $perez = $_POST['perez'];


  if (pathinfo($perez, PATHINFO_EXTENSION) !== 'txt') {
    die("<b style=color:#cc0000>Error: Editing is only allowed for files with the extension .txt</b>");
  }

  $h = fopen("$perez", "w");
  $text = $_POST['Message'];

  if (fwrite($h, $text)) {
    echo "<b style=color:#fff>Rewrite completed successfully</b>";
  } else {
    echo "<b style=color:#cc0000>An error occurred while writing data</b>";
  }

  fclose($h);
}

if ($_POST['udal']) {
  $udal = $_POST['udal'];

 
  if (pathinfo($udal, PATHINFO_EXTENSION) !== 'txt') {
    die("<b style=color:#cc0000>Error: Deletion is allowed only for files with the extension .txt</b>");
  }

  if (unlink($udal)) {
    echo "<b style=color:#ffff>File \"$udal\" deleted</b>";
  } else {
    echo "<b style=color:#cc0000>Error deleting file</b>";
  }
}

echo "<br />
  <table>
    <tbody>
      <tr>
        <td>
          <form action='' method='post'>
            <select name='file_name'>";

$dir_name = $_POST['dir_name'];

if (empty($dir_name)) {
  $dir_name = $_SESSION['dir_name'];
}

if (empty($dir_name)) {
  $dir_name = './';
}

$path = "$dir_name";
list_directory($path);

function list_directory($dir) {
  $file_list = '';

  if ($dh = opendir($dir)) {
    while (($file = readdir($dh)) !== false) {
      if ($file !== '.' AND $file !== '..') {
        $current_file = "{$dir}/{$file}";

        if (is_file($current_file) && pathinfo($current_file, PATHINFO_EXTENSION) === 'txt') {
          if (empty($dir_name)) {
            $dir_name = './';
          }

          $path = "$dir_name";
          print "<option>$dir$file</option>";
        }
      }
    }
  }
}

$_SESSION['dir_name'] = $_POST['dir_name'];

echo "
            </select>
            <input type='submit' name='submit' value='open'/>
          </form>
        </td>
        <td>
          <form method='post'>
            <select name='dir_name'>
              <option>./</option>";

$path = "./";
get_directory_list($path);

function get_directory_list($path) {
  if (is_dir($path)) {
    $dh = opendir($path);

    while (false !== ($dir = readdir($dh))) {
      if (is_dir($path . $dir) && $dir !== '.' && $dir !== '..') {
        $subdir = $path . $dir . '/';

        print "<option>" . $subdir . "</option>";
        get_directory_list($subdir);
      } else {
        next;
      }
    }

    closedir($dh);
  }
}

echo "
            </select>
            <input type='submit' name='submit' value='go'/>
          </form>
        </td>
      </tr>
    </tbody>
  </table>
  <form method='post'>";

if (!empty($file_name)) {
  $text = file_get_contents("$file_name");
}

echo "<textarea name='Message' cols='150' rows='40'>" . htmlspecialchars($text) . "</textarea><br />
  <input type='text' name='perez' value='$file_name' size='30'><input type='submit' name='submit' value='save'/><input type='reset' value='clear'></form>
  <form method='post'><input type='hidden' name='udal' value='$file_name'><input type='submit' name='submit' value='delete'/></form>
  <!--When distributing, a link is required http://old.net.eu.org/--><small><a style='color:#000; text-decoration:none' href=''></a></small>
  <center>
  <body>
  <html>";
?>
<center>
  <script src="//js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
  <script type="text/javascript">bkLib.onDomLoaded(function () { nicEditors.allTextAreas({ buttonList: ["bold", "italic", "underline", "left", "center", "right", "justify", "ol", "ul", "fontFormat", 'fontSize', 'fontFamily', "indent", "outdent", "image", "link", "unlink", "xhtml", "table", 'upload', 'forecolor', 'bgcolor'] }) });</script>
</center>
