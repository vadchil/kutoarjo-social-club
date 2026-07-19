import React, { useEffect, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { formatCurrency, formatDate, formatTime } from '@/components/ui';

interface PaymentMethod { id: string; name: string; instructions: string; bank_name?: string; account_number?: string; account_holder?: string; qris_image_path?: string; }
interface BookingSuccessProps { booking: { booking_code: string; customer_name: string; table_name: string; start_at: string; end_at: string; duration_hours: number; hourly_price: number; total_price: number; payment_method: PaymentMethod | null; status: string; expires_at: string | null; }; whatsapp_url: string; }

export default function BookingSuccess({ booking, whatsapp_url }: BookingSuccessProps) {
    const [timeLeft, setTimeLeft] = useState(0);

    useEffect(() => {
        if (!booking.expires_at) return;
        const updateTimer = () => setTimeLeft(Math.max(0, Math.floor((new Date(booking.expires_at!).getTime() - Date.now()) / 1000)));
        updateTimer();
        const interval = setInterval(updateTimer, 1000);
        return () => clearInterval(interval);
    }, [booking.expires_at]);

    const timeLabel = `${String(Math.floor(timeLeft / 60)).padStart(2, '0')}:${String(timeLeft % 60).padStart(2, '0')}`;

    return <PublicLayout><Head title="Booking Berhasil">
        <meta name="robots" content="noindex,nofollow" />
    </Head>
        <section className="ksc-container ksc-page">
            <div className="mx-auto max-w-2xl">
                <div className="ksc-panel overflow-hidden text-center">
                    <div className="bg-[oklch(0.95_0.045_150)] px-6 py-8"><div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl font-black text-[oklch(0.36_0.12_150)]">✓</div><h1 className="mt-4 text-3xl font-black tracking-[-0.03em] text-ink">Booking berhasil dibuat.</h1><p className="mt-2 text-sm text-muted">Simpan kode booking ini.</p><p className="mt-3 font-mono text-xl font-black text-brand-700">{booking.booking_code}</p></div>
                    <div className="p-6 text-left sm:p-8">
                        {booking.expires_at && <div role="status" className={`mb-6 rounded-2xl p-4 ${timeLeft > 0 ? 'bg-[oklch(0.95_0.07_93)] text-[oklch(0.34_0.1_75)]' : 'bg-[oklch(0.96_0.035_25)] text-[oklch(0.43_0.16_25)]'}`}><p className="font-bold">{timeLeft > 0 ? 'Batas konfirmasi pembayaran' : 'Waktu konfirmasi habis'}</p><p className="mt-1 text-sm">{timeLeft > 0 ? 'Kirim bukti pembayaran sebelum timer habis.' : 'Silakan cek status booking atau buat booking baru.'}</p>{timeLeft > 0 && <p className="mt-3 font-mono text-3xl font-black">{timeLabel}</p>}</div>}
                        <dl className="grid gap-x-8 gap-y-4 sm:grid-cols-[12rem_1fr]"><dt className="text-sm font-bold text-muted">Nama</dt><dd className="font-semibold text-ink">{booking.customer_name}</dd><dt className="text-sm font-bold text-muted">Meja</dt><dd className="font-semibold text-ink">{booking.table_name}</dd><dt className="text-sm font-bold text-muted">Jadwal</dt><dd className="font-semibold text-ink">{formatDate(booking.start_at)} · {formatTime(booking.start_at)}—{formatTime(booking.end_at)} WIB</dd><dt className="text-sm font-bold text-muted">Durasi</dt><dd className="font-semibold text-ink">{booking.duration_hours} jam</dd><dt className="text-sm font-bold text-muted">Total</dt><dd className="font-black text-ink">{formatCurrency(booking.total_price)}</dd></dl>
                        {booking.payment_method && <div className="ksc-panel-subtle mt-6 p-4"><p className="font-bold text-ink">{booking.payment_method.name}</p><p className="mt-2 whitespace-pre-line text-sm leading-6 text-muted">{booking.payment_method.instructions}</p>{booking.payment_method.account_number && <div className="mt-4 border-t pt-4 text-sm leading-6"><p>Bank: {booking.payment_method.bank_name}</p><p>Nomor rekening: <span className="font-mono font-black text-ink">{booking.payment_method.account_number}</span></p><p>Atas nama: {booking.payment_method.account_holder}</p></div>}</div>}
                        <div className="mt-8 flex flex-col gap-3 sm:flex-row"><a href={whatsapp_url} target="_blank" rel="noreferrer" className="ksc-button-primary flex-1">Konfirmasi via WhatsApp</a><Link href="/booking/status" className="ksc-button-secondary flex-1">Cek status</Link></div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>;
}
