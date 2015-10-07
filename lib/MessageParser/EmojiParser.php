<?php
namespace MessageParser;

class EmojiParser implements MessageParser
{
	private static $emoji = array(
		array(
			"from" => array(
				":)",
				":-)"
			),
			"to" => "\xF0\x9F\x98\x84"
		),
		array(
			"from" => array(
				":')",
				":'-)"
			),
			"to" => "\xF0\x9F\x98\x82"
		),
		array(
			"from" => array(
				":d",
				":-d",
				":D",
				":-D"
			),
			"to" => "\xF0\x9F\x98\x83"
		),
		array(
			"from" => array(
				":p",
				":-p",
				":P",
				":-P"
			),
			"to" => "\xF0\x9F\x98\x9C"
		),
		array(
			"from" => array(
				"xd",
				"xD",
				"XD"
			),
			"to" => "\xF0\x9F\x98\x86"
		),
		array(
			"from" => array(
				"xp",
				"xP",
				"XP"
			),
			"to" => "\xF0\x9F\x98\x9D"
		),
		array(
			"from" => array(
				";)",
				";-)"
			),
			"to" => "\xF0\x9F\x98\x89"
		),
		array(
			"from" => array(
				":(",
				":-("
			),
			"to" => "\xF0\x9F\x98\x94"
		),
		array(
			"from" => array(
				":'(",
				":'-("
			),
			"to" => "\xF0\x9F\x98\xA2"
		),
		array(
			"from" => array(
				":o",
				":-o",
				":O",
				":-O"
			),
			"to" => "\xF0\x9F\x98\xB1"
		),
		array(
			"from" => array(
				":*",
				":-*"
			),
			"to" => "\xF0\x9F\x98\x98"
		)
	);

	private static $isInit = false;

	/**
	 * PHP does not support the static constructor...
	 */
	public static function init()
	{
		if (! self::$isInit) {
			foreach (self::$emoji as $n => $v)
			{
				foreach ($v['from'] as $n2 => $v2)
				{
					self::$emoji[$n]['from'][$n2] = preg_quote($v2);
				}
			}
			
			self::$isInit = true;
		}
	}
	
	public function process ( $string )
	{
		$parsed = $string;
		foreach (self::$emoji as $single)
		{
			foreach ($single['from'] as $from) {
				$parsed = preg_replace("/(?<=\s|^)".$from."(?=\s|$)/i", $single['to'], $parsed);
			}
		}
		return $parsed;
	}
}

EmojiParser::init();