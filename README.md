ZendMailJ
=========

Zend Mail でISO-2022-JPのメールを送れるようにしたラップクラスです。

Installation
-----------

composer
<pre>
composer.json
{
    "require": {
        "reioto/zend-mailj":"dev-master"
    }
}

#php composer.phar install
</pre>


Usage
-----

<pre>
function recipientFilename($transport)
{
    return $transport->recipients . '_' . mt_rand() . '.tmp';
}
$tr = new Zend_Mail_Transport_File(array('callback' => 'recipientFilename',
                                         'path' => dirname(__FILE__)
                                         ));
$mail = new Zend_MailJ();
$mail->setFrom('from@example.com', '差出人名');
$mail->addTo('to@example.com', '宛名');
$mail->setSubject('件名です。件名です。件名です。件名です。');
$mail->setBodyText('メール本文');
$mail->send($tr);
</pre>