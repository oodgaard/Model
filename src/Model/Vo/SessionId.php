<?php

namespace Model\Vo;

/**
 * The SessionId Vo.
 * 
 * @category ValueObjects
 * @package  HostingControl
 * @author   Trey Shugart <tshugart@ultraserve.com.au>
 * @license  Copyright (c) Ultra Serve http://ultraserve.com.au/license
 */
class SessionId extends Generic
{
    /**
     * Generates a default session id.
     * 
     * @return SessionId
     */
    public function __construct()
    {
        parent::set(md5(mt_rand() . microtime() . mt_rand()));
    }
}