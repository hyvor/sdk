<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Rule;

enum RuleType: string
{
    case WORD_MATCHES = 'word_matches';
    case LINK_DOMAIN_MATCHES = 'link_domain_matches';
    case USER_NAME_MATCHES = 'user_name_matches';
    case LINK_COUNT_EXCEEDS = 'link_count_exceeds';
    case FLAGS_EXCEEDS = 'flags_exceeds';
    case DOWNVOTES_EXCEEDS = 'downvotes_exceeds';
    case UPVOTES_EXCEEDS = 'upvotes_exceeds';
    case USER_REPUTATION_EXCEEDS = 'user_reputation_exceeds';
}
