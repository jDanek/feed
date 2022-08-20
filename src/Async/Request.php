<?php declare(strict_types=1);

namespace Danek\FeedIo\Async;

use Danek\FeedIo\Adapter\ResponseInterface;

class Request
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var \DateTime
     */
    protected $modifiedSince;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Request constructor.
     * @param string $url
     * @param \DateTime|null $modifiedSince
     */
    public function __construct(string $url, \DateTime $modifiedSince = null)
    {
        error_log("Async reading is deprecated and will be removed in v5.0.", E_USER_DEPRECATED);
        $this->url = $url;
        $this->modifiedSince = $modifiedSince ?? new \DateTime('@0');
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedSince(): \DateTime
    {
        return $this->modifiedSince;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
