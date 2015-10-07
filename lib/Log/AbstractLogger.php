<?php
namespace Log;

use Log\ReportLevel;

abstract class AbstractLogger implements Logger
{
	protected $useGlobalReportLevel = true;

	private $reportLevel;

	protected function __construct( $reportLevel = null )
	{
		if ($reportLevel === null) {
			$this->useGlobalReportLevel = true;
			$this->reportLevel = ReportLevel::$INFO;
		} else {
			$this->useGlobalReportLevel = false;
			$this->reportLevel = $reportLevel;
		}
	}

	abstract function sendMessage( $text, $level );

	public function info( $text )
	{
		$this->sendMessage($text, LogLevel::INFO);
	}

	public function warning( $text )
	{
		$this->sendMessage($text, LogLevel::WARN);
	}

	public function error( $text )
	{
		$this->sendMessage($text, LogLevel::ERROR);
	}

	public function trace( $text )
	{
		$this->sendMessage($text, LogLevel::TRACE);
	}

	public function fatal( $text )
	{
		$this->sendMessage($text, LogLevel::FATAL);
	}

	protected function getReportLevel()
	{
		if ($this->useGlobalReportLevel)
			return ReportLevel::getGlobalReportLevel();
		
		return $this->reportLevel;
	}
}