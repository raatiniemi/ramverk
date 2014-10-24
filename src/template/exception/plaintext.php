<?php
namespace Me\Raatiniemi\Ramverk;

// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

// +--------------------------------------------------------------------------+
// | Global variable declaration.                                             |
// +--------------------------------------------------------------------------+
global $e;

// If headers have not already been sent, we have to set the content type.
if (!headers_sent()) {
    header('Content-Type: text/plain');
}

$className = get_class($e);
$length = strlen($className);

printf('===========%s%s', str_repeat('=', $length), PHP_EOL);
printf('Exception: %s%s', $className, PHP_EOL);
printf('===========%s%s', str_repeat('=', $length), PHP_EOL);

if ($e instanceof Exception) {
    printf('%1$sThis is an internal Ramverk exception.%1$s%1$s', PHP_EOL);
}

$message = sprintf(
    'An exception of type "%s" was thrown, but did not get caught during the '.
    'request execution.',
    $className
);
echo wordwrap($message, 80, PHP_EOL), PHP_EOL;

if (!empty($e->getMessage())) {
    printf('%1$sMessage%1$s', PHP_EOL);
    echo '=========', PHP_EOL;
    echo wordwrap(html_entity_decode($e->getMessage()), 80, PHP_EOL), PHP_EOL;
}

if (!empty($e->getTrace())) {
    printf('%1$sStack trace%1$s', PHP_EOL);
    echo '=============';

    foreach ($e->getTrace() as $index => $trace) {
        $file = isset($trace['file']) ? $trace['file'] : null;
        $line = isset($trace['line']) ? $trace['line'] : null;

        if (isset($file, $line)) {
            printf('%4$s#%1$d %2$s:%3$s%4$s', $index, $file, $line, PHP_EOL);
        }

        $class = isset($trace['class']) ? $trace['class'] : null;
        $function = isset($trace['function']) ? $trace['function'] : null;
        $type = isset($trace['type']) ? $trace['type'] : null;

        if (isset($class, $function, $type)) {
            printf('   %s%s%s%s', $class, $type, $function, PHP_EOL);
        }
    }
}

printf('%1$sVersion Information%1$s', PHP_EOL);
echo '=====================', PHP_EOL;
printf('PHP: %s%s', phpversion(), PHP_EOL);
printf('System: %s%s', php_uname(), PHP_EOL);
printf('Timestamp: %s%s', gmdate(DATE_ISO8601), PHP_EOL);

// End of file: plaintext.php
// Location: template/exception/plaintext.php
