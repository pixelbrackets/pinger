<?php

namespace Tests\Raitocz\Pinger;

use PHPUnit_Framework_TestCase;
use Raitocz\Pinger\Exception\PingerException;
use Raitocz\Pinger\Pinger;

/**
 * Class TimerTest
 * @package Tests\Raitocz\Pinger
 */
class PingerTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function test_getters_setters()
    {
        $pinger = new Pinger();

        $urls = array('http://localhost/');
        $repeat = 100;
        $wait = 1;
        $mode = Pinger::MODE_RANDOM;
        $getData = array('a' => true);
        $postData = array('b' => true);
        $verbose = true;

        $pinger->setUrls($urls);
        $pinger->setRepeat($repeat);
        $pinger->setWait($wait);
        $pinger->setMode($mode);
        $pinger->setGetData($getData);
        $pinger->setPostData($postData);
        $pinger->setVerbose($verbose);

        $this->assertEquals($urls, $pinger->getUrls());
        $this->assertEquals($repeat, $pinger->getRepeat());
        $this->assertEquals($wait, $pinger->getWait());
        $this->assertEquals($mode, $pinger->getMode());
        $this->assertEquals($getData, $pinger->getGetData());
        $this->assertEquals($postData, $pinger->getPostData());
        $this->assertEquals($verbose, $pinger->isVerbose());
    }


    /**
     * @dataProvider valid_urls_provider
     */
    public function test_valid_urls($url)
    {
        $pinger = new Pinger();

        $this->assertNotNull($pinger->setUrls(array($url)));
    }

    /**
     * @dataProvider invalid_urls_provider
     * @expectedException Raitocz\Pinger\Exception\PingerException
     */
    public function test_invalid_urls($url)
    {
        $pinger = new Pinger();
        $pinger->setUrls(array($url));
    }

    public function valid_urls_provider()
    {
        return array(
            array('http://localhost/'),
            array('www.localhost.com'),
            array('www.url-with-querystring.com/?url=has-querystring'),
            array('https://localhost/?a=&c=o'),
            array('http://127.0.0.1/')
        );
    }

    public function invalid_urls_provider()
    {
        return array(
            array('google.com'),
            array('nonsense'),
            array('http: //localhost/'),
            array('://asd')
        );
    }
}
