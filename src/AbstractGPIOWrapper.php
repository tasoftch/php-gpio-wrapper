<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace TASoft\GPIO;

/**
 * Class AbstractGPIOWrapper
 *
 * Assumes that all pin numbers are bcm pin numbers
 *
 * @package TASoft\GPIO
 */
abstract class AbstractGPIOWrapper implements GPIOStreamWrapperInterface
{
    /** @var int */
    private $bcmNumber;

    /** @var string */
    private $action;

    /** @var bool  */
    private $read = false;

    /** @var array List of exported pins */
    private static $exportedGPIOs = [];

    /**
     * @return array
     */
    public static function getExportedGPIOs(): array
    {
        return self::$exportedGPIOs;
    }

    /**
     * Called to export a pin
     *
     * @param int $bcmPinNumber
     * @return void
     */
    abstract protected function exportPin(int $bcmPinNumber);

    /**
     * Called to unexport a pin
     *
     * @param int $bcmPinNumber
     * @return void
     */
    abstract protected function unexportPin(int $bcmPinNumber);

    /**
     * Forwards any write operation to this method
     *
     * @param string $action
     * @param int $bcmPin
     * @param string $data
     * @return bool
     */
    abstract protected function writeAction(string $action, int $bcmPin, string $data): bool;

    /**
     * Forwards any read operation to this method
     *
     * @param string $action
     * @param int $bcmPin
     * @return string
     */
    abstract protected function readAction(string $action, int $bcmPin): string;


    /**
     * @param $path
     * @param $pathMode
     * @return bool
     */
    public function stream_open($path, $pathMode): bool
    {
        if(strcasecmp($path, 'gpio://export') == 0) {
            $this->action = 'exp';
        } elseif(strcasecmp($path, 'gpio://unexport') == 0) {
            $this->action = 'uexp';
        } elseif(preg_match("%^gpio://gpio(\d+)/(\w+)$%i", $path, $ms)) {
            $this->bcmNumber = $ms[1];
            $this->action = $ms[2];
        } else
            return false;
        return true;
    }

    /**
     * @param string $path
     * @param int $flags
     * @return array|bool
     */
    public function url_stat($path, $flags)
    {
        if(strcasecmp($path, 'gpio://export') == 0) {
            return [];
        } elseif(strcasecmp($path, 'gpio://unexport') == 0) {
            return [];
        } elseif(preg_match("%^gpio://gpio(\d+)%i", $path, $ms)) {
            $bcm = $ms[1];
            return in_array($bcm, self::$exportedGPIOs) ? [] : false;
        } else
            return false;
    }

    /**
     * @param int $length
     * @return false|string
     */
    public function stream_read(int $length)
    {
        if($this->read)
            return "";
        return $this->readAction($this->action, $this->bcmNumber);
    }

    /**
     * @return bool
     */
    public function stream_eof(): bool
    {
        if(!$this->read) {
            $this->read = true;
            return false;
        }
        return true;
    }

    /**
     * @param $data
     * @return int
     */
    public function stream_write($data): int
    {
        if($this->action == 'exp') {
            if(!in_array($data*1, self::$exportedGPIOs)) {
                $this->exportPin($data * 1);
                self::$exportedGPIOs[] = $data *1;
            }
            return strlen($data);
        } elseif($this->action == 'uexp') {
            if(($idx = array_search($data*1, self::$exportedGPIOs)) !== false) {
                $this->unexportPin($data*1);
                unset(self::$exportedGPIOs[$idx]);
            }

            return strlen($data);
        } else {
            if($this->writeAction($this->action, $this->bcmNumber, $data))
                return strlen($data);
            return 0;
        }
    }

    public static function register(): bool
    {
        if(in_array('gpio', stream_get_wrappers())) {
            stream_wrapper_unregister("gpio");
        }

        return stream_wrapper_register('gpio', get_called_class());
    }


}