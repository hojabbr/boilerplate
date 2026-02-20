export interface AuthUser {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
}

export interface AuthPageProps {
    canResetPassword?: boolean;
    canRegister?: boolean;
    status?: string;
}
