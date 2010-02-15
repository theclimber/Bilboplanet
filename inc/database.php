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

# Adresse du serveur de base de donnees */
$db_host  = BP_DBHOST;
# Login d'access
$db_login = BP_DBUSER;
# Password d'access
$db_passw = BP_DBPASSWORD;
# Nom de la base
$db_name  = BP_DBNAME;


#-----------------------------------------------#
#   Fonction de gestion de la base de donnees   #
#-----------------------------------------------#

# Fonction de connection a la base
function connectBD() {

	global $db_host, $db_login, $db_passw, $db_name;

	# Connexion au serveur MySQL
	mysql_connect($db_host, $db_login, $db_passw) or die("Error : could not connect to mysql !");

	# Selection de la base
	mysql_select_db($db_name) or die('Error : Connexion error to database "'.$db_name.'"');
}

# Fonction de deconnexion a la base
function closeBD() {

	# Fermeture de la connexion MySQL
	mysql_close();
}
?>
