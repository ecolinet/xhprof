<?php
if (PHP_SAPI == 'cli') {
    $_SERVER['REMOTE_ADDR'] = null;
    $_SERVER['HTTP_HOST'] = null;
    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
}

define('XHPROF_LIB_ROOT', dirname(dirname(__FILE__)) . '/library');

// Config
$_xhprof = include XHPROF_LIB_ROOT . '/config.php';

// Only users from authorized IP addresses may control Profiling
if ($_xhprof['controlIPs'] === false || in_array($_SERVER['REMOTE_ADDR'], $_xhprof['controlIPs']) || PHP_SAPI == 'cli') {
    if (isset($_GET['_profile'])) {
        // Give them a cookie to hold status, and redirect back to the same page
        setcookie('_profile', $_GET['_profile']);
    }
    
    if (isset($_COOKIE['_profile']) && $_COOKIE['_profile'] || PHP_SAPI == 'cli' && ((isset($_SERVER['XHPROF_PROFILE']) && $_SERVER['XHPROF_PROFILE']) || (isset($_ENV['XHPROF_PROFILE']) && $_ENV['XHPROF_PROFILE']))) {
        $_xhprof['doprofile'] = true;
        $_xhprof['type'] = 1;
    }
}

// Certain urls should have their POST data omitted. Think login forms, other privlidged info
$_xhprof['savepost'] = true;
foreach ($_xhprof['exceptionPostURLs'] as $url) {
    if (stripos($_SERVER['REQUEST_URI'], $url) !== FALSE) {
        $_xhprof['savepost'] = false;
        break;
    }
}

// Determine wether or not to profile this URL randomly
if ($_xhprof['doprofile'] === false) {
    // Profile weighting, one in one hundred requests will be profiled without being specifically requested
    if (rand(1, $_xhprof['weight']) == 1) {
        $_xhprof['doprofile'] = true;
        $_xhprof['type'] = 0;
    }
}

// Certain URLS should never be profiled.
foreach ($_xhprof['ignoreURLs'] as $url) {
    if (stripos($_SERVER['REQUEST_URI'], $url) !== FALSE) {
        $_xhprof['doprofile'] = false;
        break;
    }
}

unset($url);

// Certain domains should never be profiled.
foreach ($_xhprof['ignoreDomains'] as $domain) {
    if (stripos($_SERVER['HTTP_HOST'], $domain) !== FALSE) {
        $_xhprof['doprofile'] = false;
        break;
    }
}
unset($domain);

// Display warning if extension not available
if (extension_loaded('xhprof') && $_xhprof['doprofile'] === true) {
    require_once dirname(__FILE__) . '/../library/utils/xhprof_lib.php';
    require_once dirname(__FILE__) . '/../library/utils/xhprof_runs.php';
    
    if (isset($_xhprof['ignoredFunctions']) && is_array($_xhprof['ignoredFunctions']) && ! empty($_xhprof['ignoredFunctions'])) {
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY, array(
            'ignored_functions' => $_xhprof['ignoredFunctions']
        ));
    } else {
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    }

    register_shutdown_function('xhprof_shutdown_function', $_xhprof);
    
} elseif (! extension_loaded('xhprof') && $_xhprof['display'] === true) {
    $message = 'Warning! Unable to profile run, xhprof extension not loaded';
    trigger_error($message, E_USER_WARNING);
}

function xhprof_shutdown_function($_xhprof)
{
    $xhprof_data = xhprof_disable();
    $xhprof_runs = new XHProfRuns_Default();
    $profiler_namespace = $_xhprof['namespace']; // namespace for your application
    $run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace, null, $_xhprof);
    
    if ($_xhprof['display'] === true && PHP_SAPI != 'cli') {
        // url to the XHProf UI libraries (change the host name and path)
        $profiler_url = sprintf($_xhprof['url'] . '/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
        echo '<a href="' . $profiler_url . '" target="_blank">Profiler output</a>';
    }
}
