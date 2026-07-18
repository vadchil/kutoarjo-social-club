import React, { ReactNode, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

interface Props { children: ReactNode; settings?: Record<string, unknown>; }

const links = [['/', 'Beranda'], ['/padel', 'Padel'], ['/billiard', 'Billiard'], ['/gallery', 'Galeri'], ['/faq', 'FAQ'], ['/contact', 'Kontak']];

export default function PublicLayout({ children, settings = {} }: Props) {
    const [open, setOpen] = useState(false);
    const { url } = usePage();
    const name = String(settings.business_name || 'Kutoarjo Social Club');

    return <div className="ksc-public-shell">
        <a href="#main-content" className="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-3">Lewati ke konten</a>
        <header className="sticky top-0 z-30 border-b bg-white/95 shadow-[0_1px_0_oklch(0.88_0.014_340)] backdrop-blur-sm">
            <nav className="ksc-container flex min-h-16 items-center justify-between gap-4" aria-label="Navigasi utama">
                <Link href="/" className="flex items-center gap-3 font-extrabold tracking-[-0.025em] text-ink"><span className="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-700 text-sm font-black text-white">KS</span><span className="hidden sm:inline">{name}</span></Link>
                <button type="button" onClick={() => setOpen(v => !v)} aria-expanded={open} aria-controls="public-nav" className="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--ksc-border)] bg-white px-3 text-sm font-bold text-ink md:hidden"><span className="sr-only">Buka navigasi</span>{open ? 'Tutup' : 'Menu'}</button>
                <div id="public-nav" className={`${open ? 'flex' : 'hidden'} absolute inset-x-0 top-16 flex-col gap-1 border-b bg-white p-3 shadow-lg md:static md:flex md:flex-row md:items-center md:border-0 md:bg-transparent md:p-0 md:shadow-none`}>
                    {links.map(([href,label]) => <Link key={href} href={href} onClick={() => setOpen(false)} className={`min-h-11 rounded-lg px-3 py-3 text-sm font-semibold transition-colors md:min-h-0 md:py-2 ${url === href ? 'bg-brand-50 text-brand-700' : 'text-muted hover:bg-brand-50 hover:text-ink'}`}>{label}</Link>)}
                    <Link href="/booking/status" onClick={() => setOpen(false)} className="min-h-11 rounded-lg px-3 py-3 text-sm font-semibold text-muted hover:bg-brand-50 hover:text-ink md:min-h-0 md:py-2">Cek booking</Link>
                    <Link href="/booking/billiard" onClick={() => setOpen(false)} className="ksc-button-primary ml-0 md:ml-2">Booking billiard</Link>
                </div>
            </nav>
        </header>
        <main id="main-content">{children}</main>
        <footer className="border-t bg-white">
            <div className="ksc-container flex flex-col gap-5 py-8 text-sm text-muted sm:flex-row sm:items-center sm:justify-between"><div><p className="font-bold text-ink">{name}</p><p className="mt-1">{String(settings.operational_hours || '09.00–24.00 WIB')}</p></div><div className="flex flex-wrap gap-4"><Link href="/contact" className="hover:text-brand-700">Kontak</Link><Link href="/faq" className="hover:text-brand-700">FAQ</Link><Link href="/admin/login" className="hover:text-brand-700">Staff</Link></div></div>
        </footer>
    </div>;
}
