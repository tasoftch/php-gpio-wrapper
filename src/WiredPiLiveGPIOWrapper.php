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


class WiredPiLiveGPIOWrapper extends LiveGPIOWrapper
{
    private $resistors = [];
    private $pwmValue = [];
    private $pwm = [];

    protected function writeAction(string $action, int $bcmPin, string $data): bool
    {
        if($action == 'direction') {
            if($data == "pwm") {
                $this->pwm[$bcmPin] = true;
                return exec("gpio -g mode $bcmPin pwm") ? false : true;
            } else {
                unset($this->pwm[$bcmPin]);
                unset($this->pwmValue[$bcmPin]);
            }
        }

        if($action == "resistor" && in_array($data, ["up", "down", 'tri'])) {
            $this->resistors[$bcmPin] = $data;
            return exec("gpio -g mode $bcmPin $data") ? false : true;
        }
        if($action == 'pwm' && is_numeric($data)) {
            $value = min(1023, max(0, $data*1));
            $this->pwmValue[$bcmPin] = $value;
            return exec("gpio -g pwm $bcmPin $value") ? false : true;
        }

        return parent::writeAction($action, $bcmPin, $data);
    }

    protected function readAction(string $action, int $bcmPin): string
    {
        if($action == 'resistor') {
            return $this->resistors[$bcmPin] ?? 'tri';
        }
        if($action == 'pwm')
            return $this->pwmValue[$bcmPin] ?? 0;
        if($action == 'direction' && isset($this->pwm[$bcmPin]))
            return 'pwm';

        return parent::readAction($action, $bcmPin);
    }


}