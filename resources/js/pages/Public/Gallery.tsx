import React from 'react';
import { Head } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { EmptyState } from '@/components/ui';

interface Item { id: string; title: string; category: string; image_path: string; alt_text: string; }

export default function Gallery({ settings, items }: { settings: Record<string, unknown>; items: Item[] }) {
    return <PublicLayout settings={settings}><Head title="Galeri" />
        <section className="ksc-container ksc-page">
            <div className="mb-9 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div><p className="mb-4 font-bold text-brand-700">Galeri</p><h1 className="ksc-title">Suasana club, sebelum Anda datang.</h1></div>
                <p className="max-w-md leading-7 text-muted">Lihat venue, aktivitas, dan momen komunitas dari Kutoarjo Social Club.</p>
            </div>
            {items.length ? <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                {items.map((item) => <figure key={item.id} className="ksc-panel overflow-hidden">
                    <img src={`/storage/${item.image_path}`} alt={item.alt_text || item.title} className="aspect-video w-full object-cover" loading="lazy" />
                    <figcaption className="p-5"><strong className="text-ink">{item.title}</strong><p className="mt-1 text-sm capitalize text-muted">{item.category}</p></figcaption>
                </figure>)}
            </div> : <EmptyState title="Galeri segera hadir" description="Foto venue dan aktivitas akan tampil setelah admin mengunggah konten." />}
        </section>
    </PublicLayout>;
}
