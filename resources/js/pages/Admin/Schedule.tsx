import React, { useState } from 'react';
import AdminLayout from '@/layouts/AdminLayout';
import { Head, router } from '@inertiajs/react';
import { EmptyState, Field, PageHeader, StatusBadge } from '@/components/ui';

interface Table { id: string; name: string; table_number: number; }
interface Booking { id: string; booking_code: string; billiard_table_id: string; customer_name: string; start_time: string; end_time: string; status: string; }
interface ScheduleProps { tables: Table[]; bookings: Booking[]; selectedDate: string; }

export default function Schedule({ tables, bookings, selectedDate }: ScheduleProps) {
    const [date, setDate] = useState(selectedDate);
    const [walkInTable, setWalkInTable] = useState('');
    const [duration, setDuration] = useState('1');
    const handleDateChange = (newDate: string) => { setDate(newDate); router.get('/admin/schedule', { date: newDate }, { preserveState: true }); };
    const startWalkIn = (e: React.FormEvent) => { e.preventDefault(); router.post('/admin/walk-in', { billiard_table_id: walkInTable, duration: Number(duration) }, { onSuccess: () => setWalkInTable('') }); };

    return <AdminLayout><Head title="Jadwal Meja" />
        <PageHeader title="Jadwal meja" description="Pantau slot harian per meja dan mulai sesi walk-in dari halaman yang sama." actions={<input type="date" value={date} onChange={(e) => handleDateChange(e.target.value)} className="ksc-input w-auto" aria-label="Tanggal jadwal" />} />
        <div className="grid gap-6 lg:grid-cols-[1fr_22rem]">
            <section className="ksc-panel p-5 sm:p-6">
                <div className="space-y-3">{tables.map((table) => {
                    const tableBookings = bookings.filter((booking) => booking.billiard_table_id === table.id);
                    return <div key={table.id} className="grid gap-3 rounded-2xl border p-4 sm:grid-cols-[9rem_1fr] sm:items-start">
                        <div><p className="font-black text-ink">{table.name}</p><p className="text-xs text-muted">Meja {table.table_number}</p></div>
                        <div className="flex flex-wrap gap-2">{tableBookings.length ? tableBookings.map((booking) => <div key={booking.id} className="rounded-xl border bg-white px-3 py-2"><p className="font-mono text-xs font-black text-ink">{booking.start_time}—{booking.end_time}</p><p className="mt-1 text-xs text-muted">{booking.customer_name}</p><div className="mt-2"><StatusBadge status={booking.status} /></div></div>) : <p className="rounded-xl bg-[var(--ksc-surface-raised)] px-3 py-2 text-sm text-muted">Kosong hari ini</p>}</div>
                    </div>;
                })}</div>
                {!tables.length && <EmptyState title="Meja belum tersedia" description="Tambahkan data meja aktif sebelum membuat jadwal." />}
            </section>
            <aside className="ksc-panel h-fit p-5 sm:p-6">
                <h2 className="text-xl font-black tracking-[-0.02em] text-ink">Walk-in</h2><p className="mt-2 text-sm leading-6 text-muted">Mulai sesi langsung untuk tamu yang datang tanpa booking online.</p>
                <form onSubmit={startWalkIn} className="mt-5 space-y-4">
                    <Field id="walk_in_table" label="Meja" required><select id="walk_in_table" value={walkInTable} onChange={(e) => setWalkInTable(e.target.value)} className="ksc-input" required><option value="">Pilih meja</option>{tables.map((table) => <option key={table.id} value={table.id}>{table.name}</option>)}</select></Field>
                    <Field id="walk_in_duration" label="Durasi" required><select id="walk_in_duration" value={duration} onChange={(e) => setDuration(e.target.value)} className="ksc-input"><option value="1">1 jam</option><option value="2">2 jam</option><option value="3">3 jam</option><option value="4">4 jam</option></select></Field>
                    <button type="submit" className="ksc-button-primary w-full">Mulai sesi</button>
                </form>
            </aside>
        </div>
    </AdminLayout>;
}
