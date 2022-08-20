<?php declare(strict_types=1);

namespace Danek\FeedIo\Standard;

use Danek\FeedIo\Rule\DateTimeBuilder;

class Loader
{

    /**
     * @param DateTimeBuilder $builder
     * @return array
     */
    public function getCommonStandards(DateTimeBuilder $builder): array
    {
        return [
            'json' => new Json($builder),
            'atom' => new Atom($builder),
            'rss' => new Rss($builder),
            'rdf' => new Rdf($builder),
        ];
    }
}
