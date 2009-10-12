<?php

# Auteur  : Mathieu Onfray
# Site    : http://www.spip-contrib.net/Tester-les-URL-des-sites
# Licence : GPL

# Modification effectues : 
# - support caractere html speciaux pour messages utilisateurs
# - ajout d'un timeout a fsockopen


function check_connect2($url, $timeout) {

  /* Decoupage de l'url */
  $url_parsee = @parse_url($url);
  $host = trim($url_parsee["host"]);
  $path = trim($url_parsee["path"]);

  /* Ping de l'url */
  $fp = @fsockopen($host.$path, 80, $errno, $errstr, $timeout);
  echo $fp;
//  echo $errno;
//  echo $errstr;
  return $fp;
}

function check_connect($url)
//verifie la validite de l'adresse, c'est a dire on regarde si le site existe bien...
//on rend dans un tableau :
// "statut" : 0 si KO, 1 si redirect ou bien pour faire passer en local, 2 si OK
// "code" : code HTTP
// "message" : message
{
	$url_parsee = @parse_url($url);
	$host = trim($url_parsee["host"]);
	$path = trim($url_parsee["path"]);
	$connect = 0;
	$no_code = 0;
	//connexion par socket
	# Modif sbilbeau
	#if ($fp = @fsockopen($host,80))
	if ($fp = @fsockopen($host, 80, $errno, $errstr, 6))
	{
		//traitement du path
		if(substr($path,strlen($path)-1) != '/')
		{
			if(!ereg("\.",$path)) $path .= "/";
		}
		//envoi de la requete HTTP
		fputs($fp,"GET ".$path." HTTP/1.1\r\n"); 
		fputs($fp,"Host: ".$host."\r\n");
		fputs($fp,"Connection: close\r\n\r\n");
		//on lit le fichier
		$line = fread($fp,255);
		$en_tete = $line;
		//on lit tant qu'on n'est pas la fin du fichier ou qu'on trouve le debut du code html...
		while (!feof($fp) && !ereg("<",$line) )
		{
			$en_tete .= $line;
			$line = fread($fp,255);
		}
		fclose($fp);
		//on switch sur le code HTTP renvoye
		$no_code = substr($en_tete,9,3);
		switch ($no_code)
		{
			// 2** la page a ete trouvee
			case 200 :		
						$message = "OK";
						$color = "#33cc00";
						$connect = 2;
						break;
			case 204 :	
						$message = "Cette page ne contient rien !";
						$color = "#ff9966";
						break;
			case 206 :	
						$message = "Contenu partiel de la page !";
						$color = "#ff9966";
						break;
			// 3** il y a une redirection
			case 301 :	
						$message = "La page a &eacute;t&eacute; d&eacute;plac&eacute;e d&eacute;finitivement"; 
						$message .= seek_redirect_location($en_tete);
						$color = "#ff9966";
						$connect = 1;
						break;
			case 302 :	
						$message = "La page a &eacute;&eacute;&eacute;plac&eacute;e momentan&eacute;ment"; 
						$message .= seek_redirect_location($en_tete);
						$color = "#ff9966";
						$connect = 1;
						break;
			// 4** erreur du cote du client
			case 400 :	
						$message = "Erreur dans la requête HTTP !";
						$color = "#ff0000";
						break;
			case 401 :	
						$message = "Authentification requise !";
						$color = "#ff0000";
						break;
			case 402 :	
						$message = "L'acc&egrave;s à la page est payant !";
						$color = "#ff0000";
						break;
			case 403 :	
						$message = "Accès à la page ref&eacute; !";
						$color = "#ff0000";
						break;
			case 404 :	
						$message = "Page inexistante !";
						$color = "#ff0000";
						break;
			// 5** erreur du cot&eacute; du serveur
			case 500 :	
						$message = "Erreur interne au serveur !";
						$color = "#ff0000";
						$connect = 1;
						break;
			case 502 :	
						$message = "Erreur à cause de la passerelle du serveur !";
						$color = "#ff0000";
						break;
			// cas restant
			default :	
						$message = "Erreur non trait&eacute;e dont le num&eacute;ro est : $no_code!";
						$color = "#000000";
						break;
		}
	}
	else
	{
		$message = "Impossible de se connecter";
		$color = "#ff0000";
	}
	//creation du tableau avec les valeurs a rendre
	$data_return["statut"] = $connect; //la page est OK ou KO (200 => OK sinon KO)
	$data_return["code"] = $no_code; //code HTTP renvoye
	$data_return["message"] = "<font color=\"".$color."\">".$message."</font>\n"; //message a afficher
	return $data_return;
}

function seek_redirect_location($header)
//recherche la location de la redirection si l'erreur HTTP renvoyee commence par 3
{
	$location = "";
	$tab_header = explode("\n",$header);
	for ($i=0;$tab_header[$i];$i++)
	{
		$line = split(":",$tab_header[$i],2);
		if(eregi("location",$line[0]))
		{
			$location = trim($line[1]);
			break;
		}
	}
	if ($location) return " <a target=_blank class=spip_out href=\"$location\">ici</a>";
}
?>
