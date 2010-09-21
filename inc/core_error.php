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
  <link rel="stylesheet" type="text/css" href="meta/css/install.css" media="all" />
  <title>Bilboplanet - Error</title>
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
