<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="Content-Language" content="en" />
  <meta name="MSSmartTagsPreventParsing" content="TRUE" />
  <meta name="ROBOTS" content="NOARCHIVE,NOINDEX,NOFOLLOW" />
  <meta name="GOOGLEBOT" content="NOSNIPPET" />
  <link rel="icon" type="image/ico" href="../favicon.png" />
  <title>Bilboplanet - Error</title>
  <style media="screen" type="text/css">
body {
margin: 20px auto;
font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
background-color:#E7E7E7;
color:#3a3a3a;
font-size:12px;
 }
a, a:link, a:visited {
color : #3a3a3a;
text-decoration : none;
}
h1 {
color: #FFF;
font-size: 60px;
font-weight: normal;
}
h2 {
border-bottom: 1px solid #3a3a3a;
font-size:16px;
font-weight: bold;
text-shadow: rgba(119, 119, 119, 0.3) 2px 2px 2px;
}
h3 {
font-size:14px;
font-weight: normal;
}
#header {
background:url('meta/images/layout.png') repeat-x;
width:700px;
height:150px;
margin: 0 auto;
}
#header_ext {
background:url('meta/images/layout.png') repeat-x;
margin: 0 auto;
}
#logo {
background-image:url('meta/images/logo.png');
background-repeat: no-repeat;
height:128px;
padding-left:120px;
padding-top:20px;
}
#content {
-moz-box-shadow:0 4px 18px #999999;
-webkit-box-shadow:0 4px 18px #999999;
box-shadow:0 4px 18px #999999;
background:none repeat scroll 0 0 #FDFDFD;
border-left:1px solid #AFAFAF;
border-right:1px solid #AFAFAF;
border-top:1px solid #AFAFAF;
margin:17px auto;
padding:10px 20px 60px 20px;
width:700px;
-moz-border-radius: 3px;
-khtml-border-radius: 3px;
-webkit-border-radius: 3px;
-o-border-radius: 3px;
text-align: justify;
}
.next {
border:1px solid #A1A1A1;
background-color:#E1E1E1 ;
-moz-border-radius:6px ;
-webkit-border-radius:6px ;
-khtml-border-radius:6px ;
-o-border-radius:6px ;
padding:3px 10px 4px 12px;
width:50px;
float:right;
margin-right:10px;
margin-top:10px;
}
a:hover, .next a:focus {
text-shadow: rgba(119, 119, 119, 0.8) 2px 2px 2px;
}
</style>
</head>

<body>
<div id="header_ext">
<div id="header">
<div id="logo">

<h1>Bilboplanet</h1>

</div>
</div>
</div>
<div id="content">
<h2><?php echo $summary; ?></h2>
<?php echo $message; ?>
<br />
<br />
<?php echo $message2; ?>

</div>
</body>
</html>
