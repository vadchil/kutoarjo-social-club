import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { formatCurrency, formatDate, formatTime, StatusBadge } from '@/components/ui';

interface PaymentMethod { id: string; name: string; }
interface BookingStatusProps { booking: { booking_code: string; customer_name: string; table_name: string; start_at: string; end_at: string; duration_hours: number; total_price: number; status: string; payment_status: string; payment_method: PaymentMethod | null; }; }

export default function BookingStatus({ booking }: BookingStatusProps) {
    const rows = [
        ['Nama', booking.customer_name], ['Meja', booking.table_name], ['Tanggal', formatDate(booking.start_at)], ['Waktu', `${formatTime(booking.start_at)}—${formatTime(booking.end_at)} WIB`], ['Durasi', `${booking.duration_hours} jam`], ['Total', formatCurrency(booking.total_price)], ['Pembayaran', booking.payment_method?.name || 'Belum dipilih'],
    ];

    return <PublicLayout><Head title="Status Booking" />
        <section className="ksc-container ksc-page">
            <div className="mx-auto max-w-2xl">
                <div className="ksc-panel overflow-hidden">
                    <div className="border-b bg-white p-6 sm:p-8"><div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"><div><p className="font-mono text-sm font-bold text-brand-700">{booking.booking_code}</p><h1 className="mt-2 text-3xl font-black tracking-[-0.03em] text-ink">Detail reservasi</h1></div><StatusBadge status={booking.status} /></div></div>
                    <div className="p-6 sm:p-8"><dl className="grid gap-x-8 gap-y-4 sm:grid-cols-[12rem_1fr]">{rows.map(([label, value]) => <React.Fragment key={label}><dt className="text-sm font-bold text-muted">{label}</dt><dd className="font-semibold text-ink">{value}</dd></React.Fragment>)}<dt className="text-sm font-bold text-muted">Status pembayaran</dt><dd><StatusBadge status={booking.payment_status} /></dd></dl><div className="mt-8 border-t pt-6"><Link href="/booking/status" className="ksc-button-secondary">Cek booking lain</Link></div></div>
                </div>
            </div>
        </section>
    </PublicLayout>;
}
