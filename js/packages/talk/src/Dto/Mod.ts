import type { AuthUser } from './AuthUser.js';

export interface Mod {
    id: number;
    created_at: number;
    role: string;
    website_id: number;
    user: AuthUser;
}
