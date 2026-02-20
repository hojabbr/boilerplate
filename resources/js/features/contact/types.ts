export interface ContactFormData {
    name: string;
    email: string;
    subject?: string;
    message: string;
}

export interface ContactMessages {
    heading?: string;
    form_name?: string;
    form_email?: string;
    form_subject?: string;
    form_message?: string;
    form_send?: string;
    label_email?: string;
    label_phone?: string;
    label_social?: string;
}
