export interface AuthUser {
    id: number;
    username: string;
    name: string;
    email: string;
    picture_url: string | null;
    location: string | null;
    bio: string | null;
    website_url: string | null;
    oidc_sub: string | null;
}

export interface Domain {
    id: number;
    domain: string;
}

export interface Mod {
    id: number;
    created_at: number;
    role: string;
    website_id: number;
    user: AuthUser;
}

export interface Website {
    id: number;
    name: string;
    organization_id: number;
    owner_id: number;
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
    owner_user_id?: number | null;
}

export interface DeleteModInput {
    user_id?: number | null;
    mod_id?: number | null;
}

export interface UpdateDomainsInput {
    domains?: string[];
    operation?: string;
}
