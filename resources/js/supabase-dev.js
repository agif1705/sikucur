import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'
document.addEventListener('livewire:init', () => {
  console.log('📡 Supabase realtime script dimulai')

  const supabase = createClient(
    'https://supabase.sikucur.com',
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyb2xlIjoiYW5vbiIsImlzcyI6InN1cGFiYXNlIiwiaWF0IjoxNzg3NjMyNDIwLCJleHAiOjE5NDUzMTI0MjB9.qK04ZnpMgRGgaszqWeWqGBKssU6qPOotoXFQZMr-oLk'
  )

  const channel = supabase.channel('realtime_iclock_transaction')

  channel.on(
    'postgres_changes',
    {
      event: 'INSERT',
      schema: 'public',
      table: 'iclock_transaction',
    },
    (e) => {
      console.log('🔥 Realtime payload:', e)
      const dataBaru = e.new
      Livewire.dispatch('fingerprint-updated', {
        mesin: dataBaru.terminal_sn,
        data: dataBaru,
      })
    }
  )
  channel.on(
    'postgres_changes',
    {
      event: 'INSERT',
      schema: 'public',
      table: 'laravel_rekap_absensi_pegawais',
    },
    (e) => {
      console.log('🔥 Realtime payload:', e)
      const dataBaru = e.new
      Livewire.dispatch('insertFromRekapAbsensi', {
        mesin: dataBaru.sn_mesin,
        data: dataBaru,
      })
    }
  )

  channel.on(
    'postgres_changes',
    {
      event: 'DELETE',
      schema: 'public',
      table: 'iclock_transaction',
    },
    (e) => {
      console.log('🗑️ Data DELETE:', e)
      Livewire.dispatch('fingerprint-deleted', {
        mesin: e.old,
        data: e.old,
      })
    }
  )

  channel.subscribe((status) => {
    console.log('📶 Channel status:', status)
  })
})
