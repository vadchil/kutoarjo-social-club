import React, { ReactNode, useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { PageProps } from '@/types';

const navigation = [
    { href: '/admin', label: 'Ringkasan', roles: ['admin', 'staff'] },
    { href: '/admin/bookings', label: 'Booking', roles: ['admin', 'staff'] },
    { href: '/admin/schedule', label: 'Jadwal meja', roles: ['admin', 'staff'] },
    { href: '/admin/gallery', label: 'Galeri', roles: ['admin'] },
    { href: '/admin/faqs', label: 'FAQ', roles: ['admin'] },
    { href: '/admin/settings', label: 'Pengaturan', roles: ['admin'] },
];

export default function AdminLayout({ children }: { children: ReactNode }) {
    const { auth, flash } = usePage<PageProps>().props;
    const { url } = usePage();
    const [open, setOpen] = useState(false);

    return <div className="ksc-admin-shell flex min-h-screen">
        <Head><meta name="robots" content="noindex,nofollow" /></Head>
        <aside className={`${open ? 'flex' : 'hidden'} fixed inset-y-0 left-0 z-40 w-72 flex-col border-r bg-white p-4 shadow-xl lg:static lg:flex lg:shadow-none`}>
            <div className="flex items-center justify-between px-2 py-2"><Link href="/admin" className="flex items-center gap-3 font-extrabold text-ink"><span className="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-700 text-white">KS</span><span>KSC Operations</span></Link><button onClick={() => setOpen(false)} className="inline-flex min-h-10 items-center justify-center rounded-lg px-2 text-sm font-bold text-muted hover:bg-[var(--ksc-surface-raised)] hover:text-ink lg:hidden">Tutup</button></div>
            <nav className="mt-7 space-y-1" aria-label="Navigasi admin">{navigation.filter((item) => auth.user && item.roles.includes(auth.user.role)).map((item) => <Link key={item.href} href={item.href} onClick={() => setOpen(false)} className={`ksc-nav-link ${url === item.href || (item.href !== '/admin' && url.startsWith(item.href)) ? 'ksc-nav-link-active' : ''}`}>{item.label}</Link>)}</nav>
            <div className="mt-auto border-t pt-4"><p className="px-2 text-sm font-semibold text-ink">{auth.user?.name}</p><p className="px-2 text-xs capitalize text-muted">{auth.user?.role}</p><Link href="/admin/logout" method="post" as="button" className="ksc-button-secondary mt-3 w-full">Keluar</Link></div>
        </aside>
        {open && <button aria-label="Tutup navigasi" onClick={() => setOpen(false)} className="fixed inset-0 z-30 bg-black/30 lg:hidden"/>}
        <div className="min-w-0 flex-1"><header className="flex h-16 items-center justify-between border-b bg-white px-4 lg:px-8"><button onClick={() => setOpen(true)} className="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--ksc-border)] bg-white px-3 text-sm font-bold text-ink lg:hidden">Menu</button><p className="hidden text-sm text-muted sm:block">Kutoarjo Social Club · Operasional</p><Link href="/" className="text-sm font-semibold text-brand-700">Lihat website</Link></header>
            <main className="ksc-admin-content">{flash?.success && <div role="status" className="mb-5 rounded-xl bg-[oklch(0.95_0.045_150)] px-4 py-3 text-sm font-semibold text-[oklch(0.36_0.12_150)]">{flash.success}</div>}{flash?.error && <div role="alert" className="mb-5 rounded-xl bg-[oklch(0.96_0.035_25)] px-4 py-3 text-sm font-semibold text-[oklch(0.43_0.16_25)]">{flash.error}</div>}{children}</main>
        </div>
    </div>;
}
