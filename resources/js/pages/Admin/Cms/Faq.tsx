import React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import AdminLayout from '@/layouts/AdminLayout';
import { EmptyState, Field, PageHeader } from '@/components/ui';

interface FaqItem { id: string; question: string; answer: string; category: string; }

export default function Faq({ faqs }: { faqs: FaqItem[] }) {
    const form = useForm({ question: '', answer: '', category: 'general', sort_order: 0 });
    const submit = (e: React.FormEvent) => { e.preventDefault(); form.post('/admin/faqs', { onSuccess: () => form.reset() }); };

    return <AdminLayout><Head title="FAQ CMS" />
        <PageHeader title="FAQ" description="Kelola pertanyaan yang muncul di halaman publik." />
        <div className="grid gap-6 lg:grid-cols-[24rem_1fr]">
            <form onSubmit={submit} className="ksc-panel h-fit space-y-4 p-5 sm:p-6">
                <h2 className="text-xl font-black tracking-[-0.02em] text-ink">Tambah FAQ</h2>
                <Field id="question" label="Pertanyaan" error={form.errors.question} required><input id="question" value={form.data.question} onChange={(e) => form.setData('question', e.target.value)} className="ksc-input" required /></Field>
                <Field id="answer" label="Jawaban" error={form.errors.answer} required><textarea id="answer" value={form.data.answer} onChange={(e) => form.setData('answer', e.target.value)} className="ksc-input min-h-32" required /></Field>
                <Field id="category" label="Kategori"><select id="category" value={form.data.category} onChange={(e) => form.setData('category', e.target.value)} className="ksc-input"><option value="general">General</option><option value="padel">Padel</option><option value="billiard">Billiard</option></select></Field>
                <button type="submit" disabled={form.processing} className="ksc-button-primary w-full">{form.processing ? 'Menyimpan…' : 'Tambah FAQ'}</button>
            </form>
            <section className="space-y-3">{faqs.length ? faqs.map((faq) => <article key={faq.id} className="ksc-panel flex flex-col gap-4 p-5 sm:flex-row sm:items-start sm:justify-between"><div><p className="text-xs font-bold uppercase tracking-[0.04em] text-brand-700">{faq.category}</p><h3 className="mt-2 font-bold text-ink">{faq.question}</h3><p className="mt-2 max-w-2xl text-sm leading-6 text-muted">{faq.answer}</p></div><button type="button" onClick={() => router.delete(`/admin/faqs/${faq.id}`)} className="ksc-button-danger shrink-0">Hapus</button></article>) : <EmptyState title="FAQ belum ada" description="Tambahkan pertanyaan pertama agar halaman publik tidak kosong." />}</section>
        </div>
    </AdminLayout>;
}
