<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/*$config['protocol'] = 'mail';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";*/


$config['protocol'] = 'smtp';
$config['smtp_host'] = 'in-v3.mailjet.com';
$config['smtp_port'] = '25';
$config['smtp_user'] = '9b1dfa658af96b19bec21d1861c6c603';
$config['smtp_pass'] = '4cfb777dae1a1cf33a8b58066f982268';
$config['charset'] = 'utf-8';
$config['mailtype'] = 'html';
$config['newline'] = "\r\n";
$config['crlf'] = "\n";
