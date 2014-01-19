<?php
// +--------------------------------------------------------------------------+
// | This file is a part of the Ramverk project by The Developer Blog.        |
// | Copyright (c) 2013, Authors                                              |
// | Copyright (c) 2013, The Developer Blog                                   |
// +--------------------------------------------------------------------------+
namespace Net\TheDeveloperBlog\Ramverk
{
// +--------------------------------------------------------------------------+
// | Namespace use-directives.                                                |
// +--------------------------------------------------------------------------+

// +--------------------------------------------------------------------------+
// | Global variable declaration.                                             |
// +--------------------------------------------------------------------------+
	global $e;

	// If headers have not already been sent, we have to set the content type.
	if(!headers_sent()) {
		header('Content-Type: text/plain');
	}

	$className = get_class($e);
?>
===========<?=str_repeat('=', strlen($className)) . PHP_EOL; ?>
Exception: <?=$className . PHP_EOL; ?>
===========<?=str_repeat('=', strlen($className)) . PHP_EOL; ?>

<?php if($e instanceof Exception) : ?>
This is an internal Ramverk exception.
<?php endif; ?>

<?php
	$message = sprintf(
		'An exception of type "%s" was thrown, but did not get caught during the request execution.',
		$className
	);

	echo wordwrap($message, 80, PHP_EOL) . PHP_EOL;
?>

Message
=========
<?=wordwrap(html_entity_decode($e->getMessage()), 80, PHP_EOL) . PHP_EOL; ?>

Version Information
=====================
PHP: <?=phpversion() . PHP_EOL; ?>
System: <?=php_uname() . PHP_EOL; ?>
Timestamp: <?=gmdate(DATE_ISO8601) . PHP_EOL; ?>
<?php
}
// End of file: plaintext.php
// Location: template/exception/plaintext.php ?>