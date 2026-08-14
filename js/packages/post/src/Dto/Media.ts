import type { MediaFolder } from './MediaFolder.js';

export interface Media {
    id: number;
    created_at: number;
    folder: MediaFolder;
    url: string;
    size: number;
    extension: string;
}
