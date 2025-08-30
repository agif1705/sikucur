import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'
document.addEventListener('livewire:init', () => {
  console.log('📡 Supabase realtime script dimulai')

  const supabase = createClient(
    'https://realtime.sikucur.com',
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyb2xlIjoiYW5vbiIsImlzcyI6InN1cGFiYXNlIiwiaWF0IjoxNzU1NjIyODAwLCJleHAiOjE5MTMzODkyMDB9.mDUDIN37Evdb7-Rf-0DzN_JT2smCnnxR9EAyRyJZiF8'
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
      console.log('🔥 Realtime iclock_transaction payload:', e)
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
      console.log('🔥 laravel_rekap_absensi:', e)
      const dataBaru = e.new
      Livewire.dispatch('rekap-absensi-updated', {
        mesin: dataBaru,
        data: dataBaru,
      })
    }
  )
  channel.on(
    'postgres_changes_rekap',
    {
      event: 'DELETE',
      schema: 'public',
      table: 'laravel_rekap_absensi_pegawais',
    },
    (e) => {
      console.log('🔥 laravel_rekap_absensi:', e)
      const dataBaru = e.new
      Livewire.dispatch('rekap-absensi-updated', {
        mesin: dataBaru,
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
