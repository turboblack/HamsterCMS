<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Win95 Cool Page<?php
        if(isset($_GET['p'])){
            $_GET['p'] = $_GET['p'];
        } else {
            $_GET['p'] = 'index.txt';
        }
        echo preg_replace('#([^/][а-Я]+)\.txt$#', '$1', $_GET['p']);
        ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF8">
<style type="text/css">
.ws12 {font-size: 16px;}
.wpmd {font-size: 16px;font-family: Arial,Helvetica,Sans-Serif;font-style: normal;font-weight: normal;}
DIV,UL,OL /* Left */
{
 margin-top: 0px;
 margin-bottom: 0px;
}
 a {
    text-decoration: none;
  }
</style>

</head>
<body bgColor="#000000">
<div id="image2" style="position:absolute; overflow:hidden; left:50px; top:30px; width:48px; height:48px; z-index:0"><a href="index.php?p=index.txt" title="Index"><img src="images/image002.gif" alt="" title="" border=0 width=48 height=48></a></div>

<div id="text1" style="position:absolute; overflow:hidden; left:25px; top:80px; width:104px; height:26px; z-index:1">
<a href="index.php?p=index.txt" title="Index"><div class="wpmd">
<div align=center><font color="#FFFFFF" face="MS Sans Serif">My Computer</font></div>
</div></a></div>

<div id="image1" style="position:absolute; overflow:hidden; left:50px; top:150px; width:48px; height:48px; z-index:2"><a href="index.php?p=index.txt" title="Index"><img src="images/image001.gif" alt="" title="" border=0 width=48 height=48></a></div>

<div id="image3" style="position:absolute; overflow:hidden; left:50px; top:270px; width:48px; height:48px; z-index:3"><a href="index.php?p=index.txt" title="Index"><img src="images/image004.gif" alt="" title="" border=0 width=48 height=48></a></div>

<div id="image4" style="position:absolute; overflow:hidden; left:964px; top:520px; width:48px; height:48px; z-index:4"><a href="mailto:yourmail@mail.com" title="e-mail"><img src="images/image007.gif" alt="" title="" border=0 width=48 height=48></a></div>

<div id="image5" style="position:absolute; overflow:hidden; left:50px; top:390px; width:48px; height:48px; z-index:5"><a href="index.php?p=index.txt" title="Index"><img src="images/image008.gif" alt="" title="" border=0 width=48 height=48></a></div>

<div id="image7" style="position:absolute; overflow:hidden; left:150px; top:55px; width:770px; height:540px; z-index:6"><img src="images/Untitled-2.gif" alt="" title="" border=0 width=770 height=540></div>

<div id="text2" style="position:absolute; overflow:hidden; left:25px; top:200px; width:98px; height:23px; z-index:7">
<a href="index.php?p=index.txt" title="Index"><div class="wpmd">
<div align=center><font color="#FFFFFF" face="MS Sans Serif">Notepad</font></div>
</div></a></div>

<div id="text3" style="position:relative;  overflow:auto;  scrollbar-color: #c0c0c0 transparent; left:160px; top:95px; width:745px; height:465px; z-index:8">
<div class="ws12">
    <?php
    $nothere = array("/", "\\");
    $_GET['p'] = str_replace($nothere, "", $_GET['p']);
    if(file_exists($_GET['p'])){
        echo(file_get_contents($_GET['p']));
    } else {
        echo("ERROR 404 - Page Not Found");
        header("Status: 404 Not Found");
    }
    ?>
</div></div>

<div id="text4" style="position:absolute; overflow:hidden; left:25px; top:320px; width:98px; height:23px; z-index:9">
<a href="index.php?p=index.txt" title="Index"><div class="wpmd">
<div align=center><font color="#FFFFFF" face="MS Sans Serif">Internet</font></div>
</div></a></div>

<div id="text5" style="position:absolute; overflow:hidden; left:25px; top:440px; width:98px; height:23px; z-index:10">
<a href="index.php?p=index.txt" title="Index"><div class="wpmd">
<div align=center><font color="#FFFFFF" face="MS Sans Serif">My Files</font></div>
</div></a></div>

<div id="text6" style="position:absolute; overflow:hidden; left:939px; top:570px; width:98px; height:23px; z-index:11">
<a href="mailto:yourmail@mail.com" title="e-mail"><div class="wpmd">
<div align=center><font color="#FFFFFF" face="MS Sans Serif">E-mail</font></div>
</div></a></div>

<div id="image6" style="position:absolute; overflow:hidden; left:50px; top:510px; width:48px; height:40px; z-index:12"><a href="index.html" title="Index"><img src="images/bash.png" alt="" title="" border=0 width=48 height=40></a></div>

<div id="text7" style="position:absolute; overflow:hidden; left:25px; top:560px; width:98px; height:23px; z-index:13">
<a href="index.php?p=index.txt" title="Index"><div class="wpmd">
<div align=center><font color="#FFFFFF" face="MS Sans Serif">MS-DOS</font></div>
</div></a></div>


</body>
</html>
