import React from 'react';
import { Head } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

const jsonLd = (data: unknown) => JSON.stringify(data).replace(/</g, '\\u003c');

export default function Contact({ settings }: { settings: Record<string, unknown> }) {
    const origin = typeof window === 'undefined' ? undefined : window.location.origin;
    const links = [
        ['WhatsApp', `https://wa.me/${String(settings.whatsapp_number || '')}`],
        ['Instagram', String(settings.instagram_url || '#')],
        ['Google Maps', String(settings.google_maps_url || '#')],
    ];

    return <PublicLayout settings={settings}><Head title="Kontak">
        <meta name="description" content="Hubungi Kutoarjo Social Club untuk info venue, jadwal padel dan billiard, pembayaran, alamat, WhatsApp, Instagram, dan Google Maps." />
        <script type="application/ld+json">{jsonLd({
            '@context': 'https://schema.org',
            '@type': 'SportsActivityLocation',
            name: String(settings.business_name || 'Kutoarjo Social Club'),
            url: origin,
            address: String(settings.business_address || ''),
            telephone: String(settings.whatsapp_number || ''),
            sameAs: [settings.instagram_url, settings.google_maps_url].filter(Boolean),
        })}</script>
    </Head>
        <section className="ksc-container ksc-page">
            <div className="grid gap-10 lg:grid-cols-[.9fr_1.1fr] lg:items-start">
                <div><p className="mb-4 font-bold text-brand-700">Kontak</p><h1 className="ksc-title">Datang, tanya slot, atau konfirmasi booking.</h1><p className="ksc-subtitle mt-5">Hubungi tim KSC untuk info venue, jadwal padel, billiard, dan pembayaran.</p></div>
                <div className="ksc-panel p-6 sm:p-8">
                    <dl className="space-y-6">
                        <div><dt className="text-sm font-bold text-muted">Alamat</dt><dd className="mt-2 leading-7 text-ink">{String(settings.business_address || 'Alamat Kutoarjo Social Club')}</dd></div>
                        <div><dt className="text-sm font-bold text-muted">Jam operasional</dt><dd className="mt-2 text-2xl font-black tracking-[-0.025em] text-ink">{String(settings.operational_hours || '09.00–24.00 WIB')}</dd></div>
                    </dl>
                    <div className="mt-8 flex flex-wrap gap-3">{links.map(([label, href]) => <a key={label} href={href} className={label === 'WhatsApp' ? 'ksc-button-primary' : 'ksc-button-secondary'}>{label}</a>)}</div>
                </div>
            </div>
        </section>
    </PublicLayout>;
}
