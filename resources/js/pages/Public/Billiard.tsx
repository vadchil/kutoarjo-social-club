import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { formatCurrency } from '@/components/ui';

export default function Billiard({ settings, prices }: { settings: Record<string, unknown>; prices: Record<string, number> }) {
    return <PublicLayout settings={settings}><Head title="Billiard Kutoarjo">
        <meta name="description" content="Booking meja billiard di Kutoarjo Social Club. Pilih meja, cek jadwal, lihat harga weekday dan weekend, lalu konfirmasi via WhatsApp." />
    </Head>
        <section className="ksc-container ksc-page ksc-rise">
            <div className="grid gap-10 lg:grid-cols-[1.1fr_.9fr] lg:items-end">
                <div>
                    <p className="mb-4 max-w-md font-bold leading-6 text-brand-700">Billiard tanpa ribet: pilih meja, pilih jam, langsung amankan slot.</p>
                    <h1 className="ksc-display">Delapan meja. Satu jadwal yang jelas.</h1>
                    <p className="ksc-subtitle mt-7">Main santai, latihan rutin, atau kompetisi kecil. Booking online tersedia untuk billiard, walk-in tetap dilayani selama meja kosong.</p>
                    <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Link href="/booking/billiard" className="ksc-button-primary">Booking meja billiard</Link>
                        <Link href="/booking/status" className="ksc-button-secondary">Cek booking</Link>
                    </div>
                </div>
                <div className="ksc-panel overflow-hidden p-2">
                    <div className="rounded-[1rem] bg-brand-700 p-6 text-white">
                        <p className="text-sm font-bold text-white/78">Operasional</p>
                        <p className="mt-3 text-4xl font-black tracking-[-0.035em]">09.00—24.00</p>
                        <div className="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                            <div className="rounded-2xl bg-white/12 p-4"><p className="text-sm text-white/78">Meja aktif</p><p className="mt-1 text-2xl font-black">8 meja</p></div>
                            <div className="rounded-2xl bg-white/12 p-4"><p className="text-sm text-white/78">Konfirmasi</p><p className="mt-1 text-2xl font-black">WhatsApp</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="mt-14 grid gap-px overflow-hidden rounded-3xl border bg-[var(--ksc-border)] sm:grid-cols-2">
                <div className="bg-white p-7 sm:p-9"><p className="text-sm font-semibold text-muted">Senin—Jumat</p><p className="mt-2 text-3xl font-black tracking-tight text-ink">{formatCurrency(prices.billiard_weekday || 0)}</p><p className="text-sm text-muted">per meja per jam</p></div>
                <div className="bg-white p-7 sm:p-9"><p className="text-sm font-semibold text-muted">Sabtu—Minggu</p><p className="mt-2 text-3xl font-black tracking-tight text-ink">{formatCurrency(prices.billiard_weekend || 0)}</p><p className="text-sm text-muted">per meja per jam</p></div>
            </div>
        </section>
    </PublicLayout>;
}
