import type { SubscriberExportStatus } from './SubscriberExportStatus.js';

export interface SubscriberExport {
    id: number;
    created_at: number;
    status: SubscriberExportStatus;
    error_message: string | null;
    url: string | null;
}
