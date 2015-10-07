<?php
namespace Log;

/**
 * Enum replacement for PHP.
 *
 *
 * Final with a private constructor to make sure it is never instantiated.
 *
 * List of internal name => readable name.
 */
final class LogLevel
{
	const TRACE = "debug";
	const INFO = "info";
	const WARN = "warning";
	const ERROR = "error";
	const FATAL = "fatal";

	private function __construct()
	{
	}
}
