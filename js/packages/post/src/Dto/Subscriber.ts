import type { SubscriberSource } from './SubscriberSource.js';
import type { SubscriberStatus } from './SubscriberStatus.js';

export interface Subscriber {
    id: number;
    email: string;
    source: SubscriberSource;
    status: SubscriberStatus;
    list_ids: number[] | Record<string, number>;
    lists: string[] | Record<string, string>;
    subscribe_ip: string | null;
    subscribed_at: number | null;
    metadata: Record<string, boolean | number | string>;
}
