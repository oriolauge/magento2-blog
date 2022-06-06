<?php

namespace OAG\Blog\Api;

/**
 * Interface UrlFinderInterface
 */
interface UrlFinderInterface
{
    /**
     * @param string $path
     */
    public function resolve(string $path);
}
