<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Page;

enum PageSort: string
{
    case NEWEST = 'newest';
    case OLDEST = 'oldest';
    case RECENTLY_COMMENTED = 'recently_commented';
    case MOST_COMMENTED = 'most_commented';
    case MOST_REACTIONS = 'most_reactions';
    case MOST_RATINGS = 'most_ratings';
    case HIGHEST_RATING = 'highest_rating';
}
