<?php
session_start();
error_reporting(0);
$valid_username = "login";
$valid_password = "password";

/* logout */
if ($_POST['logout']) {
  session_destroy();
  header("Location: admin.php");
  exit;
}
/* put posted credentials to session variables */
if ($_POST['username'] && $_POST['password']) {
  $_SESSION['_username'] = $_POST['username'];
  $_SESSION['_password'] = $_POST['password'];
}
/* reading input */
$filename = $_POST['filename'] ?? '';
$template = $_POST['template'] ?? '';
$directory = $_POST['directory'] ?? './files/';
$action_delete = isset($_POST['action_delete']);
$action_save = isset($_POST['action_save']);
$action_edit = isset($_POST['action_edit']);
$action_changedir = isset($_POST['action_changedir']);
$debuginfo = '';
$filelist = '';

/* check for valid credentials or halt execution with an error message */
$loggedin = (!empty($_SESSION['_username']) && $_SESSION['_username'] === $valid_username && !empty($_SESSION['_password']) && $_SESSION['_password'] === $valid_password);
if (!$loggedin && isset($_POST['username'])) {
  $debuginfo .= "<b style=color:#cc0000>Wrong login/password!</b> ";
}
if ($loggedin) {
  /* action: change directory */
  if ($action_changedir) {
    $filename = ''; /* unset filename, we are somewhere else now */
    $template = ''; /* unset template */
  }
  /* action: save the (new?) page's content */
if ($action_save) {
    $newfilename = $_POST['newfilename'] ? trim(trim($_POST['newfilename']),'/') : '';
    $filenameWithPath = './files/' . $newfilename . '.txt'; 
    $filecontent = $_POST['content'] ?? '';

    $h = fopen($filenameWithPath, "w");
    if (fwrite($h, $filecontent)) {
        $debuginfo .= "Saving content completed successfully. ";
    } else {
        $debuginfo .= "<b style=color:#cc0000>An error occurred while writing content data</b> ";
    }
    fclose($h);

    if (!empty($template)) {
        $h = fopen('./files/' . $newfilename . '.txt_', "w");
        if (fwrite($h, $template)) {
            $debuginfo .= "Saving template assertion completed successfully.";
        } else {
            $debuginfo .= "<b style=color:#cc0000>An error occurred while writing template assertion</b>";
        }
        fclose($h);
    } else {
        if (file_exists('./files/' . $newfilename . '.txt_')) {
            if (unlink('./files/' . $newfilename . '.txt_')) {
                $debuginfo .= "Removing template assertion completed successfully.";
            } else {
                $debuginfo .= "<b style=color:#cc0000>An error occurred while removing template assertion</b>";
            }
        }
    }
}

  /* action: delete the page */
  if ($action_delete) {
    if (!file_exists($filename . '.txt')) {
      $debuginfo .= "<b style=color:#cc0000>Error: File does not exist</b>";
    } else {
      if (unlink($filename . '.txt') && (!file_exists($filename . '.txt_') || unlink($filename . '.txt_'))) {
        $debuginfo .= "<b style=color:#ffff>File \"$filename\" deleted</b>";
        $filename = ''; /* unset filename */
        $template = ''; /* unset template */
      } else {
        $debuginfo .= "<b style=color:#cc0000>Error deleting file</b>";
      }
    }
  }
  /* option-tag-list of files in selected directory */
  if ($dh = opendir($directory)) {
    while (($file = readdir($dh)) !== false) {
      if (str_ends_with($file, '.txt')) {
        $current_file = "{$directory}{$file}";
        if (is_file($current_file)) {
          $file = preg_replace('/(.*)\.txt$/i', '$1', $file);
          $filelist .= "<option" . ($directory . $file == $filename ? " selected" : "") . " value=\"$directory$file\">$file</option>";

        }
      }
    }
  }
}
/* option-tag-list of available templates, the curre */
function get_templates($selected) {
  $return = '';
  if ($dh = opendir('./templates')) {
    while (false !== ($dir = readdir($dh))) {
      $subdir = './templates/' . $dir;
      if ($dir != '.' && $dir != '..' && is_dir($subdir) && file_exists("$subdir/index.html")) {
        $return .= "<option" . ($dir == $selected? " selected" : "") . ">$dir</option>";
      }
    }
  }
  return $return;
}
/* option-tag-list of subdirectories (called recursively); writing directly to output */
function list_subdirectories($path, $current) {
  if (is_dir($path)) {
    $dh = opendir($path);
    while (false !== ($dir = readdir($dh))) {
      if (is_dir($path . $dir) && $dir !== '.' && $dir !== '..' && ($path . $dir) !== './templates') {
        $subdir = $path . $dir . '/';
        echo "<option" . ($subdir == $current ? " selected" : "") . ">$subdir</option>";
        list_subdirectories($subdir, $current);
      }
    }
    closedir($dh);
  }
}
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html>
  <head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <meta http-equiv='Content-Language' content='EN'>
    <style type="text/css">
      .form-style-2 { font: 13px Arial, Helvetica, sans-serif; }
      .form-style-2-heading { font-weight: bold; padding-bottom: 3px; }
      .form-style-2 label>span { margin-left: 20px; padding-right: 5px; }
      .form-style-2 input[type=submit], .form-style-2 input[type=button] {border: none; padding: 1px 15px 1px 15px; margin: 1px 5px 1px 5px;
        background: #08f; color: #fff; box-shadow: 1px 1px 4px #aaa; -moz-box-shadow: 1px 1px 4px #aaa; -webkit-box-shadow: 1px 1px 4px #aaa;
        border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px; min-width: 75px;
      }
      .form-style-2 input[type=submit]:hover, .form-style-2 input[type=button]:hover { background: #048; color: #fff; }
      .form-style-2 input[type='text'], .form-style-2 select, .form-style-2 input[type='password'] { width: 150px; }
      .form-style-2 input[type=submit].red { background: #d00; }
      .form-style-2 input[type=submit].red:hover { background: #a00; }
      #loginform { margin: 0px 0px 10px 0px; border: 1px solid #008; padding: 20px; background: #fff;
        border-radius: 10px; -webkit-border-radius: 10px; -moz-border-radius: 10px;
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;
	    }
      #loginform p { text-align: right; }
      #debuginfo { color: #fff; }
      #debuginfo:hover { color: #888; }
    </style>
  </head>
  <body>
  <div class="form-style-2">
    <center>
      <div class="form-style-2-heading">HamsterCMS</div>
        <form action="" method="post">
          <div id="debuginfo"><?= $debuginfo ?>&nbsp;</div>
          <?php if (!$loggedin) { /* not logged in */ ?>
            <div id="loginform">
              <p>
                <label for="username"><span>Login:</span></label>
                <input id="username" name="username" type="text" placeholder="Enter login">
              </p>
              <p>
                <label for="password"><span>Password:</span></label>
                <input id="password" name="password" type="password" placeholder="Enter Password">
              </p>
                <input id="login" type="submit" value="Login"></td>
              </p>
            </div>
          <?php } else { /* logged in */ ?>
            <?php if (!empty($filelist)) { ?>
              <label for="filename"><span>Page:</span>
                <select id="filename" name="filename" onchange="return document.getElementById('action_edit').click();"><option value=""></option>
                <?= $filelist ?>
                </select>
              </label>
              <input type="submit" id="action_edit" name="action_edit" value="Edit"/>
            <?php } ?>
            <label for="directory"><span>Directory:</span>
              <select id="directory" name="directory" onchange="return document.getElementById('action_changedir').click();"><option value=""></option>
                <option>./</option><?php list_subdirectories("./", $directory); ?>
              </select>
            </label>
            <input type="submit" id="action_changedir" name="action_changedir" value="Change"/>
            <?php        
            /* only have the editor loaded if there is a valid file selected */
            if (!empty($filename) && file_exists("$filename.txt")) { /* if file selectd */
              $filecontent = file_get_contents("$filename.txt");
              $template = (file_exists("$filename.txt_") ? file_get_contents("$filename.txt_") : '');
            ?>
              <textarea name="content" cols="150" rows="40"><?= htmlspecialchars($filecontent) ?></textarea>
<p>
  <label for="newfilename"><span>Save as:</span></label>
  <input type="text" id="newfilename" name="newfilename" value="<?= basename(htmlspecialchars($filename)) ?>">
  <label for="template"><span>Template:</span></label>
  <select id="template" name="template">
    <option value=""></option>
    <?= get_templates($template) ?>
  </select>
  <input type="submit" name="action_save" value="Save" />
</p>


              <p><input class="red" type="submit" name="action_delete" value="Delete" onClick="return confirm('Do you really want to delete this page?');" /></p>
              <script src="//js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
              <script type="text/javascript">
                bkLib.onDomLoaded(function() {
                  nicEditors.allTextAreas({
                    buttonList: ["bold", "italic", "underline", "left", "center", "right", "justify", "ol", "ul", "fontFormat", 'fontSize', 'fontFamily', "indent", "outdent", "image", "link", "unlink", "xhtml", "table", 'upload', 'forecolor', 'bgcolor']
                  })
                });
              </script>
            <?php } /* end if file selected */ ?>
            <p><input type="submit" name="logout" value="Logout"></p>
          <?php } /* end logged in */ ?>
        </form>
      </center>
    </div>
  </body>
</html>