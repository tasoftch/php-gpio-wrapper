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


interface GPIOStreamWrapperInterface
{
    /**
     * Implementaion of the php's built-in stream wrapper
     * Opens a path
     *
     * @param $path
     * @param $pathMode
     * @return bool
     */
    public function stream_open($path, $pathMode): bool;

    /**
     * Implementaion of the php's built-in stream wrapper
     * Return the state of a path
     *
     * @param string $path
     * @param int $flags
     * @return bool|array
     */
    public function url_stat($path, $flags);

    /**
     * Reads to end of contents or $length characters
     *
     * @param int $length
     * @return string|false
     */
    public function stream_read(int $length);

    /**
     * If stream_read returns false and this method also, the read process stops
     *
     * @return bool
     */
    public function stream_eof(): bool;

    /**
     * Stream writes to resource.
     * Should return amount of written bytes
     *
     * @param $data
     * @return int
     */
    public function stream_write($data): int;

    /**
     * Registers this class as stream wrapper
     *
     * @return bool
     */
    public static function register(): bool;
}