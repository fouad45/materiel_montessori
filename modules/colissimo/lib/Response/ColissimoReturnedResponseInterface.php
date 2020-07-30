<?php

/**
 * Interface ColissimoReturnedResponseInterface
 */
interface ColissimoReturnedResponseInterface
{
    /**
     * @param string $responseHeader
     * @param string $responseBody
     * @return mixed
     */
    public static function buildFromResponse($responseHeader, $responseBody);
}
