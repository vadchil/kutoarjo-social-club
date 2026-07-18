import React from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import { PageHeader } from '@/components/ui';

export default function Dashboard() {
    return <AdminLayout><Head title="Dashboard" />
        <PageHeader title="Ringkasan operasional" description="Pantau booking, jadwal meja, dan konten publik dari satu tempat." actions={<Link href="/admin/bookings" className="ksc-button-primary">Kelola booking</Link>} />
        <div className="grid gap-4 md:grid-cols-3">
            <section className="ksc-panel p-6"><p className="text-sm font-bold text-muted">Booking</p><h2 className="mt-3 text-2xl font-black tracking-[-0.025em] text-ink">Jadwal hari ini</h2><p className="mt-2 text-sm leading-6 text-muted">Lihat slot aktif, mulai sesi, dan selesaikan permainan.</p><Link href="/admin/schedule" className="mt-5 inline-flex font-bold text-brand-700">Buka jadwal →</Link></section>
            <section className="ksc-panel p-6"><p className="text-sm font-bold text-muted">Walk-in</p><h2 className="mt-3 text-2xl font-black tracking-[-0.025em] text-ink">Mulai cepat</h2><p className="mt-2 text-sm leading-6 text-muted">Catat sesi langsung untuk tamu yang datang tanpa booking online.</p><Link href="/admin/schedule" className="mt-5 inline-flex font-bold text-brand-700">Mulai walk-in →</Link></section>
            <section className="ksc-panel p-6"><p className="text-sm font-bold text-muted">Website</p><h2 className="mt-3 text-2xl font-black tracking-[-0.025em] text-ink">Konten publik</h2><p className="mt-2 text-sm leading-6 text-muted">Perbarui galeri, FAQ, dan informasi bisnis yang tampil untuk customer.</p><Link href="/admin/settings" className="mt-5 inline-flex font-bold text-brand-700">Buka CMS →</Link></section>
        </div>
    </AdminLayout>;
}
