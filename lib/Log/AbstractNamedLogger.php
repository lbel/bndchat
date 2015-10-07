<?php
namespace Log;

use \Named;

abstract class AbstractNamedLogger extends abstractLogger implements Logger, Named
{
	/**
	 *
	 * @var string
	 */
	protected $name;

	protected function __construct( $name, $reportLevel )
	{
		parent::__construct($reportLevel);
		
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}
}