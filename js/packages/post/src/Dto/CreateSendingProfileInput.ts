export interface CreateSendingProfileInput {
    from_email: string;
    from_name?: string | null;
    reply_to_email?: string | null;
    brand_name?: string | null;
    brand_logo?: string | null;
    brand_url?: string | null;
}
