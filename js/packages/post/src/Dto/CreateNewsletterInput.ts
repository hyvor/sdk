export interface CreateNewsletterInput {
    name: string;
    subdomain: string;
    autogenerate_subdomain_on_duplicate?: boolean;
    metadata?: Record<string, string>;
    start_trial?: boolean;
}
