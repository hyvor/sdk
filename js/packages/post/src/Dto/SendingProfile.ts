export interface SendingProfile {
    id: number;
    created_at: number;
    from_email: string;
    from_name: string | null;
    reply_to_email: string | null;
    brand_name: string | null;
    brand_logo: string | null;
    brand_url: string | null;
    is_default: boolean;
    is_system: boolean;
}
