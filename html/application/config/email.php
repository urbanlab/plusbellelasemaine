<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/*$config['protocol'] = 'mail';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";*/

// get env vars from os
$smtp_host = getenv('SMTP_HOST');
$smtp_port = getenv('SMTP_PORT');
$smtp_user = getenv('SMTP_USER');
$smtp_pass = getenv('SMTP_PASS');

$config['protocol'] = 'smtp';
$config['smtp_host'] = $smtp_host;
$config['smtp_port'] = $smtp_port;
$config['smtp_user'] = $smtp_user;
$config['smtp_pass'] = $smtp_pass;
$config['charset'] = 'utf-8';
$config['mailtype'] = 'html';
$config['newline'] = "\r\n";
$config['crlf'] = "\n";
