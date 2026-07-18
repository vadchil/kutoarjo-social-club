import React, { ReactNode } from 'react';

export function PageHeader({ title, description, actions }: { title: string; description?: string; actions?: ReactNode }) {
    return <header className="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><h1 className="text-2xl font-extrabold tracking-[-0.025em] text-ink sm:text-3xl">{title}</h1>{description && <p className="mt-2 max-w-2xl text-sm leading-6 text-muted">{description}</p>}</div>{actions && <div className="flex flex-wrap gap-2">{actions}</div>}</header>;
}

export function Field({ id, label, error, hint, required, children }: { id: string; label: string; error?: string; hint?: string; required?: boolean; children: ReactNode }) {
    const errorId = `${id}-error`;
    const hintId = `${id}-hint`;
    return <div><label htmlFor={id} className="ksc-label">{label}{required && <span className="ml-1 text-brand-600" aria-hidden="true">*</span>}</label>{children}{hint && !error && <p id={hintId} className="mt-1.5 text-xs leading-5 text-muted">{hint}</p>}{error && <p id={errorId} className="ksc-error" role="alert">{error}</p>}</div>;
}

export const statusLabels: Record<string, string> = {
    pending_payment: 'Menunggu pembayaran', waiting_confirmation: 'Menunggu verifikasi', confirmed: 'Terkonfirmasi', in_progress: 'Sedang bermain', completed: 'Selesai', cancelled: 'Dibatalkan', expired: 'Kedaluwarsa', unpaid: 'Belum dibayar', waiting_verification: 'Menunggu verifikasi', paid: 'Lunas', rejected: 'Ditolak', refunded: 'Dikembalikan',
};

export function StatusBadge({ status }: { status: string }) {
    const tone = status === 'confirmed' || status === 'paid' || status === 'completed' ? 'success' : status === 'in_progress' ? 'info' : status === 'cancelled' || status === 'expired' || status === 'rejected' ? 'danger' : status === 'pending_payment' || status === 'waiting_confirmation' || status === 'waiting_verification' || status === 'unpaid' ? 'warning' : 'neutral';
    return <span className={`ksc-status ksc-status-${tone}`}>{statusLabels[status] || status.replaceAll('_', ' ')}</span>;
}

export function EmptyState({ title, description, action }: { title: string; description: string; action?: ReactNode }) {
    return <div className="rounded-2xl border border-dashed p-10 text-center"><div className="mx-auto mb-4 h-2 w-14 rounded-full bg-brand-200"/><h2 className="font-bold text-ink">{title}</h2><p className="mx-auto mt-2 max-w-md text-sm leading-6 text-muted">{description}</p>{action && <div className="mt-5">{action}</div>}</div>;
}

export const formatCurrency = (value: number) => `Rp${new Intl.NumberFormat('id-ID').format(value)}`;
export const formatDate = (value: string, options?: Intl.DateTimeFormatOptions) => new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta', dateStyle: 'long', ...options }).format(new Date(value));
export const formatTime = (value: string) => new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false }).format(new Date(value));
