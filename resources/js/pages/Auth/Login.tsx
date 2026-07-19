import React, { FormEvent } from 'react';
import { useForm, Head, Link } from '@inertiajs/react';
import { Field } from '@/components/ui';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({ email: '', password: '', remember: false });
    const submit = (e: FormEvent) => { e.preventDefault(); post('/admin/login'); };

    return <main className="flex min-h-screen items-center justify-center bg-[var(--ksc-bg)] px-4 py-12">
        <Head title="Login Admin">
            <meta name="robots" content="noindex,nofollow" />
        </Head>
        <div className="w-full max-w-md">
            <Link href="/" className="mb-6 flex items-center justify-center gap-3 font-extrabold tracking-[-0.025em] text-ink"><span className="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-700 text-sm font-black text-white">KS</span>KSC Operations</Link>
            <form onSubmit={submit} className="ksc-panel space-y-5 p-6 sm:p-8">
                <div><h1 className="text-2xl font-black tracking-[-0.025em] text-ink">Masuk admin</h1><p className="mt-2 text-sm leading-6 text-muted">Gunakan akun staff atau admin untuk mengelola booking dan konten.</p></div>
                <Field id="email" label="Email" error={errors.email} required><input id="email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className="ksc-input" autoComplete="email" required /></Field>
                <Field id="password" label="Password" error={errors.password} required><input id="password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} className="ksc-input" autoComplete="current-password" required /></Field>
                <label className="flex min-h-11 items-center gap-3 text-sm font-semibold text-ink"><input type="checkbox" checked={data.remember} onChange={(e) => setData('remember', e.target.checked)} className="h-4 w-4 rounded border-[var(--ksc-border)] text-brand-700 focus:ring-brand-700" />Ingat saya</label>
                <button type="submit" disabled={processing} className="ksc-button-primary w-full">{processing ? 'Memproses…' : 'Masuk'}</button>
            </form>
        </div>
    </main>;
}
