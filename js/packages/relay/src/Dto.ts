export interface ApiKey {
    id: number;
    name: string;
    scopes: string[];
    allowed_ips: string[];
    key: string | null;
    created_at: number;
    is_enabled: boolean;
    last_accessed_at: number | null;
}

export interface Domain {
    id: number;
    created_at: number;
    domain: string;
    status: DomainStatus;
    status_changed_at: number;
    dkim_selector: string;
    dkim_host: string;
    dkim_public_key: string;
    dkim_txt_value: string;
    dkim_checked_at: number | null;
    dkim_error_message: string | null;
}

export enum DomainStatus {
    PENDING = 'pending',
    ACTIVE = 'active',
    WARNING = 'warning',
    SUSPENDED = 'suspended',
}

export interface Project {
    id: number;
    created_at: number;
    name: string;
    send_type: ProjectSendType;
}

export enum ProjectSendType {
    TRANSACTIONAL = 'transactional',
    DISTRIBUTIONAL = 'distributional',
}

export interface ProjectUserMini {
    id: number;
    name: string;
    email: string;
    username: string | null;
    picture_url: string | null;
    oidc_sub: string | null;
}

export interface ProjectUser {
    id: number;
    created_at: number;
    scopes: string[];
    user: ProjectUserMini;
    oidc_sub: string | null;
    project: Project;
}

export interface SendAttempt {
    id: number;
    created_at: number;
    status: SendAttemptStatus;
    try_count: number;
    domain: string;
    resolved_mx_hosts: string[];
    responded_mx_host: string | null;
    smtp_conversations: Record<string, Record<string, unknown> | null>;
    duration_ms: number;
    recipients: SendAttemptRecipient[];
}

export interface SendAttemptRecipient {
    id: number;
    created_at: number;
    recipient_id: number;
    recipient_status: SendRecipientStatus;
    smtp_code: number;
    smtp_enhanced_code: string | null;
    smtp_message: string;
    is_suppressed: boolean;
}

export enum SendAttemptStatus {
    ACCEPTED = 'accepted',
    DEFERRED = 'deferred',
    BOUNCED = 'bounced',
    PARTIAL = 'partial',
    FAILED = 'failed',
}

export interface SendContent {
    body_html: string | null;
    body_text: string | null;
    headers: Record<string, string>;
    raw: string;
}

export interface SendFeedback {
    id: number;
    created_at: number;
    type: SendFeedbackType;
    recipient_id: number;
    debug_incoming_email_id: number;
}

export enum SendFeedbackType {
    BOUNCE = 'bounce',
    COMPLAINT = 'complaint',
}

export interface Send {
    id: number;
    uuid: string;
    created_at: number;
    from_address: string;
    from_name: string | null;
    subject: string | null;
    size_bytes: number;
    queued: boolean;
    send_after: number;
    recipients: SendRecipient[];
    attempts: SendAttempt[];
    feedback: SendFeedback[];
}

export interface SendRecipient {
    id: number;
    type: SendRecipientType;
    address: string;
    name: string;
    status: SendRecipientStatus;
    try_count: number;
}

export enum SendRecipientStatus {
    QUEUED = 'queued',
    ACCEPTED = 'accepted',
    DEFERRED = 'deferred',
    BOUNCED = 'bounced',
    COMPLAINED = 'complained',
    SUPPRESSED = 'suppressed',
    FAILED = 'failed',
}

export enum SendRecipientType {
    TO = 'to',
    CC = 'cc',
    BCC = 'bcc',
}

export interface Suppression {
    id: number;
    created_at: number;
    email: string;
    reason: string;
    description: string | null;
}

export interface WebhookDelivery {
    id: number;
    created_at: number;
    url: string;
    event: WebhooksEventEnum;
    status: WebhookDeliveryStatus;
    response: string | null;
    response_code: number | null;
    try_count: number;
    request_body: string;
}

export enum WebhookDeliveryStatus {
    PENDING = 'pending',
    DELIVERED = 'delivered',
    FAILED = 'failed',
}

export interface Webhook {
    id: number;
    url: string;
    description: string | null;
    events: string[];
    secret: string | null;
}

export enum WebhooksEventEnum {
    SEND_RECIPIENT_ACCEPTED = 'send.recipient.accepted',
    SEND_RECIPIENT_DEFERRED = 'send.recipient.deferred',
    SEND_RECIPIENT_BOUNCED = 'send.recipient.bounced',
    SEND_RECIPIENT_COMPLAINED = 'send.recipient.complained',
    SEND_RECIPIENT_SUPPRESSED = 'send.recipient.suppressed',
    SEND_RECIPIENT_FAILED = 'send.recipient.failed',
    SUPPRESSION_CREATED = 'suppression.created',
    SUPPRESSION_DELETED = 'suppression.deleted',
    DOMAIN_CREATED = 'domain.created',
    DOMAIN_STATUS_CHANGED = 'domain.status.changed',
    DOMAIN_DELETED = 'domain.deleted',
}

export interface AnalyticsStatsInput {
    period?: string;
}

export interface CreateApiKeyInput {
    name: string;
    scopes: string[];
    allowed_ips?: string[];
}

export interface CreateProjectInput {
    name: string;
    send_type: ProjectSendType;
}

export interface CreateProjectUserInput {
    user_id: number;
    scopes: string[];
}

export interface CreateWebhookInput {
    url: string;
    description: string;
    events: string[];
}

export interface DomainCreateInput {
    domain: string;
    dkim_selector?: string | null;
    dkim_private_key?: string | null;
}

export interface DomainIdOrDomainInput {
    id?: number | null;
    domain?: string | null;
}

export interface RetrySendInput {
    send_after?: number | null;
    recipient_ids?: number[] | null;
}

export interface SendEmailInput {
    from: Record<string, string> | string;
    to: (Record<string, string> | string)[] | Record<string, Record<string, string> | string> | Record<string, string> | string;
    cc?: (Record<string, string> | string)[] | Record<string, Record<string, string> | string> | Record<string, string> | string;
    bcc?: (Record<string, string> | string)[] | Record<string, Record<string, string> | string> | Record<string, string> | string;
    subject?: string;
    body_html?: string | null;
    body_text?: string | null;
    headers?: Record<string, string>;
    attachments?: (Record<string, string | null>)[];
}

export interface UpdateApiKeyInput {
    name: string;
    is_enabled: boolean;
    scopes: string[];
    allowed_ips: string[];
}

export interface UpdateProjectInput {
    name: string;
}

export interface UpdateWebhookInput {
    url: string;
    description: string;
    events: string[];
}
