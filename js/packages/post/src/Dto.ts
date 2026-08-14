export interface ApiKey {
    id: number;
    name: string;
    scopes: string[] | Record<string, string>;
    key: string | null;
    created_at: number;
    is_enabled: boolean;
    last_accessed_at: number | null;
}

export interface Domain {
    id: number;
    created_at: number;
    domain: string;
    dkim_public_key: string;
    dkim_txt_name: string;
    dkim_txt_value: string;
    relay_status: RelayDomainStatus;
    relay_last_checked_at: number | null;
    relay_error_message: string | null;
}

export interface Issue {
    id: number;
    uuid: string;
    created_at: number;
    subject: string | null;
    content: string | null;
    sending_profile_id: number;
    status: IssueStatus;
    lists: number[] | Record<string, number>;
    scheduled_at: number | null;
    sending_at: number | null;
    sent_at: number | null;
    total_sends: number;
    from_email: string | null;
    from_name: string | null;
    reply_to_email: string | null;
    sendable_subscribers_count: number;
}

export enum IssueStatus {
    DRAFT = 'draft',
    SCHEDULED = 'scheduled',
    SENDING = 'sending',
    SENT = 'sent',
}

export interface List {
    id: number;
    created_at: number;
    name: string;
    description: string | null;
    subscribers_count: number;
}

export enum ListRemovalReason {
    UNSUBSCRIBE = 'unsubscribe',
    BOUNCE = 'bounce',
    COMPLAINT = 'complaint',
    OTHER = 'other',
}

export enum ListsStrategy {
    MERGE = 'merge',
    OVERWRITE = 'overwrite',
    REMOVE = 'remove',
}

export enum MediaFolder {
    ISSUE_IMAGES = 'issue_images',
    NEWSLETTER_IMAGES = 'newsletter_images',
    IMPORT = 'import',
    EXPORT = 'export',
}

export interface Media {
    id: number;
    created_at: number;
    folder: MediaFolder;
    url: string;
    size: number;
    extension: string;
}

export enum MetadataStrategy {
    OVERWRITE = 'overwrite',
    MERGE = 'merge',
}

export enum NewsletterFormDefaultColorPalette {
    LIGHT = 'light',
    DARK = 'dark',
    OS = 'os',
}

export interface Newsletter {
    id: number;
    subdomain: string;
    created_at: number;
    name: string;
    language_code: string | null;
    is_rtl: boolean;
    metadata: Record<string, string>;
    address: string | null;
    unsubscribe_text: string | null;
    branding: boolean;
    template_color_accent: string | null;
    template_color_accent_text: string | null;
    template_color_background: string | null;
    template_color_background_text: string | null;
    template_color_box: string | null;
    template_color_box_text: string | null;
    template_box_shadow: string | null;
    template_box_radius: string | null;
    template_box_border: string | null;
    template_font_family: string | null;
    template_font_size: string | null;
    template_font_weight: string | null;
    template_font_weight_heading: string | null;
    template_font_line_height: string | null;
    form_title: string | null;
    form_description: string | null;
    form_footer_text: string | null;
    form_button_text: string | null;
    form_success_message: string | null;
    form_width: number | null;
    form_custom_css: string | null;
    form_color_light_text: string | null;
    form_color_light_text_light: string | null;
    form_color_light_accent: string | null;
    form_color_light_accent_text: string | null;
    form_color_light_input: string | null;
    form_color_light_input_text: string | null;
    form_light_input_box_shadow: string | null;
    form_light_input_border: string | null;
    form_light_border_radius: number | null;
    form_color_dark_text: string | null;
    form_color_dark_text_light: string | null;
    form_color_dark_accent: string | null;
    form_color_dark_accent_text: string | null;
    form_color_dark_input: string | null;
    form_color_dark_input_text: string | null;
    form_dark_input_box_shadow: string | null;
    form_dark_input_border: string | null;
    form_dark_border_radius: number | null;
    form_default_color_palette: NewsletterFormDefaultColorPalette;
    form_input_border_radius: number;
}

export enum RelayDomainStatus {
    PENDING = 'pending',
    ACTIVE = 'active',
    WARNING = 'warning',
    SUSPENDED = 'suspended',
}

export interface SendingProfile {
    id: number;
    created_at: number;
    from_email: string;
    from_name: string | null;
    reply_to_email: string | null;
    brand_name: string | null;
    brand_logo: string | null;
    brand_url: string | null;
    is_default: boolean;
    is_system: boolean;
}

export interface Send {
    id: number;
    created_at: number;
    subscriber: Subscriber | null;
    email: string;
    status: SendStatus;
    sent_at: number | null;
    failed_at: number | null;
    delivered_at: number | null;
    unsubscribed_at: number | null;
    bounced_at: number | null;
    hard_bounce: boolean;
    complained_at: number | null;
}

export enum SendStatus {
    PENDING = 'pending',
    SENT = 'sent',
    FAILED = 'failed',
}

export interface SubscriberExport {
    id: number;
    created_at: number;
    status: SubscriberExportStatus;
    error_message: string | null;
    url: string | null;
}

export enum SubscriberExportStatus {
    PENDING = 'pending',
    COMPLETED = 'completed',
    FAILED = 'failed',
}

export interface SubscriberImport {
    id: number;
    created_at: number;
    status: SubscriberImportStatus;
    fields: Record<string, string | null> | null;
    csv_fields: string[] | null;
    imported_subscribers: number | null;
    warnings: string | null;
    error_message: string | null;
}

export enum SubscriberImportStatus {
    REQUIRES_INPUT = 'requires_input',
    PENDING_APPROVAL = 'pending_approval',
    IMPORTING = 'importing',
    FAILED = 'failed',
    COMPLETED = 'completed',
}

export interface SubscriberMetadataDefinition {
    id: number;
    created_at: number;
    key: string;
    name: string;
    type: SubscriberMetadataDefinitionType;
}

export enum SubscriberMetadataDefinitionType {
    TEXT = 'text',
}

export interface Subscriber {
    id: number;
    email: string;
    source: SubscriberSource;
    status: SubscriberStatus;
    list_ids: number[] | Record<string, number>;
    lists: string[] | Record<string, string>;
    subscribe_ip: string | null;
    subscribed_at: number | null;
    metadata: Record<string, boolean | number | string>;
}

export enum SubscriberSource {
    CONSOLE = 'console',
    FORM = 'form',
    IMPORT = 'import',
}

export enum SubscriberStatus {
    SUBSCRIBED = 'subscribed',
    PENDING = 'pending',
}

export interface Template {
    template: string;
}

export interface UserMini {
    name: string;
    email: string;
    username: string | null;
    picture_url: string | null;
}

export interface User {
    id: number;
    role: UserRole;
    created_at: number;
    user: UserMini;
}

export enum UserRole {
    OWNER = 'owner',
    ADMIN = 'admin',
}

export interface BulkActionSubscriberInput {
    subscribers_ids: number[] | Record<string, number>;
    action: string;
    status?: string | null;
    metadata: Record<string, string>;
}

export interface CreateApiKeyInput {
    name: string;
    scopes: string[];
}

export interface CreateDomainInput {
    domain: string;
}

export interface CreateListInput {
    name: string;
    description?: string | null;
}

export interface CreateNewsletterInput {
    name: string;
    subdomain: string;
    autogenerate_subdomain_on_duplicate?: boolean;
    metadata?: Record<string, string>;
    start_trial?: boolean;
}

export interface CreateSendingProfileInput {
    from_email: string;
    from_name?: string | null;
    reply_to_email?: string | null;
    brand_name?: string | null;
    brand_logo?: string | null;
    brand_url?: string | null;
}

export interface CreateSubscriberInput {
    email: string;
    lists?: (number | string)[] | Record<string, number | string> | null;
    status?: SubscriberStatus | null;
    source?: SubscriberSource | null;
    subscribe_ip?: string | null;
    subscribed_at?: number | null;
    metadata?: Record<string, boolean | number | string> | null;
    lists_strategy?: ListsStrategy;
    list_skip_resubscribe_on?: string[] | Record<string, string>;
    list_removal_reason?: ListRemovalReason;
    metadata_strategy?: MetadataStrategy;
    send_pending_confirmation_email?: boolean;
    subscribeIp?: string | null;
    subscribedAt?: string | null;
    listSkipResubscribeOn: ListRemovalReason[] | Record<string, ListRemovalReason>;
    listResubscribeOnValues: string[] | Record<string, string>;
}

export interface CreateSubscriberMetadataDefinitionInput {
    key: string;
    name: string;
}

export interface CreateUserInput {
    user_id: number;
    on_duplicate?: string;
}

export interface DeleteUserInput {
    user_id?: number | null;
    id?: number | null;
}

export interface ImportInput {
    mapping: Record<string, string | null>;
}

export interface MediaUploadInput {
    folder: MediaFolder;
}

export interface RenderTemplateInput {
    template?: string | null;
}

export interface SendTestInput {
    emails: string[] | Record<string, string>;
}

export interface UpdateApiKeyInput {
    name: string;
    is_enabled: boolean;
    scopes: string[] | Record<string, string>;
}

export interface UpdateIssueInput {
    subject?: string | null;
    lists: number[] | Record<string, number>;
    content?: string | null;
    sending_profile_id: number;
}

export interface UpdateListInput {
    name?: string | null;
    description?: string | null;
}

export interface UpdateNewsletterInput {
    name: string;
    subdomain: string;
}

export interface UpdateSendingProfileInput {
    from_email: string;
    from_name?: string | null;
    reply_to_email?: string | null;
    brand_name?: string | null;
    brand_logo?: string | null;
    brand_url?: string | null;
    is_default: boolean;
}

export interface UpdateSubscriberMetadataDefinitionInput {
    name: string;
}

export interface UpdateTemplateInput {
    template?: string | null;
}

export interface UploadImportInput {
    source: string;
}
