import type { IssueStatus } from './IssueStatus.js';

export interface Issue {
    id: number;
    uuid: string;
    created_at: number;
    subject: string | null;
    content: string | null;
    sending_profile_id: number;
    status: IssueStatus;
    lists: number[] | Record<string, number>;
    scheduled_at: number | null;
    sending_at: number | null;
    sent_at: number | null;
    total_sends: number;
    from_email: string | null;
    from_name: string | null;
    reply_to_email: string | null;
    sendable_subscribers_count: number;
}
