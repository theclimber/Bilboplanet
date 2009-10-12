<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Clearbricks.
# Copyright (c) 2006 Olivier Meunier and contributors.
# All rights reserved.
#
# Clearbricks is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Clearbricks is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Clearbricks; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
/**
@defgroup CLEARBRICKS Clearbricks classes
*/

define('CLEARBRICKS_VERSION','0.8');

# Autoload
$__autoload = array(
	'crypt'			=> dirname(__FILE__).'/lib.crypt.php',
	'dt'				=> dirname(__FILE__).'/lib.date.php',
	'files'			=> dirname(__FILE__).'/lib.files.php',
	'path'			=> dirname(__FILE__).'/lib.files.php',
	'form'			=> dirname(__FILE__).'/lib.form.php',
	'formSelectOption'	=> dirname(__FILE__).'/lib.form.php',
	'html'			=> dirname(__FILE__).'/lib.html.php',
	'http'			=> dirname(__FILE__).'/lib.http.php',
	'text'			=> dirname(__FILE__).'/lib.text.php'
);

# autoload for clearbricks
function cb_autoload($name)
{
	global $__autoload;
	
	if (isset($__autoload[$name])) {
		require_once $__autoload[$name];
	}
}

# if php version >= 5.1.2, we can benefit from spl_autoload_register, 
# so other libraries can define their own independent autoload too
if (function_exists("spl_autoload_register")) {
	spl_autoload_register("cb_autoload");
} else {
	# otherwise we define a classic autoload function for older php...
	function __autoload($name) {
		cb_autoload($name);
	}
}

# We only need l10n __() function
#require_once dirname(__FILE__).'/lib.l10n.php';

# We set default timezone to avoid warning
dt::setTZ('UTC');
?>
