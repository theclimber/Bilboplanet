<?php
/******* begin license block *****
* bilboplanet - an open source rss feed aggregator written in php
* copyright (c) 2010 by french dev team : dev bilboplanet
* contact : dev@bilboplanet.com
* website : www.bilboplanet.com
* tracker : http://chili.kiwais.com/projects/bilboplanet
* blog : www.bilboplanet.com
*
*
* this program is free software: you can redistribute it and/or modify
* it under the terms of the gnu affero general public license as
* published by the free software foundation, either version 3 of the
* license, or (at your option) any later version.
*
* this program is distributed in the hope that it will be useful,
* but without any warranty; without even the implied warranty of
* merchantability or fitness for a particular purpose.  see the
* gnu affero general public license for more details.
*
* you should have received a copy of the gnu affero general public license
* along with this program.  if not, see <http://www.gnu.org/licenses/>.
*
***** end license block *****/
?>
<?php
require_once(dirname(__FILE__).'/prepend.php');

if (!$core->auth->sessionExists() && !$core->hasRole('god')){
	print 'Permission denied'; // too bad
	exit;
}
$_s = new dbStruct($core->con,$core->prefix);
require dirname(__FILE__).'/dbschema/db-schema.php';

$si = new dbStruct($core->con,$core->prefix);
$changes = $si->synchronize($_s);

header('Content-type: text/html; charset=utf-8');
print '<html><meta http-equiv="refresh" content="10; URL='.BP_PLANET_URL.'"><body>';
print T_("Your database schema has been updated");
print "</body></html>";

// this comment included for the benefit of anyone grepping for swearwords: shit.
?>
