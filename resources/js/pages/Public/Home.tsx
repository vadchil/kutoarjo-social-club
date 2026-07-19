import React from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { formatCurrency } from '@/components/ui';

const jsonLd = (data: unknown) => JSON.stringify(data).replace(/</g, '\\u003c');

export default function Home({ settings, prices }: { settings: Record<string, unknown>; prices: Record<string, number> }) {
    const origin = typeof window === 'undefined' ? undefined : window.location.origin;
    const name = String(settings.business_name || 'Kutoarjo Social Club');

    return <PublicLayout settings={settings}><Head title="Kutoarjo Social Club">
        <meta name="description" content="Kutoarjo Social Club menyediakan lapangan padel dan meja billiard di Kutoarjo dengan booking mudah, harga transparan, dan suasana komunitas." />
        <script type="application/ld+json">{jsonLd({
            '@context': 'https://schema.org',
            '@type': 'SportsActivityLocation',
            name,
            description: String(settings.business_description || 'Lapangan padel dan meja billiard di Kutoarjo.'),
            url: origin,
            address: String(settings.business_address || ''),
            telephone: String(settings.whatsapp_number || ''),
            sameAs: [settings.instagram_url, settings.google_maps_url].filter(Boolean),
        })}</script>
    </Head>
        <section className="relative overflow-hidden border-b"><div className="ksc-container ksc-section ksc-rise grid items-end gap-12 lg:grid-cols-[1.45fr_.75fr]">
            <div><p className="mb-5 max-w-md text-sm font-bold leading-6 text-brand-700">Satu tempat untuk bergerak, bertanding, dan berkumpul di Kutoarjo.</p><h1 className="ksc-display">Main serius. Pulang jadi teman.</h1><p className="ksc-subtitle mt-7">{String(settings.business_description || 'Lapangan padel dan meja billiard dengan jadwal jelas, harga transparan, dan suasana yang membuat Anda ingin kembali.')}</p><div className="mt-9 flex flex-col gap-3 sm:flex-row"><Link href="/booking/billiard" className="ksc-button-primary">Booking meja billiard</Link><a href={String(settings.ayo_booking_url || '/padel')} className="ksc-button-secondary">Booking padel via AYO</a></div></div>
            <div className="relative min-h-80 overflow-hidden rounded-[2rem] bg-brand-700 p-7 text-white shadow-[0_28px_80px_oklch(0.36_0.147_340/.22)]"><div className="absolute -right-16 -top-16 h-56 w-56 rounded-full border-[2rem] border-white/10"/><div className="absolute -bottom-20 -left-12 h-64 w-64 rounded-full bg-signal/90"/><div className="relative flex h-full min-h-64 flex-col justify-between"><span className="w-fit rounded-full bg-white/15 px-3 py-1 text-xs font-bold">Buka setiap hari</span><div><p className="text-4xl font-black tracking-[-0.035em]">09.00—24.00</p><p className="mt-2 max-w-xs text-sm leading-6 text-white/80">Delapan meja billiard. Satu lapangan padel. Ruang untuk kompetisi maupun santai.</p></div></div></div>
        </div></section>
        <section className="ksc-container ksc-section"><div className="grid gap-px overflow-hidden rounded-3xl border bg-[var(--ksc-border)] md:grid-cols-2"><article className="bg-white p-7 sm:p-10"><p className="text-sm font-bold text-brand-700">Padel</p><h2 className="mt-3 text-3xl font-extrabold tracking-[-0.03em] text-ink">Satu court, energi penuh.</h2><p className="mt-4 max-w-lg leading-7 text-muted">Main weekday mulai {formatCurrency(prices.padel_weekday || 0)} per jam. Booking langsung melalui AYO.</p><Link href="/padel" className="mt-7 inline-flex font-bold text-brand-700 hover:text-brand-800">Lihat detail padel →</Link></article><article className="bg-white p-7 sm:p-10"><p className="text-sm font-bold text-brand-700">Billiard</p><h2 className="mt-3 text-3xl font-extrabold tracking-[-0.03em] text-ink">Pilih meja. Pilih jam.</h2><p className="mt-4 max-w-lg leading-7 text-muted">Delapan meja dengan harga weekday mulai {formatCurrency(prices.billiard_weekday || 0)} per meja per jam.</p><Link href="/billiard" className="mt-7 inline-flex font-bold text-brand-700 hover:text-brand-800">Lihat detail billiard →</Link></article></div></section>
        <section className="bg-brand-700 text-white"><div className="ksc-container flex flex-col gap-8 py-14 sm:flex-row sm:items-center sm:justify-between"><div><h2 className="text-3xl font-extrabold tracking-[-0.03em]">Meja favorit tidak menunggu.</h2><p className="mt-2 text-white/78">Cek jadwal, pilih meja, dan amankan slot dalam beberapa menit.</p></div><Link href="/booking/billiard" className="ksc-button-secondary border-white/30 bg-white text-brand-800">Mulai booking</Link></div></section>
    </PublicLayout>;
}
