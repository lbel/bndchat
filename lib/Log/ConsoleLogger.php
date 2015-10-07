<?php
namespace Log;

/**
 * Logger which writes to stdout / print
 */
class ConsoleLogger extends AbstractNamedLogger implements Logger
{
	public function __construct($name, $reportLevel=null)
	{
		parent::__construct($name, $reportLevel);	
	}

	public function sendMessage( $text, $level )
	{
		if (ReportLevel::isPassedReportLevel($level, $this->getReportLevel()))
		{
			print "[" . $this->getName() . "] ";
			for ($a = 3 + strlen($this->getName()); $a < 35; $a++)
			{
				echo " ";
			}
			print strtoupper($level);

			for ($a = 2 + strlen($level); $a < 15; $a++)
			{
				echo " ";
			}
			echo $text . "\n";
		}
	}
}