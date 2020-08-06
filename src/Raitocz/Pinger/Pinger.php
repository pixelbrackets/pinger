<?php

namespace Raitocz\Pinger;

use Raitocz\Pinger\Exception\PingerException;

/**
 * Class Pinger
 * @package Raitocz\Pinger
 */
class Pinger
{
    /** Randomly picks the URL from array */
    const MODE_RANDOM = 1;
    /** Randomly picks the URL from array but never sends to same URL twice in a row */
    const MODE_RANDOM_NOREPEAT = 2;
    /** Takes each URL in array and sends number of request specified in $repeat  */
    const MODE_BATCH_URL = 3;
    /** Iterates $repeat times trough array in a loop and sends each URL one request */
    const MODE_BATCH_ARRAY = 4;

    /** @var array List of urls to ping */
    protected $urls = array();

    /** @var integer Number of repeats */
    protected $repeat;

    /** @var  integer Selected mode of operation */
    protected $mode = self::MODE_RANDOM;

    /** @var float Number of seconds to wait between each request */
    protected $wait = 1;

    /** @var array Array of GET variables */
    protected $getData = array();

    /** @var array Array of POST variables */
    protected $postData = array();

    /** @var array Array of Proxy IPs */
    protected $proxies;

    /** @var bool Verbose mode */
    protected $verbose = false;

    /**
     * Static call if you don't want to create objects
     *
     * @param $urls
     * @param $repeat
     * @param $wait
     * @param int $mode
     * @param array $getData
     * @param array $postData
     * @param bool $verbose
     * @return void
     */
    public static function run($urls, $repeat, $wait, $mode = self::MODE_RANDOM,
                               $postData = array(), $getData = array(), $verbose = false)
    {
        $pinger = new self();

        $pinger->setUrls($urls)
            ->setRepeat($repeat)
            ->setWait($wait)
            ->setMode($mode)
            ->setGetData($getData)
            ->setPostData($postData)
            ->setVerbose($verbose)
            ->start();

        unset($pinger);
    }


    /**
     * Starts the pinging process
     *
     * @return void
     * @throws PingerException
     */
    public function start()
    {
        if (!count($this->urls) || $this->repeat == null || $this->wait == null) {
            throw new PingerException('URLs, Repeat or Wait is not set');
        }

        switch ($this->getMode()) {
            case self::MODE_RANDOM:
                $this->modeRandom();
                break;
            case self::MODE_RANDOM_NOREPEAT:
                $this->modeRandomNoRepeat();
                break;
            case self::MODE_BATCH_URL:
                $this->modeBatchURL();
                break;
            case self::MODE_BATCH_ARRAY:
                $this->modeBatchArray();
                break;
            default:
                throw new PingerException('Unknown mode');
        }
    }


    /**
     *
     */
    protected function ping($url)
    {
        if ($this->isVerbose()) {
            print('ping: ' . $url . PHP_EOL);
        }

        $pingPath = realpath(dirname(__FILE__). '/Ping.php');
        shell_exec('php '. $pingPath .' '. $url . '  >log.txt 2>log.txt &');
        usleep($this->wait * 1000000);
    }

    /**
     * Checks for valid URLs with regexp.
     *
     * @see https://regex101.com/r/cX0pJ8/1
     * @param $urls
     * @return void
     * @throws PingerException
     */
    protected function checkUrls($urls)
    {
        foreach ($urls as $url) {
          if (!preg_match_all('/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[\-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[\-;:&=\+\$,\w]+
            @)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w\-_]*)?\??(?:[\-\+=&;%@.\w_]*)#?(?:[.\!\/\\w]*))?)/i', $url)) {
                throw new PingerException('URL is in incorrect format: ' . $url);
            }
        }
    }


    /**
     * Mode Random
     * For each URL there is counter of how many times it was pinged and after it hits the repeat value it is then
     * removed from array.
     *
     * @return void
     */
    protected function modeRandom()
    {
        $urls = $this->urls;
        $stats = array_fill_keys($urls, 0);

        while (count($urls) > 0) {
            $urlsCount = count($urls);
            $url = $urls[rand(0, $urlsCount - 1)];
            $stats[$url]++;

            if ($stats[$url] == $this->repeat) {
                if (($key = array_search($url, $urls)) !== false) {
                    unset($urls[$key]);
                    sort($urls);
                    continue;
                }
            }

            $this->ping($url);
        }
    }


    /**
     * Mode Random - No repeat
     * For each URL there is counter of how many times it was pinged and after it hits the repeat value it is then
     * removed from array. It is ensured that the URL will not be pinged twice in a row.
     *
     * Currently, if all URLs were pinged and there is for example 4 remaining pings for last URL it will do them all
     * in row. If this is an issue for you then please make ticket on GitHub.
     *
     * @see https://github.com/raitocz/pinger
     * @return void
     */
    protected function modeRandomNoRepeat()
    {
        $urls = $this->urls;
        $prevUrl = null;
        $stats = array_fill_keys($urls, 0);

        while (count($urls) > 0) {
            $urlsCount = count($urls);
            $url = $urls[rand(0, $urlsCount - 1)];

            if ($url == $prevUrl && $urlsCount > 1) {
                continue;
            }

            $stats[$url]++;

            if ($stats[$url] == $this->repeat) {
                if (($key = array_search($url, $urls)) !== false) {
                    unset($urls[$key]);
                    sort($urls);
                    continue;
                }
            }

            $this->ping($url);
            $prevUrl = $url;
        }
    }

    /**
     * Mode Batch URL
     * Each time URL is being processed this mode will sent all Repeat number of pings to it and then proceeds to next
     * url.
     *
     * @return void
     */
    protected function modeBatchURL()
    {
        $urls = $this->urls;

        foreach ($urls as $url) {
            for ($pings = 0; $pings <= $this->repeat; $pings++) {
                $this->ping($url);
            }
        }
    }

    /**
     * Mode Batch Array
     * The array of URLs is being pinged one by one for the number of Repeats.
     *
     * @return void
     */
    protected function modeBatchArray()
    {
        $urls = $this->urls;

        for ($pings = 0; $pings <= $this->repeat; $pings++) {
            foreach ($urls as $url) {
                $this->ping($url);
            }
        }
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     * @return $this
     * @throws PingerException
     */
    public function setMode($mode)
    {
        if (!is_numeric($mode)) {
            throw new PingerException('Invalid mode set');
        }
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @param array $urls
     * @return $this
     * @throws PingerException
     */
    public function setUrls($urls)
    {
        if (!is_array($urls)) {
            throw new PingerException('URLs must be strings in array format');
        }
        $this->checkUrls($urls);
        $this->urls = $urls;

        return $this;
    }


    /**
     * @return int
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * @param int $repeat
     * @return $this
     * @throws PingerException
     */
    public function setRepeat($repeat)
    {
        if (!is_numeric($repeat)) {
            throw new PingerException('Invalid repeat set');
        }

        $this->repeat = $repeat;
        return $this;
    }

    /**
     * @return float
     */
    public function getWait()
    {
        return $this->wait;
    }

    /**
     * @param float $wait
     * @return $this
     * @throws PingerException
     */
    public function setWait($wait)
    {
        if (!is_numeric($wait)) {
            throw new PingerException('Invalid wait set');
        }
        $this->wait = $wait;
        return $this;
    }

    /**
     * @return array
     */
    public function getGetData()
    {
        return $this->getData;
    }

    /**
     * @param array $getData
     * @return $this
     * @throws PingerException
     */
    public function setGetData($getData)
    {
        if($getData === null){
            return $this;
        }

        if(!is_array($getData)){
            throw new PingerException('GET data should be in array');
        }
        $this->getData = http_build_query($getData);
        return $this;
    }

    /**
     * @return array
     */
    public function getPostData()
    {
        return $this->postData;
    }

    /**
     * @param array $postData
     * @return $this
     * @throws PingerException
     */
    public function setPostData($postData)
    {
        if($postData === null){
            return $this;
        }

        if(!is_array($postData)){
            throw new PingerException('POST data should be in array');
        }
        $this->postData = http_build_query($postData);
        return $this;
    }

    /**
     * @return bool
     */
    public function isVerbose()
    {
        return $this->verbose;
    }

    /**
     * @param bool $verbose
     * @return $this
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
        return $this;
    }


}
