import type { SendStatus } from './SendStatus.js';
import type { Subscriber } from './Subscriber.js';

export interface Send {
    id: number;
    created_at: number;
    subscriber: Subscriber | null;
    email: string;
    status: SendStatus;
    sent_at: number | null;
    failed_at: number | null;
    delivered_at: number | null;
    unsubscribed_at: number | null;
    bounced_at: number | null;
    hard_bounce: boolean;
    complained_at: number | null;
}
