export interface CreateWebsiteInput {
    name: string;
    domain: string;
    metadata?: Record<string, Record<string, unknown> | null>;
    start_trial?: boolean;
    owner_user_id?: number | null;
}
