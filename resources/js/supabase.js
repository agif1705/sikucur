import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'

document.addEventListener('livewire:init', () => {
  console.log('📡 Supabase realtime script dimulai2')

  // --- Ambil URL & KEY dari body dataset --- //
  const PROD = {
    url: document.body.dataset.supabaseProdUrl,
    key: document.body.dataset.supabaseProdKey,
    label: 'PROD',
  }
  const DEV = {
    url: document.body.dataset.supabaseDevUrl,
    key: document.body.dataset.supabaseDevKey,
    label: 'DEV',
  }

  const ENVS = [PROD, DEV]

  ENVS.forEach((env) => {
    const supabase = createClient(env.url, env.key)
    const channel = supabase.channel('realtime_iclock_transaction')

    // Event INSERT iclock_transaction
    channel.on(
      'postgres_changes',
      { event: 'INSERT', schema: 'public', table: 'iclock_transaction' },
      (e) => {
        console.log(`[${env.label}] 🔥 INSERT iclock_transaction`, e)
        Livewire.dispatch('fingerprint-updated', {
          env: env.label,
          mesin: e.new.terminal_sn,
          data: e.new,
        })
      }
    )

    // Event INSERT laravel_rekap_absensi_pegawais
    channel.on(
      'postgres_changes',
      { event: 'INSERT', schema: 'public', table: 'laravel_rekap_absensi_pegawais' },
      (e) => {
        console.log(`[${env.label}] 🔥 INSERT laravel_rekap_absensi_pegawais`, e)
        Livewire.dispatch('insertFromRekapAbsensi', {
          env: env.label,
          mesin: e.new.sn_mesin,
          data: e.new,
        })
      }
    )

    // Event DELETE iclock_transaction
    channel.on(
      'postgres_changes',
      { event: 'DELETE', schema: 'public', table: 'iclock_transaction' },
      (e) => {
        console.log(`[${env.label}] 🗑️ DELETE iclock_transaction`, e)
        Livewire.dispatch('fingerprint-deleted', {
          env: env.label,
          mesin: e.old.terminal_sn,
          data: e.old,
        })
      }
    )

    channel.subscribe((status) => console.log(`[${env.label}] 📶 Channel status:`, status))
  })
})
