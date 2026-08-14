import type { ListRemovalReason } from './ListRemovalReason.js';
import type { ListsStrategy } from './ListsStrategy.js';
import type { MetadataStrategy } from './MetadataStrategy.js';
import type { SubscriberSource } from './SubscriberSource.js';
import type { SubscriberStatus } from './SubscriberStatus.js';

export interface CreateSubscriberInput {
    email: string;
    lists?: (number | string)[] | Record<string, number | string> | null;
    status?: SubscriberStatus | null;
    source?: SubscriberSource | null;
    subscribe_ip?: string | null;
    subscribed_at?: number | null;
    metadata?: Record<string, boolean | number | string> | null;
    lists_strategy?: ListsStrategy;
    list_skip_resubscribe_on?: string[] | Record<string, string>;
    list_removal_reason?: ListRemovalReason;
    metadata_strategy?: MetadataStrategy;
    send_pending_confirmation_email?: boolean;
    subscribeIp?: string | null;
    subscribedAt?: string | null;
    listSkipResubscribeOn: ListRemovalReason[] | Record<string, ListRemovalReason>;
    listResubscribeOnValues: string[] | Record<string, string>;
}
