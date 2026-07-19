import React from 'react';
import { Head } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { EmptyState } from '@/components/ui';

interface FaqItem { id: string; question: string; answer: string; category: string; }

const jsonLd = (data: unknown) => JSON.stringify(data).replace(/</g, '\\u003c');

export default function FAQ({ settings, faqs }: { settings: Record<string, unknown>; faqs: FaqItem[] }) {
    return <PublicLayout settings={settings}><Head title="FAQ">
        <meta name="description" content="Jawaban pertanyaan umum tentang booking, jadwal, fasilitas, padel, billiard, dan pembayaran di Kutoarjo Social Club." />
        {faqs.length > 0 && <script type="application/ld+json">{jsonLd({
            '@context': 'https://schema.org',
            '@type': 'FAQPage',
            mainEntity: faqs.map((faq) => ({
                '@type': 'Question',
                name: faq.question,
                acceptedAnswer: { '@type': 'Answer', text: faq.answer },
            })),
        })}</script>}
    </Head>
        <section className="ksc-container ksc-page">
            <div className="mx-auto max-w-3xl">
                <p className="mb-4 font-bold text-brand-700">FAQ</p>
                <h1 className="ksc-title">Pertanyaan sebelum datang.</h1>
                <p className="ksc-subtitle mt-5">Jawaban singkat untuk booking, jadwal, fasilitas, dan pembayaran di Kutoarjo Social Club.</p>
                <div className="mt-9 space-y-3">
                    {faqs.length ? faqs.map((faq) => <details key={faq.id} className="ksc-panel-subtle group p-5 open:bg-white">
                        <summary className="cursor-pointer list-none font-bold text-ink marker:hidden"><span className="flex items-center justify-between gap-4">{faq.question}<span className="text-brand-700 transition-transform group-open:rotate-45">+</span></span></summary>
                        <p className="mt-4 max-w-2xl leading-7 text-muted">{faq.answer}</p>
                    </details>) : <EmptyState title="FAQ belum tersedia" description="Pertanyaan umum akan muncul di sini setelah ditambahkan oleh admin." />}
                </div>
            </div>
        </section>
    </PublicLayout>;
}
