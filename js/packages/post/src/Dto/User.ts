import type { UserMini } from './UserMini.js';
import type { UserRole } from './UserRole.js';

export interface User {
    id: number;
    role: UserRole;
    created_at: number;
    user: UserMini;
}
