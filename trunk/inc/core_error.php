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
  <link rel="icon" type="image/ico" href="favicon.ico" />
  <title>Bilboplanet - Error</title>
  <style media="screen" type="text/css">
  <!--
  body {
	font: 62.5%/1.5em "DejaVu Sans","Lucida Grande","Lucida Sans Unicode",Arial,sans-serif;
	color : #000;
	background : #eceade;
	margin : 40px;
	padding : 0;
  }
  #content {
	  margin: 0 25%;
	  padding: 1px 1em 2em;
	  background: #fff;
	  font-size: 1.2em;
	  -moz-border-radius: 11px;
	  -khtml-border-radius: 11px;
  	  -webkit-border-radius: 11px;
	  border-radius: 11px;
  }
  a, a:link, a:visited {
	color : #06c;
	text-decoration : none;
	border-bottom : 1px dotted #f90;
  }
  h1 {
  	color: #06c;
  	font-size: 2.5em;
  	font-weight: normal;
  }
  
  h2 {
  	font-size: 1.5em;
  }
  -->
</style>
</head>

<body>
<div id="content">
<h1>Bilboplanet</h1>
<h2><?php echo $summary; ?></h2>
<?php echo $message; ?></div>
</body>
</html>
