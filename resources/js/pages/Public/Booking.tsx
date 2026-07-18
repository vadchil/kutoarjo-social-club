import React, { useEffect, useMemo, useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';
import { Field, formatCurrency } from '@/components/ui';

interface Table { id: string; name: string; table_number: number; }
interface PaymentMethod { id: string; name: string; instructions: string; }
interface BookingProps { tables: Table[]; paymentMethods: PaymentMethod[]; settings: { timezone: string; maxDays: number; minDuration: number; maxDuration: number; }; }

export default function BookingForm({ tables, paymentMethods, settings }: BookingProps) {
    const [step, setStep] = useState(1);
    const [checking, setChecking] = useState(false);
    const [stepError, setStepError] = useState('');
    const [availability, setAvailability] = useState<{ available: boolean; message?: string; price?: { total_price: number; hourly_price: number } } | null>(null);

    const { data, setData, post, processing, errors } = useForm({
        billiard_table_id: '', payment_method_id: '', customer_name: '', customer_phone: '', booking_type: 'online', date: '', start_time: '', duration: String(settings.minDuration || 1), start_at: '', end_at: '', customer_notes: '',
    });

    const durations = useMemo(() => Array.from({ length: (settings.maxDuration || 4) - (settings.minDuration || 1) + 1 }, (_, i) => String((settings.minDuration || 1) + i)), [settings.maxDuration, settings.minDuration]);

    useEffect(() => {
        if (!data.date || !data.start_time || !data.duration) return;
        const start = new Date(`${data.date}T${data.start_time}:00`);
        const end = new Date(start.getTime() + Number(data.duration) * 60 * 60 * 1000);
        setData((prev) => ({ ...prev, start_at: `${data.date} ${data.start_time}:00`, end_at: `${data.date} ${String(end.getHours()).padStart(2, '0')}:${String(end.getMinutes()).padStart(2, '0')}:00` }));
    }, [data.date, data.start_time, data.duration]);

    useEffect(() => { setAvailability(null); setStepError(''); }, [data.billiard_table_id, data.date, data.start_time, data.duration]);

    const checkAvailability = async () => {
        if (!data.billiard_table_id || !data.start_at || !data.end_at) return;
        setChecking(true); setAvailability(null); setStepError('');
        try {
            const res = await fetch('/booking/billiard/check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '' },
                body: JSON.stringify({ billiard_table_id: data.billiard_table_id, start_at: data.start_at, end_at: data.end_at }),
            });
            const result = await res.json();
            setAvailability(res.ok ? { available: true, price: result.price } : { available: false, message: result.message || 'Slot tidak tersedia.' });
        } catch {
            setAvailability({ available: false, message: 'Koneksi gagal. Coba cek jadwal sekali lagi.' });
        } finally {
            setChecking(false);
        }
    };

    const nextStep = () => {
        if (step === 1 && !availability?.available) return setStepError('Cek ketersediaan meja dulu sebelum lanjut.');
        setStepError(''); setStep((prev) => prev + 1);
    };

    return <PublicLayout><Head title="Booking Billiard" />
        <section className="ksc-container ksc-page">
            <div className="mx-auto max-w-3xl">
                <p className="mb-4 font-bold text-brand-700">Booking billiard</p>
                <h1 className="ksc-title">Pilih meja dan amankan slot.</h1>
                <p className="ksc-subtitle mt-4">Booking online minimal {settings.minDuration} jam, maksimal {settings.maxDuration} jam. Konfirmasi pembayaran dilanjutkan lewat WhatsApp.</p>

                <div className="mt-8 grid gap-2 sm:grid-cols-3" aria-label="Progress booking">
                    {['Jadwal', 'Data diri', 'Pembayaran'].map((label, i) => <div key={label} className={`rounded-2xl border px-4 py-3 text-sm font-bold ${step === i + 1 ? 'border-brand-700 bg-brand-50 text-brand-700' : 'bg-white text-muted'}`}>{i + 1}. {label}</div>)}
                </div>

                <div className="ksc-panel mt-6 p-5 sm:p-7">
                    {step === 1 && <div className="space-y-5">
                        <h2 className="text-xl font-extrabold tracking-[-0.02em] text-ink">Jadwal bermain</h2>
                        <Field id="table" label="Meja" error={errors.billiard_table_id} required><select id="table" value={data.billiard_table_id} onChange={(e) => setData('billiard_table_id', e.target.value)} className="ksc-input"><option value="">Pilih meja</option>{tables.map((t) => <option key={t.id} value={t.id}>{t.name}</option>)}</select></Field>
                        <div className="grid gap-4 sm:grid-cols-3">
                            <Field id="date" label="Tanggal" error={errors.start_at} required><input id="date" type="date" value={data.date} onChange={(e) => setData('date', e.target.value)} className="ksc-input" /></Field>
                            <Field id="time" label="Jam mulai" required><input id="time" type="time" value={data.start_time} onChange={(e) => setData('start_time', e.target.value)} className="ksc-input" /></Field>
                            <Field id="duration" label="Durasi" required><select id="duration" value={data.duration} onChange={(e) => setData('duration', e.target.value)} className="ksc-input">{durations.map((hour) => <option key={hour} value={hour}>{hour} jam</option>)}</select></Field>
                        </div>
                        <button type="button" onClick={checkAvailability} disabled={checking || !data.billiard_table_id || !data.date || !data.start_time} className="ksc-button-primary w-full">{checking ? 'Mengecek jadwal…' : 'Cek ketersediaan'}</button>
                        {availability && <div role={availability.available ? 'status' : 'alert'} className={`rounded-2xl p-4 ${availability.available ? 'bg-[oklch(0.95_0.045_150)] text-[oklch(0.32_0.12_150)]' : 'bg-[oklch(0.96_0.035_25)] text-[oklch(0.43_0.16_25)]'}`}>{availability.available ? <><p className="font-bold">Slot tersedia.</p><p className="mt-1 text-sm">Total {formatCurrency(availability.price?.total_price || 0)}</p></> : <p className="font-semibold">{availability.message}</p>}</div>}
                    </div>}

                    {step === 2 && <div className="space-y-5">
                        <h2 className="text-xl font-extrabold tracking-[-0.02em] text-ink">Data pemesan</h2>
                        <Field id="name" label="Nama" error={errors.customer_name} required><input id="name" value={data.customer_name} onChange={(e) => setData('customer_name', e.target.value)} className="ksc-input" autoComplete="name" /></Field>
                        <Field id="phone" label="Nomor WhatsApp" error={errors.customer_phone} hint="Gunakan format 628123456789." required><input id="phone" value={data.customer_phone} onChange={(e) => setData('customer_phone', e.target.value)} className="ksc-input" inputMode="tel" autoComplete="tel" placeholder="628123456789" /></Field>
                        <Field id="notes" label="Catatan"><textarea id="notes" value={data.customer_notes} onChange={(e) => setData('customer_notes', e.target.value)} className="ksc-input min-h-28" placeholder="Opsional" /></Field>
                    </div>}

                    {step === 3 && <div className="space-y-5">
                        <h2 className="text-xl font-extrabold tracking-[-0.02em] text-ink">Pembayaran</h2>
                        <Field id="payment" label="Metode pembayaran" error={errors.payment_method_id} required><select id="payment" value={data.payment_method_id} onChange={(e) => setData('payment_method_id', e.target.value)} className="ksc-input" required><option value="">Pilih metode pembayaran</option>{paymentMethods.map((pm) => <option key={pm.id} value={pm.id}>{pm.name}</option>)}</select></Field>
                        <div className="ksc-panel-subtle p-4 text-sm leading-6"><p className="font-bold text-ink">Ringkasan booking</p><dl className="mt-3 grid gap-2 sm:grid-cols-2"><dt className="text-muted">Nama</dt><dd className="font-semibold text-ink">{data.customer_name}</dd><dt className="text-muted">Jadwal</dt><dd className="font-semibold text-ink">{data.date} · {data.start_time} WIB</dd><dt className="text-muted">Durasi</dt><dd className="font-semibold text-ink">{data.duration} jam</dd><dt className="text-muted">Total</dt><dd className="font-black text-ink">{formatCurrency(availability?.price?.total_price || 0)}</dd></dl></div>
                    </div>}

                    {stepError && <p className="ksc-error mt-4" role="alert">{stepError}</p>}
                    <div className="mt-7 flex justify-between gap-3 border-t pt-5">
                        <button type="button" onClick={() => setStep((prev) => prev - 1)} disabled={step === 1 || processing} className="ksc-button-secondary">Kembali</button>
                        {step < 3 ? <button type="button" onClick={nextStep} className="ksc-button-primary">Lanjut</button> : <button type="button" onClick={() => post('/booking/billiard')} disabled={processing} className="ksc-button-primary">{processing ? 'Menyimpan…' : 'Buat booking'}</button>}
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>;
}
