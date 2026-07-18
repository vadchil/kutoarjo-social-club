import React from 'react';
import { Head } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

export default function Contact({ settings }: { settings: Record<string, unknown> }) {
    const links = [
        ['WhatsApp', `https://wa.me/${String(settings.whatsapp_number || '')}`],
        ['Instagram', String(settings.instagram_url || '#')],
        ['Google Maps', String(settings.google_maps_url || '#')],
    ];

    return <PublicLayout settings={settings}><Head title="Kontak" />
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
