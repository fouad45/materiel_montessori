<?php

/**
 * Class ColissimoResponseParser
 */
class ColissimoResponseParser
{
    /** Regex for separator */
    const UUID = '/--uuid:/';

    /** New line character in Response */
    const NEW_LINE_CHAR = "\r\n";

    /** @var string $uuid */
    private $uuid;

    /**
     * @param string $rawHeaders
     * @return array
     */
    public function parseHeaders($rawHeaders)
    {
        $headers = array();
        $key = '';

        foreach (explode("\n", $rawHeaders) as $i => $h) {
            $h = explode(':', $h, 2);
            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
            }
        }

        return $headers;
    }

    /**
     * @param string $body
     * @return array
     * @throws Exception
     */
    public function parseBody($body)
    {
        $contents = $this->splitContent($body);
        if (!is_array($contents) || empty($contents)) {
            throw new Exception('Empty response.');
        }
        $parsedResponse = array();
        foreach ($contents as $content) {
            if ($this->uuid == null) {
                $uuidStart = strpos($content, self::UUID, 0) + strlen(self::UUID);
                $uuidEnd = strpos($content, self::NEW_LINE_CHAR, $uuidStart);
                $this->uuid = substr($content, $uuidStart, $uuidEnd - $uuidStart);
            }
            $headers = $this->extractHeader($content);
            if (count($headers) > 0) {
                if (isset($headers['Content-ID'])) {
                    $parsedResponse[$headers['Content-ID']] = trim(substr($content, $headers['offsetEnd']));
                }
            }
        }
        if (empty($parsedResponse)) {
            throw new Exception('Response cannot be parsed.');
        }

        return $parsedResponse;
    }

    /**
     * @param array $parsedHeaders
     * @return bool|string
     */
    public function parseContentType($parsedHeaders)
    {
        if (!isset($parsedHeaders['Content-Type'])) {
            return false;
        }
        $contentTypes = explode(';', $parsedHeaders['Content-Type']);
        foreach ($contentTypes as $contentType) {
            if (strpos($contentType, 'start=') !== false) {
                return substr($contentType, 8, -1);
            }
        }

        return false;
    }

    /**
     * @param string $response
     * @return array
     */
    private static function splitContent($response)
    {
        $contents = array();
        $matches = array();
        preg_match_all(self::UUID, $response, $matches, PREG_OFFSET_CAPTURE);
        for ($i = 0; $i < count($matches[0]) - 1; $i++) {
            if ($i + 1 < count($matches[0])) {
                $contents[$i] = substr(
                    $response,
                    $matches[0][$i][1],
                    $matches[0][$i + 1][1] - $matches[0][$i][1]
                );
            } else {
                $contents[$i] = substr(
                    $response,
                    $matches[0][$i][1],
                    strlen($response)
                );
            }
        }

        return $contents;
    }

    /**
     * @param string $part
     * @return array
     */
    private function extractHeader($part)
    {
        $header = array();
        $headerLineStart = strpos($part, 'Content-', 0);
        $endLine = 0;
        while ($headerLineStart !== false) {
            $header['offsetStart'] = $headerLineStart;
            $endLine = strpos($part, self::NEW_LINE_CHAR, $headerLineStart);
            $headerLine = explode(': ', substr($part, $headerLineStart, $endLine - $headerLineStart));
            $header[$headerLine[0]] = $headerLine[1];
            $headerLineStart = strpos($part, 'Content-', $endLine);
        }
        $header['offsetEnd'] = $endLine;

        return $header;
    }
}
