<?php
require_once 'Zend/Mail.php';

/**
 * Zend_MailJ
 * wraped class for Zend_Mail
 * supported Japanese
 * @package Zend_MailJ
 */

class Zend_MailJ extends Zend_Mail
{
    public function __construct($charset = 'ISO-2022-JP')
    {
        parent::__construct($charset);
        if ($charset === 'ISO-2022-JP') {
            $this->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
        }
    }

    public function mbconvert($string)
    {
        return mb_convert_encoding($string, $this->getCharset());
    }

    public function setBodyText($txt, $charset = null, $encoding = null)
    {
        if($charset === null) {
            $charset = $this->getCharset();
            $txt = $this->mbconvert($txt);
        }

        if($encoding === null) {
            if($charset === 'ISO-2022-JP') {
                $encoding = Zend_Mime::ENCODING_7BIT;
            } else {
                $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE;
            }
        }

        return parent::setBodyText($txt, $charset, $encoding);
    }

    /**
     * override
     * @see Zend_Mail
     */
    protected function _encodeHeader($value)
    {
        if (Zend_Mime::isPrintable($value) === false) {
            if ($this->getHeaderEncoding() === Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
                $value = mb_encode_mimeheader($value, $this->getCharset(),
                                              'Q', Zend_Mime::LINEEND);
            } else {
                $value = mb_encode_mimeheader($value, $this->getCharset(),
                                              'B', Zend_Mime::LINEEND);
            }
        }

        return $value;
    }
}
