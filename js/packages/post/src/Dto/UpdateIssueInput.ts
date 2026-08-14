export interface UpdateIssueInput {
    subject?: string | null;
    lists: number[] | Record<string, number>;
    content?: string | null;
    sending_profile_id: number;
}
