export interface Domain {
    id: number;
    domain: string;
}

export interface Mod {
    id: number;
    created_at: number;
    role: string;
    website_id: number;
    user: UserMini;
}

export interface UserMini {
    id: number;
    name: string;
    username: string;
    picture_url: string | null;
}

export interface Website {
    id: number;
    name: string;
    created_at: string;
    is_blocked: boolean;
    is_deleted: boolean;
    metadata: Record<string, Record<string, unknown> | null> | null;
    created_by_source: string | null;
    domains: Domain[];
}

export interface CreateModInput {
    user_id: number;
    role?: string;
    on_duplicate?: string;
}

export interface CreateWebsiteInput {
    name: string;
    domain: string;
    metadata?: Record<string, Record<string, unknown> | null>;
    start_trial?: boolean;
}

export interface DeleteModInput {
    user_id?: number | null;
    mod_id?: number | null;
}

export interface UpdateDomainsInput {
    domains?: string[];
    operation?: string;
}
