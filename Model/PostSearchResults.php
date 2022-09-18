<?php
declare(strict_types=1);

namespace OAG\Blog\Model;
use OAG\Blog\Api\Data\PostSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with Post search results.
 */
class PostSearchResults extends SearchResults implements PostSearchResultsInterface
{
}
