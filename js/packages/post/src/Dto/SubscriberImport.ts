import type { SubscriberImportStatus } from './SubscriberImportStatus.js';

export interface SubscriberImport {
    id: number;
    created_at: number;
    status: SubscriberImportStatus;
    fields: Record<string, string | null> | null;
    csv_fields: string[] | null;
    imported_subscribers: number | null;
    warnings: string | null;
    error_message: string | null;
}
