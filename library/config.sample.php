<?php
$_xhprof = array();

// Change these:
$_xhprof['dbtype'] = 'mysql'; // Only relevant for PDO
$_xhprof['dbhost'] = 'localhost';
$_xhprof['dbuser'] = 'root';
$_xhprof['dbpass'] = 'password';
$_xhprof['dbname'] = 'xhprof';
$_xhprof['dbadapter'] = 'Pdo';
$_xhprof['servername'] = 'myserver';
$_xhprof['namespace'] = 'myapp';
$_xhprof['url'] = 'http://url/to/xhprof/xhprof_html';
/*
 * MySQL/MySQLi/PDO ONLY
 * Switch to JSON for better performance and support for larger profiler data sets.
 * WARNING: Will break with existing profile data, you will need to TRUNCATE the profile data table.
 */
$_xhprof['serializer'] = 'php'; 

//Uncomment one of these, platform dependent. You may need to tune for your specific environment, but they're worth a try

//These are good for Windows
/*
$_xhprof['dot_binary']  = 'C:\\Programme\\Graphviz\\bin\\dot.exe';
$_xhprof['dot_tempdir'] = 'C:\\WINDOWS\\Temp';
$_xhprof['dot_errfile'] = 'C:\\WINDOWS\\Temp\\xh_dot.err';
*/

//These are good for linux and its derivatives.
/*
$_xhprof['dot_binary']  = '/usr/bin/dot';
$_xhprof['dot_tempdir'] = '/tmp';
$_xhprof['dot_errfile'] = '/tmp/xh_dot.err';
*/

$_xhprof['ignoreURLs'] = array();

$_xhprof['ignoreDomains'] = array();

$_xhprof['exceptionURLs'] = array();

$_xhprof['exceptionPostURLs'] = array();
$_xhprof['exceptionPostURLs'][] = "login";

$_xhprof['display'] = false;
$_xhprof['doprofile'] = false;

//Control IPs allow you to specify which IPs will be permitted to control when profiling is on or off within your application, and view the results via the UI.
// $controlIPs = false; //Disables access controlls completely. 
$_xhprof['controlIPs'] = array();
$_xhprof['controlIPs'][] = "127.0.0.1";   // localhost, you'll want to add your own ip here
$_xhprof['controlIPs'][] = "::1";         // localhost IP v6

//$_xhprof['controlIPs'] = false; // No IP auth

//Default weight - can be overidden by an Apache environment variable 'xhprof_weight' for domain-specific values
// 1 means : every requests
$_xhprof['weight'] = 1;

if($domain_weight = getenv('xhprof_weight')) {
    $_xhprof['weight'] = $domain_weight;
}

unset($domain_weight);

return $_xhprof;
