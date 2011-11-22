<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2010 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : http://chili.kiwais.com/projects/bilboplanet
* Blog : www.bilboplanet.com
* 
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
***** END LICENSE BLOCK *****/
?>
<?php
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
		<link rel="icon" type="image/ico" href="http://www.bilboplanet.com/logo/favicon.png" />
		<title>Bilboplanet - Error</title>
		<style media="screen" type="text/css">
			body {
				margin: 0px auto;
				font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
				background-color: #E7E7E7;
				color: #3a3a3a;
				font-size: 12px;
			}

			a, a:link, a:visited {
				color : #3a3a3a;
				text-decoration : none;
			}

			a:hover, .next a:focus {
				text-shadow: rgba(119, 119, 119, 0.8) 2px 2px 2px;
			}

			h1 {
				color: #FFFFFF;
				font-size: 60px;
				font-weight: normal;
			}

			h2 {
				border-bottom: 1px solid #3a3a3a;
				font-size: 16px;
				font-weight: bold;
				text-shadow: rgba(119, 119, 119, 0.3) 2px 2px 2px;
			}

			h3 {
				font-size: 14px;
				font-weight: normal;
			}

			#header {
				filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#22384f', endColorstr='#2e65a1'); /* for IE */
				background: -webkit-gradient(linear, left top, left bottom, from(#22384f), to(#2e65a1)); /* for Safari */
				background: -moz-linear-gradient(top, #22384f, #2e65a1); /* for Firefox */
				width: 700px;
				height: 150px;
				margin: 0 auto;
			}

			#header_ext {
				filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#22384f', endColorstr='#2e65a1'); /* for IE */
				background: -webkit-gradient(linear, left top, left bottom, from(#22384f), to(#2e65a1)); /* for Safari */
				background: -moz-linear-gradient(top, #22384f, #2e65a1); /* for Firefox */
				background: -moz-linear-gradient(top, #22384f, #2e65a1);
				margin: 0 auto;
			}

			#logo {
				background: url('http://www.bilboplanet.com/logo/logo.png') no-repeat;
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
				float:right;
				margin-right:10px;
				margin-top:10px;
			}
			.next:after {
			content: " →";
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
			<p>
				<?php echo $message; ?>
			</p>
			<p>
				<?php echo $message2; ?>
			</p>
		</div>
	</body>
</html>
