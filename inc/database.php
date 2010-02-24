<?php
/******* BEGIN LICENSE BLOCK *****
* BilboPlanet - Un agrégateur de Flux RSS Open Source en PHP.
* BilboPlanet - An Open Source RSS feed aggregator written in PHP
* Copyright (C) 2009 By French Dev Team : Dev BilboPlanet
* Contact : dev@bilboplanet.com
* Website : www.bilboplanet.com
* Tracker : redmine.bilboplanet.com
* Blog : blog.bilboplanet.com
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
# Inclusion de la configuration du planet
require_once(dirname(__FILE__).'/config.php');

#-----------------------------------------------#
#   Fonction de gestion de la base de donnees   #
#-----------------------------------------------#

# Fonction de connection a la base
function connectBD() {
	if (BP_DBENCRYPTED_PASSWORD) {
		mysql_connect(BP_DBHOST, BP_DBUSER, base64_decode(BP_DBPASSWORD)) or die("Error : could not connect to mysql : ".mysql_error());
	} else {
		mysql_connect(BP_DBHOST, BP_DBUSER, BP_DBPASSWORD) or die("Error : could not connect to mysql : ".mysql_error());
	}

	mysql_select_db(BP_DBNAME) or die('Error : Connexion error to database "'.BP_DBNAME.'" : '.mysql_error());
}

function closeBD() {
	mysql_close();
}
?>
