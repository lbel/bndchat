<?php
namespace MessageParser\PriviligeAware;

use MessageParser\HtmlParser;
use MessageParser\UrlParser;
use MessageParser\EmojiParser;

use MessageParser\MessageParser;

class PriviligeAwareMessageParser
{
	/**
	 * @var MessageParser
	 */
	private $htmlParser;
	
	/**
	 * @var MessageParser
	 */
	private $urlParser;
	
	/**
	 * @var MessageParser
	 */
	private $emojiParser;
	
	public function process( $text, \User\Privilige\UserPrivilige $userPrivilige)
	{
		if (!$userPrivilige->canHtml())
		{
			$text = $this->htmlParser->process($text);
		}
		
		$text = $this->emojiParser->process($text);
		$text = $this->urlParser->process($text);
		
		var_dump($text);
		return $text;
	}
	
	public function __construct()
	{
		$this->htmlParser = new HtmlParser();
		$this->urlParser = new UrlParser();
		$this->emojiParser = new EmojiParser();
	}
}