export interface BulkActionSubscriberInput {
    subscribers_ids: number[] | Record<string, number>;
    action: string;
    status?: string | null;
    metadata: Record<string, string>;
}
