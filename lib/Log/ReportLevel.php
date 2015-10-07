<?php
namespace Log;

final class ReportLevel
{
	public static $ALL = 5;
	public static $TRACE = 5;
	public static $INFO = 3;
	public static $WARN = 2;
	public static $ERROR = 1;
	public static $FATAL = 0;
	public static $NONE = -1;
	
	private static $GLOBAL_REPORT_LEVEL = 3;

	public static function isPassedReportLevel( $logLevel, $reportLevel )
	{
		$passList = array(
			self::$INFO => array(LogLevel::INFO, LogLevel::WARN, LogLevel::ERROR, LogLevel::FATAL),
			self::$WARN => array(LogLevel::WARN, LogLevel::ERROR, LogLevel::FATAL),
			self::$ERROR => array(LogLevel::ERROR, LogLevel::FATAL),
			self::$FATAL => array(LogLevel::FATAL),
			self::$NONE => array()
		);
		
		if ($reportLevel >= self::$TRACE)
			return true;
		
		return (isset(array_flip($passList[ $reportLevel ])[ $logLevel ]));
	}
	
	public static function getGlobalReportLevel()
	{
		return self::$GLOBAL_REPORT_LEVEL;	
	}
	
	public static function setGlobalReportLevel($reportLevel)
	{
		self::$GLOBAL_REPORT_LEVEL = $reportLevel;	
	}
	
	private function __construct()
	{
	
	}
}