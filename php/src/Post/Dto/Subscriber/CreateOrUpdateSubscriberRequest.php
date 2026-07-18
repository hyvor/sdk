<?php

declare(strict_types=1);

namespace Hyvor\Sdk\Post\Dto\Subscriber;

/**
 * Input for `SubscribersResource::createOrUpdate()` (`POST /subscribers`).
 * If a subscriber with the given email already exists, it is updated;
 * otherwise a new subscriber is created.
 */
final class CreateOrUpdateSubscriberRequest
{
    /**
     * @param (int|string)[]|null $lists An array of list IDs or names.
     *  Subscribes to or unsubscribes from lists based on `$lists_strategy`.
     * @param array<string, string>|null $metadata Keys must be defined in
     *  the Subscriber Metadata Definitions section (or via the API).
     * @param ListSkipResubscribeReason[]|null $list_skip_resubscribe_on If
     *  the subscriber was previously removed from a list, the reason(s) for
     *  ignoring re-subscription to that list. Default:
     *  `[unsubscribe, bounce, complaint]`.
     */
    public function __construct(
        public readonly string $email,
        public readonly ?array $lists = null,
        /** Default: subscribed. */
        public readonly ?SubscriberStatus $status = null,
        /** Default: console. */
        public readonly ?SubscriberSource $source = null,
        public readonly ?string $subscribe_ip = null,
        /**
         * Unix timestamp of when the subscriber opted in. If not set, it
         * will be set to the current time if status is `subscribed`.
         */
        public readonly ?int $subscribed_at = null,
        public readonly ?array $metadata = null,

        // ============ SETTINGS ===========
        // change how the endpoint behaves

        /** Default: merge. */
        public readonly ?ListsStrategy $lists_strategy = null,
        public readonly ?array $list_skip_resubscribe_on = null,
        /** Default: unsubscribe. Only used when `$lists_strategy` is `REMOVE`. */
        public readonly ?ListRemovalReason $list_removal_reason = null,
        /** Default: merge. */
        public readonly ?MetadataStrategy $metadata_strategy = null,
        /**
         * Whether to send a confirmation email when adding a subscriber with
         * `pending` status, or when changing an existing subscriber's status
         * to `pending`. Default: false.
         */
        public readonly ?bool $send_pending_confirmation_email = null,
    ) {
    }
}
