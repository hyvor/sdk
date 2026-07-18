<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Job;

enum ImportCommentsFormat: string
{
    case WORDPRESS = 'wordpress';
    case DISQUS = 'disqus';
    case HYVOR_TALK = 'hyvor_talk';
    case INTENSE_DEBATE = 'intense_debate';
}
