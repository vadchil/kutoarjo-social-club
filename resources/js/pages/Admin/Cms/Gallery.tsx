import React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/AdminLayout';
import { EmptyState, Field, PageHeader } from '@/components/ui';

interface Item { id: string; title: string; category: string; image_path: string; alt_text: string; sort_order: number; is_published: boolean; }

export default function Gallery({ items }: { items: Item[] }) {
    const form = useForm({ title: '', category: 'venue', alt_text: '', sort_order: 0, image: null as File | null });
    const submit = (e: React.FormEvent) => { e.preventDefault(); form.post('/admin/gallery', { forceFormData: true, onSuccess: () => form.reset() }); };

    return <AdminLayout><Head title="Gallery CMS" />
        <PageHeader title="Galeri" description="Unggah foto venue, padel, billiard, dan suasana komunitas untuk halaman publik." />
        <div className="grid gap-6 lg:grid-cols-[24rem_1fr]">
            <form onSubmit={submit} className="ksc-panel h-fit space-y-4 p-5 sm:p-6">
                <h2 className="text-xl font-black tracking-[-0.02em] text-ink">Tambah foto</h2>
                <Field id="title" label="Judul" error={form.errors.title} required><input id="title" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} className="ksc-input" required /></Field>
                <Field id="category" label="Kategori"><select id="category" value={form.data.category} onChange={(e) => form.setData('category', e.target.value)} className="ksc-input"><option value="venue">Venue</option><option value="padel">Padel</option><option value="billiard">Billiard</option></select></Field>
                <Field id="alt_text" label="Alt text" error={form.errors.alt_text} hint="Jelaskan isi foto untuk aksesibilitas." required><input id="alt_text" value={form.data.alt_text} onChange={(e) => form.setData('alt_text', e.target.value)} className="ksc-input" required /></Field>
                <Field id="image" label="File gambar" error={form.errors.image} required><input id="image" type="file" accept="image/jpeg,image/png,image/webp" onChange={(e) => form.setData('image', e.target.files?.[0] || null)} className="ksc-input file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:font-bold file:text-brand-700" required /></Field>
                <button type="submit" disabled={form.processing} className="ksc-button-primary w-full">{form.processing ? 'Mengunggah…' : 'Upload foto'}</button>
            </form>
            <section>{items.length ? <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">{items.map((item) => <article key={item.id} className="ksc-panel overflow-hidden"><img src={`/storage/${item.image_path}`} alt={item.alt_text || item.title} className="aspect-video w-full object-cover" loading="lazy" /><div className="p-4"><p className="text-xs font-bold uppercase tracking-[0.04em] text-brand-700">{item.category}</p><h3 className="mt-2 font-bold text-ink">{item.title}</h3><p className="mt-1 text-sm text-muted">{item.is_published ? 'Published' : 'Draft'}</p><button type="button" onClick={() => router.delete(`/admin/gallery/${item.id}`)} className="ksc-button-danger mt-4 w-full">Hapus</button></div></article>)}</div> : <EmptyState title="Belum ada foto" description="Upload foto pertama untuk mengisi galeri publik." />}</section>
        </div>
    </AdminLayout>;
}
