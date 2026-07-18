import React from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import { EmptyState, formatCurrency, formatDate, formatTime, PageHeader, StatusBadge, statusLabels } from '@/components/ui';

interface BilliardTable { id: string; name: string; }
interface PaymentMethod { id: string; name: string; }
interface Booking { id: string; booking_code: string; customer_name: string; customer_phone: string; billiard_table: BilliardTable; payment_method: PaymentMethod | null; start_at: string; end_at: string; total_price: number; status: string; payment_status: string; }
interface PaginationLink { url: string | null; label: string; active: boolean; }
interface BookingsIndexProps { bookings: { data: Booking[]; links: PaginationLink[]; current_page: number; last_page: number; }; filters: { status?: string; }; }

const statuses = ['', 'pending_payment', 'waiting_confirmation', 'confirmed', 'in_progress', 'completed', 'cancelled'];

export default function Index({ bookings, filters }: BookingsIndexProps) {
    const filter = (status: string) => router.get('/admin/bookings', status ? { status } : {}, { replace: true, preserveState: true });
    const updateStatus = (id: string, status: string, notes?: string) => router.patch(`/admin/bookings/${id}/status`, { status, notes });
    const cancel = (id: string) => { const reason = window.prompt('Alasan pembatalan'); if (reason) updateStatus(id, 'cancelled', reason); };

    return <AdminLayout><Head title="Booking" />
        <PageHeader title="Booking" description="Verifikasi pembayaran, mulai sesi, selesaikan permainan, dan batalkan slot bila perlu." actions={<Link href="/admin/schedule" className="ksc-button-secondary">Jadwal meja</Link>} />
        <section className="ksc-panel overflow-hidden">
            <div className="flex gap-2 overflow-x-auto border-b p-4" aria-label="Filter status booking">
                {statuses.map((status) => <button key={status || 'all'} type="button" onClick={() => filter(status)} className={`ksc-status whitespace-nowrap ${(!status && !filters.status) || filters.status === status ? 'ksc-status-info' : 'ksc-status-neutral'}`}>{status ? statusLabels[status] || status : 'Semua'}</button>)}
            </div>
            {bookings.data.length ? <div className="overflow-x-auto">
                <table className="ksc-table w-full text-sm">
                    <thead><tr><th>Kode</th><th>Customer</th><th>Meja</th><th>Jadwal</th><th>Total</th><th>Status</th><th className="text-right">Aksi</th></tr></thead>
                    <tbody>{bookings.data.map((booking) => <tr key={booking.id}>
                        <td className="font-mono font-black text-ink">{booking.booking_code}</td>
                        <td><p className="font-semibold text-ink">{booking.customer_name}</p><p className="text-xs text-muted">{booking.customer_phone}</p></td>
                        <td className="font-semibold text-ink">{booking.billiard_table.name}</td>
                        <td><p className="font-semibold text-ink">{formatDate(booking.start_at, { dateStyle: 'medium' })}</p><p className="text-xs text-muted">{formatTime(booking.start_at)}—{formatTime(booking.end_at)} WIB</p></td>
                        <td className="font-bold text-ink">{formatCurrency(booking.total_price)}</td>
                        <td><div className="flex flex-col gap-1"><StatusBadge status={booking.status} /><StatusBadge status={booking.payment_status} /></div></td>
                        <td className="min-w-40 text-right"><div className="flex flex-wrap justify-end gap-2">
                            {booking.status === 'pending_payment' && <button type="button" onClick={() => updateStatus(booking.id, 'confirmed')} className="ksc-button-secondary min-h-9 px-3 text-xs">Konfirmasi</button>}
                            {booking.status === 'confirmed' && <button type="button" onClick={() => updateStatus(booking.id, 'in_progress')} className="ksc-button-secondary min-h-9 px-3 text-xs">Mulai</button>}
                            {booking.status === 'in_progress' && <button type="button" onClick={() => updateStatus(booking.id, 'completed')} className="ksc-button-primary min-h-9 px-3 text-xs">Selesai</button>}
                            {['pending_payment', 'confirmed'].includes(booking.status) && <button type="button" onClick={() => cancel(booking.id)} className="ksc-button-danger min-h-9 px-3 text-xs">Batal</button>}
                        </div></td>
                    </tr>)}</tbody>
                </table>
            </div> : <div className="p-6"><EmptyState title="Tidak ada booking" description="Belum ada booking untuk filter ini. Ubah filter atau cek jadwal meja." /></div>}
        </section>
    </AdminLayout>;
}
