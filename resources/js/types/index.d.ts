export interface User {
    id: string;
    name: string;
    email: string;
    role: 'admin' | 'staff';
}

export interface PageProps extends Record<string, unknown> {
    auth: {
        user: User | null;
    };
    flash: {
        success: string | null;
        error: string | null;
    };
}
