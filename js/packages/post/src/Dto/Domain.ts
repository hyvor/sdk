import type { RelayDomainStatus } from './RelayDomainStatus.js';

export interface Domain {
    id: number;
    created_at: number;
    domain: string;
    dkim_public_key: string;
    dkim_txt_name: string;
    dkim_txt_value: string;
    relay_status: RelayDomainStatus;
    relay_last_checked_at: number | null;
    relay_error_message: string | null;
}
