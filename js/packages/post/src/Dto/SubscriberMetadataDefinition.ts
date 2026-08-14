import type { SubscriberMetadataDefinitionType } from './SubscriberMetadataDefinitionType.js';

export interface SubscriberMetadataDefinition {
    id: number;
    created_at: number;
    key: string;
    name: string;
    type: SubscriberMetadataDefinitionType;
}
