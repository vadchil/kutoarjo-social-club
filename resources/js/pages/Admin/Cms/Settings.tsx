import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/AdminLayout';
import { Field, PageHeader } from '@/components/ui';

interface Setting { id: string; key: string; value: { value: unknown }; }

const labels: Record<string, string> = {
    business_name: 'Nama bisnis', business_description: 'Deskripsi bisnis', business_address: 'Alamat', operational_hours: 'Jam operasional', whatsapp_number: 'Nomor WhatsApp', instagram_url: 'URL Instagram', ayo_booking_url: 'URL booking AYO', google_maps_url: 'URL Google Maps', timezone: 'Timezone', maximum_booking_days: 'Maksimal hari booking', minimum_booking_duration: 'Durasi minimal booking', maximum_booking_duration: 'Durasi maksimal booking', booking_expiry_minutes: 'Batas bayar online (menit)',
};

export default function Settings({ settings }: { settings: Setting[] }) {
    const initial = Object.fromEntries(settings.map((setting) => [setting.key, String(setting.value?.value ?? '')]));
    const form = useForm({ settings: initial });
    const submit = (e: React.FormEvent) => { e.preventDefault(); form.patch('/admin/settings'); };

    return <AdminLayout><Head title="Pengaturan" />
        <PageHeader title="Pengaturan website" description="Kontrol informasi publik, link eksternal, dan aturan booking dasar." />
        <form onSubmit={submit} className="ksc-panel p-5 sm:p-6">
            <div className="grid gap-5 md:grid-cols-2">{Object.entries(form.data.settings).map(([key, value]) => <Field key={key} id={key} label={labels[key] || key.replaceAll('_', ' ')}><input id={key} value={String(value)} onChange={(e) => form.setData('settings', { ...form.data.settings, [key]: e.target.value })} className="ksc-input" /></Field>)}</div>
            <div className="mt-7 border-t pt-5"><button type="submit" disabled={form.processing} className="ksc-button-primary">{form.processing ? 'Menyimpan…' : 'Simpan pengaturan'}</button></div>
        </form>
    </AdminLayout>;
}
