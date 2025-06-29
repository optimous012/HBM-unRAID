<?php

/*
  MIT License

  Copyright (c) 2020-2022 b3rs3rk

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
*/

namespace hbastat\lib;

/** @noinspection PhpIncludeInspection */
require_once('/usr/local/emhttp/plugins/dynamix/include/Wrappers.php');

/**
 * Class Main
 * @package hbastat\lib
 */
class Main
{
    const PLUGIN_NAME = 'hbastat';
    const COMMAND_EXISTS_CHECKER = 'which';
    const CMD_UTILITY = 'storcli64';
    const STATISTICS_PARAM = '/c0 show temperature';

    /**
     * @var array
     */
    public $settings;

    /**
     * @var string
     */
    protected $stdout;

    /**
     * @var array
     */
    protected $pageData;

    /**
     * @var bool
     */
    protected $cmdexists;

    /**
     * HBAStat constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;

        $this->stdout = '';
        $this->inventory = [];

        $this->pageData = [
            'temp'      => 'N/A',
            'tempmax'   => 'N/A',
        ];
    }


    /**
     * Retrieves plugin settings and returns them or defaults if no file
     *
     * @return mixed
     */
    public static function getSettings()
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        return parse_plugin_cfg(self::PLUGIN_NAME);
    }

    /**
     * Triggers regex match all against class variable stdout and places matches in class variable inventory
     *
     * @param string $regex
     */
    protected function parseInventory(string $regex = '')
    {
        preg_match_all($regex, $this->stdout, $this->inventory, PREG_SET_ORDER);
    }


    /**
     * Strips all spaces from a provided string
     *
     * @param string $text
     * @return string
     */
    protected static function stripSpaces(string $text = ''): string
    {
        return str_replace(' ', '', $text);
    }

    /**
     * Converts Celsius to Fahrenheit
     *
     * @param int $temp
     * @return float
     */
    protected static function convertCelsius(int $temp = 0): float
    {
        $fahrenheit = $temp*(9/5)+32;
        
        return round($fahrenheit, -1);
    }

    /**
     * Rounds a float to a whole number
     *
     * @param float $number
     * @param int $precision
     * @return float
     */
    protected static function roundFloat(float $number, int $precision = 0): float
    {
        if ($precision > 0) {
            $result = number_format(round($number, $precision), $precision, '.','');
        } else {
            $result = round($number, $precision);
        }

        return $result;
    }

    /**
     * Replaces a string within a string with an empty string
     *
     * @param string|string[] $strip
     * @param string $string
     * @return string|string[]
     */
    protected static function stripText($strip, string $string)
    {
        return str_replace($strip, '', $string);
    }

    public function getStatistics()
    {
        $this->stdout = shell_exec(self::CMD_UTILITY . ES . self::STATISTICS_PARAM);
        if (!empty($this->stdout) && strlen($this->stdout) > 0) {
            $this->parseStatistics();
        } else {
            $this->pageData['error'][] = Error::get(Error::VENDOR_DATA_NOT_RETURNED);
        }

        return json_encode($this->pageData) ;
    }

    private function parseStatistics()
    {
        $test = strpos($this->stdout,"temperature(Degree Celsius)");
        $hba_temp = substr($this->stdout, $test + 1 + strlen("temperature(Degree Celsius)"), 2);
        $this->pageData['temp'] = $hba_temp + 'C';
        if ($this->settings['TEMPFORMAT'] == 'F') {
            $this->pageData[$key] = $this->convertCelsius((int) $this->stripText('C', $this->pageData[$key])) . 'F';
        }
    }
}
