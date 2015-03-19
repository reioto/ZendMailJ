<?php

require_once 'Zend_MailJ.php';

/**
 * Mock mail transport class for testing purposes
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_Mock extends Zend_Mail_Transport_Abstract
{
	/**
	 * @var Zend_Mail
	 */
	public $mail       = null;
	public $returnPath = null;
	public $subject    = null;
	public $from       = null;
	public $headers    = null;
	public $called     = false;

	public function _sendMail()
	{
		$this->mail       = $this->_mail;
		$this->subject    = $this->_mail->getSubject();
		$this->from       = $this->_mail->getFrom();
		$this->returnPath = $this->_mail->getReturnPath();
		$this->headers    = $this->_headers;
		$this->called     = true;
	}
}

class Zend_MailJTest extends PHPUnit_Framework_TestCase
{
	public function testJapaneseHeader_chunked()
	{
		mb_internal_encoding('utf-8');
		$mail = new Zend_MailJ();
		$testStr = 'あああああああああああああああああああああああああ';

		$mail->addTo('to@example.com', $testStr);
		$mail->setSubject($testStr);
		$mail->setFrom('from@example.com', $testStr);
		$mail->setBodyText('hogehoge');

		$mock = new Zend_Mail_Transport_Mock();
		$mail->send($mock);

		$headerNames = array('To', 'Subject', 'From');

		foreach($headerNames as $headerName) {
			$header = $mock->headers[$headerName][0];
			$blocks = array();

			preg_match_all('/=\?(?<charset>.+?)\?(?<encoding>.+?)\?(?<body>.+?)\?=/', $header, $chunks, PREG_SET_ORDER);
			$this->assertGreaterThanOrEqual(2, count($chunks), 'We want to test chunked header!');

			foreach($chunks as $chunk) {
				$this->assertSame('ISO-2022-JP', $chunk['charset']);
				$this->assertSame('B', $chunk['encoding']);
				// All chunks started with Japanese character should have escape sequence for compatibility.
				$this->assertStringStartsWith('GyRC', $chunk['body']);
			}
		}
	}
}

