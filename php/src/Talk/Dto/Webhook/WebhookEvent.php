<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Talk\Dto\Webhook;

enum WebhookEvent: string
{
    case COMMENT_CREATE = 'comment.create';
    case COMMENT_UPDATE = 'comment.update';
    case COMMENT_DELETE = 'comment.delete';

    case REACTION_CREATED = 'reaction.created';
    case REACTION_UPDATED = 'reaction.updated';
    case REACTION_DELETED = 'reaction.deleted';

    case RATING_CREATED = 'rating.created';
    case RATING_UPDATED = 'rating.updated';
    case RATING_DELETED = 'rating.deleted';

    case COMMENT_VOTE_CREATED = 'comment.vote.created';
    case COMMENT_VOTE_UPDATED = 'comment.vote.updated';
    case COMMENT_VOTE_DELETED = 'comment.vote.deleted';

    case COMMENT_FLAG_CREATED = 'comment.flag.created';
    case COMMENT_FLAG_DELETED = 'comment.flag.deleted';

    case USER_CREATED = 'user.created';
    case USER_UPDATED = 'user.updated';

    case MEDIA_CREATED = 'media.created';
    case MEDIA_DELETED = 'media.deleted';
}
