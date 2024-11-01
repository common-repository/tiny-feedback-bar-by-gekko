<?php

//Op welk domein installeer je jouw Tiny Feedback Bar
define('DOMAIN', 'http://feedback.deltadeveloper.nl'); 				//bijvoorbeeld 'subdomein.gekko.nl', dus zonder 'http' en '/'

//Voorzie de Tiny Feedback Bar van je eigen logo
//Plaats je logo in de map /skin/images
define('COMPANY_LOGO_BAR', 'logo-3pix.jpg');				//max. 135 x 39 px
define('COMPANY_WEBSITE', 'http://www.3pix.nl');			//bijv: 'http://www.uwdomein.nl'
define('COMPANY_LOGO_POPUP', 'logo-popup-3pix.gif'); 		//max. 150 x 83 px

//In geval van storingen zal de Feedback Bar uw 
//bedrijfsnaam + telefoonnummer afbeelden
define('YOUR_COMPANY', '3pix');
define('YOUR_TELEPHONE', '040-201 12 12');

//To Do statusses
$delta_config['to_do']['status'][] = array('id' => '50', 'text' => '1. New');
$delta_config['to_do']['status'][] = array('id' => '100', 'text' => '2. Feedback');
$delta_config['to_do']['status'][] = array('id' => '150', 'text' => '3. Re-opened');
$delta_config['to_do']['status'][] = array('id' => '300', 'text' => '4. Unable to solve');
$delta_config['to_do']['status'][] = array('id' => '500', 'text' => '5. Closed / solved');

//To Do categories
$delta_config['to_do']['cat'][] = array('id' => '100', 'text' => 'Phone support');
$delta_config['to_do']['cat'][] = array('id' => '200', 'text' => 'E-mail support');
$delta_config['to_do']['cat'][] = array('id' => '300', 'text' => 'Failure');
$delta_config['to_do']['cat'][] = array('id' => '500', 'text' => 'Request for maintenance');
$delta_config['to_do']['cat'][] = array('id' => '600', 'text' => 'Other');

//To Do priorities
$delta_config['to_do']['priority'][] = array('id' => '5', 'text' => 'Urgent', 'icon' => 'p1.gif', 'text' => 'Urgent priority', 'color' => 'FF0000');
$delta_config['to_do']['priority'][] = array('id' => '2', 'text' => 'High', 'icon' => 'p2.gif', 'text' => 'High priority', 'color' => 'FF0000');
$delta_config['to_do']['priority'][] = array('id' => '1', 'text' => 'Normal', 'icon' => 'p3.gif', 'text' => 'Normal priority', 'color' => '000000');
$delta_config['to_do']['priority'][] = array('id' => '4', 'text' => 'Low', 'icon' => 'p4.gif', 'text' => 'Low priority', 'color' => '999999');

//To Do severity / impact
$delta_config['to_do']['severity'][] = array('id' => '100', 'text' => 'Content');
$delta_config['to_do']['severity'][] = array('id' => '200', 'text' => 'Tweak');
$delta_config['to_do']['severity'][] = array('id' => '300', 'text' => 'Small');
$delta_config['to_do']['severity'][] = array('id' => '400', 'text' => 'Big');
$delta_config['to_do']['severity'][] = array('id' => '500', 'text' => 'Crash');

//To Do reproducibility
$delta_config['to_do']['reproducibility'][] = array('id' => '100', 'text' => '-');
$delta_config['to_do']['reproducibility'][] = array('id' => '200', 'text' => 'Not reproducable');
$delta_config['to_do']['reproducibility'][] = array('id' => '300', 'text' => 'Haven\'t tried');
$delta_config['to_do']['reproducibility'][] = array('id' => '400', 'text' => 'Random');
$delta_config['to_do']['reproducibility'][] = array('id' => '500', 'text' => 'Sometimes');
$delta_config['to_do']['reproducibility'][] = array('id' => '600', 'text' => 'Always');

?>