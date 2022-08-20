<?php declare(strict_types=1);

namespace Danek\FeedIo\Adapter;

/**
 * Describes a HTTP Response as returned by an instance of ClientInterface
 *
 */
interface ResponseInterface
{

    /**
     * @return string
     */
    public function getBody(): ?string;

    /**
     * @return int
     */
    public function getDuration(): int;

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @return \DateTime
     */
    public function getLastModified(): ?DateTime;

    /**
     * @return iterable
     */
    public function getHeaders(): iterable;

    /**
     * @param string $name
     * @return iterable
     */
    public function getHeader(string $name): iterable;

    /**
     * @return boolean
     */
    public function isModified(): bool;
}
