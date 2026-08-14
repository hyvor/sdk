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
