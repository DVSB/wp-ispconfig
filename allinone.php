<?php 
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit; 
//print_r($cfg);
//print_r($clientdata);

function string2url($cadena) {
	$cadena = trim($cadena);
	$cadena = strtr($cadena,
							"ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
							"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
	//$cadena = strtr($cadena,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz");
	$cadena = preg_replace('#([^.a-z0-9]+)#i', '_', $cadena);
    $cadena = preg_replace('#-{2,}#','_',$cadena);
    $cadena = preg_replace('#-$#','',$cadena);
    $cadena = preg_replace('#^-#','',$cadena);
	$cadena = str_replace(".","_",$cadena);
	return $cadena;
}

$soapusername = $cfg['soapusername'];
$soappassword = $cfg['soappassword'];
$soap_location = $cfg['soap_location'];
$soap_uri = $cfg['soap_uri'];

// VARIABLES
$template_id = $clientdata['template_id'];
$domain = $clientdata['domain'];
$cliente = $clientdata['cliente'];
$empresa = $clientdata['empresa'];
$username = string2url($clientdata['username']);
$password = $clientdata['password'];
$ip = $clientdata['ip'];
$ns1 = $clientdata['ns1'];
$ns2 = $clientdata['ns2'];
$email = $clientdata['email'];


$client = new SoapClient(null, array('location' => $soap_location,
									 'uri'      => $soap_uri,
									 'trace' => 1,
									 'exceptions' => 1));

	// A ver si logea
	try {
		if($session_id = $client->login($soapusername,$soappassword)) {
			echo 'Logged successfull. Session ID:'.$session_id.'<br />';
		}


	// Nuevo Cliente
		//* Set the function parameters.
		$random_rs_id = 1;
		$params = array(
				'company_name' => $empresa,
				'contact_name' => $cliente,
				'customer_no' => '',
				'vat_id' => '1',
				'street' => '',
				'zip' => '',
				'city' => '',
				'state' => '',
				'country' => 'AR',
				'telephone' => '',
				'mobile' => '',
				'fax' => '',
				'email' => $email,
				'internet' => '',
				'icq' => '',
				'notes' => '',
				'default_mailserver' => 1,
				'limit_maildomain' => -1,
				'limit_mailbox' => -1,
				'limit_mailalias' => -1,
				'limit_mailaliasdomain' => -1,
				'limit_mailforward' => -1,
				'limit_mailcatchall' => -1,
				'limit_mailrouting' => 0,
				'limit_mailfilter' => -1,
				'limit_fetchmail' => -1,
				'limit_mailquota' => -1,
				'limit_spamfilter_wblist' => 0,
				'limit_spamfilter_user' => 0,
				'limit_spamfilter_policy' => 1,
				'default_webserver' => 1,
				'limit_web_ip' => '',
				'limit_web_domain' => -1,
				'limit_web_quota' => -1,
				'web_php_options' => 'no,fast-cgi,cgi,mod,suphp',
				'limit_web_subdomain' => -1,
				'limit_web_aliasdomain' => -1,
				'limit_ftp_user' => -1,
				'limit_shell_user' => 0,
				'ssh_chroot' => 'no,jailkit,ssh-chroot',
				'limit_webdav_user' => 0,
				'default_dnsserver' => 1,
				'limit_dns_zone' => -1,
				'limit_dns_slave_zone' => -1,
				'limit_dns_record' => -1,
				'default_dbserver' => 1,
				'limit_database' => -1,
				'limit_cron' => 0,
				'limit_cron_type' => 'url',
				'limit_cron_frequency' => 5,
				'limit_traffic_quota' => -1,
				'limit_client' => 0,
				'parent_client_id' => 0,
				'username' => $username,
				'password' => $password,
				'language' => 'es',
				'usertheme' => 'default',
				'template_master' => 0,
				'template_additional' => '',
				'created_at' => 0
				);
		
		$client_id = $client->client_add($session_id, $random_rs_id, $params);
		
		echo "Client: <br>Client ID:".$client_id."<br>";
		echo "Username: ".$username."<br>";
		echo "password: ".$password."<br>";
		echo "company_name: ".$empresa."<br>";
		echo "contact_name: ".$cliente."<br><br>";
		echo "email: ".$email."<br><br>";

	// Fin Nuevo Cliente

	// NUEVA ZONA y Registros DNS
		$dnsemail = str_replace('@','.',$email);
		$dnszone = $client->dns_templatezone_add($session_id, $client_id, $template_id, $domain, $ip, $ns1, $ns2, $dnsemail);
		
		echo "DNS Zones added from DNS template: ".$template_id."<br><br>";
		
		/* $plantilla="
	[ZONE]
	origin={DOMAIN}.
	ns={NS1}.
	mbox={EMAIL}.
	refresh=7200
	retry=540
	expire=604800
	minimum=86400
	ttl=3600

	[DNS_RECORDS]
	A|{DOMAIN}.|{IP}|0|3600
	A|www|{IP}|0|3600
	A|mail|{IP}|0|3600
	NS|{DOMAIN}.|{NS1}.|0|3600
	NS|{DOMAIN}.|{NS2}.|0|3600
	MX|{DOMAIN}.|mail.{DOMAIN}.|10|3600";
		*/
	// FIN NUEVA ZONA y Registros DNS

	// Creo el DOMINIO
		$params = array(
				'server_id'	=> '1',
				'domain' => $domain,
				'ip_address' => '*',
				'type' => 'vhost',
				'parent_domain_id' => 0,
				'vhost_type' => '',
				'hd_quota' => -1,
				'traffic_quota' => -1,
				'cgi' => 'n',
				'ssi' => 'n',
				'suexec' => 'n',
				'errordocs' => 1,
				'is_subdomainwww' => 1,
				'subdomain' => '',
				'php' => 'mod', 
				'ruby' => 'n', 
				'redirect_type' => '',
				'redirect_path' => '',
				'ssl' => 'n',
				'ssl_state' => '',
				'ssl_locality' => '',
				'ssl_organisation' => '',
				'ssl_organisation_unit' => '',
				'ssl_country' => '',
				'ssl_domain' => '',
				'ssl_request' => '',
				'ssl_cert' => '',
				'ssl_bundle' => '',
				'ssl_action' => '',
				'stats_password' => $password,
				'stats_type' => 'webalizer',
				'allow_override' => 'All',
				'apache_directives' => '',
				'php_open_basedir' => '/', 
				'custom_php_ini' => '', 
				'backup_interval' => '',
				'backup_copies' => 1,
				'active' => 'y',
				'traffic_quota_lock' => 'n',
				'pm_process_idle_timeout'=>10,
				'pm_max_requests'=>0
		);

		$domain_id = $client->sites_web_domain_add($session_id, $client_id, $params, $readonly = false);
		echo "Web Domain ID: ".$domain_id."<br>";
		echo "Domain: ".$domain."<br><br>";

		// fin Creo el DOMINIO
		// CREO FTP_USER	
		$params = array('server_id'			=> '1',
						'parent_domain_id'	=> $domain_id,
						'username'			=> $username,
						'password'			=> $password,
						'quota_size'		=> -1,
						'active'			=> 'y',
						'uid'				=> 'web'.$domain_id,
						'gid'				=> 'client'.$client_id,
						'dir'				=> '/var/www/clients/client'.$client_id.'/web'.$domain_id,
						'quota_files'		=> -1,
						'ul_ratio'			=> -1,
						'dl_ratio'			=> -1,
						'ul_bandwidth'		=> -1,
						'dl_bandwidth'		=> -1);

		$ftp_user_id = $client->sites_ftp_user_add($session_id, $client_id, $params);			

		echo "FTP User ID: ".$ftp_user_id."<br>";
		echo "FTP domain: ".$domain."<br>";
		echo "FTP User: ".$username."<br>";
		echo "FTP Pass: ".$password."<br>";
		echo "FTP dir: /var/www/clients/client$client_id/web$domain_id<br><br>";
		
		// FIN CREO FTP_USER	
		
		// CREO DATABASE USER	
		$params = array(
				'server_id' => 1,
				'database_user' => $username,
				'database_password' => $password
				);

		$database_user_id = $client->sites_database_user_add($session_id, $client_id, $params);

		echo "Database User ID: ".$database_user_id."<br>";
		echo "Database User: ".$username."<br>";
		echo "Database Pass: ".$password."<br>";
		echo "You must create Databases<br><br>";
		// FIN CREO DATABASE USER	

		// AGREGO EL NUEVO DOMINIO AL MAIL
		$params = array(
				'server_id' => 1,
				'domain' => $domain,
				'active' => 'y'
				);
		
		$domain_id = $client->mail_domain_add($session_id, $client_id, $params);

		echo "Mail Domain ID: ".$domain_id."<br><br>";
		// FIN AGREGO EL NUEVO DOMINIO AL MAIL
		// AGREGO NUEVO MAIL
			$params = array(
				'server_id' => 1,
				'email' => $username.'@'.$domain,
				'login' => $username.'@'.$domain,
				'password' => $password,
				'name' => $cliente,
				'uid' => 5000,
				'gid' => 5000,
				'maildir' => '/var/vmail/'.$domain.'/'.$username,
				'quota' => 524288000,
				'cc' => '',
				'homedir' => '/var/vmail',
				'autoresponder' => 'n',
				'autoresponder_start_date' => '',
				'autoresponder_end_date' => '',
				'autoresponder_text' => '',
				'move_junk' => 'n',
				'custom_mailfilter' => '',
				'postfix' => 'y',
				'access' => 'n',
				'disableimap' => 'n',
				'disablepop3' => 'n',
				'disabledeliver' => 'n',
				'disablesmtp' => 'n'
				);
		
		$email_id = $client->mail_user_add($session_id, $client_id, $params);

		echo "New email ID: ".$email_id."<br>";
		echo "New e-mail account: ".$username."@".$domain."<br>";
		echo "Password: ".$password."<br>";
		
		// FIN  AGREGO NUEVO MAIL

		if($client->logout($session_id)) {
			echo 'Logged out.<br />';
		}
		
		
	} catch (SoapFault $e) {
		echo $client->__getLastResponse();
		die('SOAP Error: '.$e->getMessage());
	}


?>