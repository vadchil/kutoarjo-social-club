import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { Field } from '@/components/ui';

export default function BookingLookup({ error }: { error?: string }) {
    const { data, setData, post, processing, errors } = useForm({ booking_code: '', customer_phone: '' });
    const submit = (e: React.FormEvent) => { e.preventDefault(); post('/booking/status'); };

    return <PublicLayout><Head title="Cek Status Booking">
        <meta name="robots" content="noindex,follow" />
        <meta name="description" content="Cek status booking Kutoarjo Social Club menggunakan kode booking dan nomor WhatsApp pemesan." />
    </Head>
        <section className="ksc-container ksc-page">
            <div className="mx-auto max-w-xl">
                <p className="mb-4 font-bold text-brand-700">Status booking</p>
                <h1 className="ksc-title">Cek detail reservasi.</h1>
                <p className="ksc-subtitle mt-5">Masukkan kode booking dan nomor WhatsApp yang dipakai saat reservasi.</p>
                <form onSubmit={submit} className="ksc-panel mt-8 space-y-5 p-6 sm:p-8">
                    {error && <div role="alert" className="rounded-2xl bg-[oklch(0.96_0.035_25)] p-4 text-sm font-semibold text-[oklch(0.43_0.16_25)]">{error}</div>}
                    <Field id="booking_code" label="Kode booking" error={errors.booking_code} required><input id="booking_code" value={data.booking_code} onChange={(e) => setData('booking_code', e.target.value.toUpperCase())} className="ksc-input font-mono" placeholder="KSC-BL-260718-A7K2" required /></Field>
                    <Field id="customer_phone" label="Nomor WhatsApp" error={errors.customer_phone} required><input id="customer_phone" value={data.customer_phone} onChange={(e) => setData('customer_phone', e.target.value)} className="ksc-input" inputMode="tel" placeholder="628123456789" required /></Field>
                    <button type="submit" disabled={processing} className="ksc-button-primary w-full">{processing ? 'Mencari…' : 'Cek booking'}</button>
                </form>
            </div>
        </section>
    </PublicLayout>;
}
