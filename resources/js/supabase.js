import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'
document.addEventListener('livewire:init', () => {
  console.log('📡 Supabase realtime script dimulai')

  const supabase = createClient(
    'https://realtime.baduo.cloud',
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyAgCiAgICAicm9sZSI6ICJhbm9uIiwKICAgICJpc3MiOiAic3VwYWJhc2UtZGVtbyIsCiAgICAiaWF0IjogMTY0MTc2OTIwMCwKICAgICJleHAiOiAxNzk5NTM1NjAwCn0.dc_X5iR_VP_qT0zsiyj_I_OZ2T9FtRU2BBNWN8Bu4GE'
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
